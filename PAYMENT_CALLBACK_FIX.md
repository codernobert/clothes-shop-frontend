# Payment Callback URL Fix - Documentation

## 🐛 Problem Identified

**Issue:** Payment flow was ending at `webhook.site` instead of redirecting to the payment callback page.

**Root Cause:** The backend was using a hardcoded callback URL from `application.properties` instead of using the callback URL passed by the frontend in the payment request.

---

## ✅ Solution Applied

### 1. Backend Changes

#### Modified: `PaymentIntentRequest.java`
**Added field:**
```java
private String callbackUrl; // URL to redirect after payment (optional, falls back to configured URL)
```

#### Modified: `PaymentService.java`
**Updated logic to use callback URL from request:**
```java
// Use callback URL from request if provided, otherwise use the configured one
String effectiveCallbackUrl = (request.getCallbackUrl() != null && !request.getCallbackUrl().isEmpty()) 
    ? request.getCallbackUrl() 
    : callbackUrl;

log.info("Using callback URL: {}", effectiveCallbackUrl);

// Build Paystack request with the effective callback URL
PaystackInitializeRequest paystackRequest = PaystackInitializeRequest.builder()
    // ...other fields...
    .callbackUrl(effectiveCallbackUrl)
    .build();
```

**Before:**
```java
// Always used hardcoded URL from properties
.callbackUrl(callbackUrl)
```

**After:**
```java
// Uses URL from request, falls back to properties if not provided
.callbackUrl(effectiveCallbackUrl)
```

### 2. Frontend (No Changes Required)

The frontend was already correctly passing the callback URL:

```javascript
const callbackUrl = window.location.origin + window.location.pathname.replace('checkout.php', 'payment_callback.php');

fetch('ajax/create_payment.php', {
    body: JSON.stringify({
        amount: amount,
        currency: 'KES',
        description: 'Order #' + orderNumber,
        email: document.getElementById('email').value,
        callbackUrl: callbackUrl  // ✅ This was being ignored before
    })
});
```

---

## 🔄 How It Works Now

### Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│ Frontend (checkout.php)                                      │
│                                                              │
│ 1. Construct callback URL:                                  │
│    http://localhost:8000/payment_callback.php               │
│                                                              │
│ 2. Send to backend in payment request ✅ NEW!               │
└────────────────────────┬────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────┐
│ Backend (PaymentService.java)                               │
│                                                              │
│ 3. Check if callbackUrl is in request ✅ NEW!               │
│    YES: Use request.getCallbackUrl()                        │
│    NO:  Use configured paystack.callback.url                │
│                                                              │
│ 4. Send to Paystack with correct callback URL               │
└────────────────────────┬────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────┐
│ Paystack                                                     │
│                                                              │
│ 5. User completes payment                                   │
│                                                              │
│ 6. Redirect to: http://localhost:8000/payment_callback.php  │
│    ?reference=ORDER-xxx ✅ CORRECT!                         │
└────────────────────────┬────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────┐
│ Frontend (payment_callback.php)                             │
│                                                              │
│ 7. Verify payment                                           │
│ 8. Confirm payment                                          │
│ 9. Show success page ✅                                     │
└─────────────────────────────────────────────────────────────┘
```

---

## 🧪 Testing

### Before the Fix ❌
```
1. User completes checkout
2. Redirected to Paystack ✅
3. Completes payment ✅
4. Redirected to: https://webhook.site/820ddc03... ❌
5. Flow ends, no verification or confirmation ❌
```

### After the Fix ✅
```
1. User completes checkout
2. Redirected to Paystack ✅
3. Completes payment ✅
4. Redirected to: http://localhost:8000/payment_callback.php?reference=ORDER-xxx ✅
5. Payment verified ✅
6. Payment confirmed ✅
7. Order updated to CONFIRMED ✅
8. Success page shown ✅
```

---

## 📋 Files Modified

### Backend Files
1. ✏️ `src/main/java/com/ecommerce/clothesshop/dto/PaymentIntentRequest.java`
   - Added `callbackUrl` field

2. ✏️ `src/main/java/com/ecommerce/clothesshop/service/PaymentService.java`
   - Updated `initializePayment()` to use callback URL from request
   - Added fallback to configured URL if not provided
   - Added logging for callback URL

3. 📝 `src/main/resources/application.properties`
   - Added comment explaining callback URL can be overridden

### Frontend Files
- ✅ No changes needed (already correct)

### Documentation Files Created
1. 📄 `PAYMENT_CALLBACK_FIX.md` - This file

---

## 🔍 Technical Details

### Why Was webhook.site Being Used?

The webhook.site URL was likely being used during testing or development. The issue was that the backend was ignoring the frontend's callback URL and using a hardcoded value instead.

### Priority Order for Callback URL

```java
if (request.getCallbackUrl() != null && !request.getCallbackUrl().isEmpty()) {
    // Use URL from request (HIGHEST PRIORITY)
    effectiveCallbackUrl = request.getCallbackUrl();
} else {
    // Use URL from application.properties (FALLBACK)
    effectiveCallbackUrl = callbackUrl;
}
```

This allows:
1. **Frontend control**: Frontend can specify callback URL dynamically
2. **Backend fallback**: If frontend doesn't provide URL, use configured default
3. **Flexibility**: Different environments can use different callback URLs

---

## 🚀 Deployment Notes

### Local Development
```properties
paystack.callback.url=http://localhost:8000/payment_callback.php
```

### Production
```properties
paystack.callback.url=https://yourdomain.com/payment_callback.php
```

**Note:** Frontend will override this with the correct URL based on `window.location.origin`

---

## ✅ Verification Steps

### 1. Start Backend
```bash
./mvnw spring-boot:run
```

### 2. Check Logs for Callback URL
When payment is initialized, you should see:
```
INFO: Using callback URL: http://localhost:8000/payment_callback.php
```

### 3. Test Complete Flow
1. Add items to cart
2. Go to checkout
3. Complete checkout form
4. Click "Complete Order"
5. **Expected:** Redirected to Paystack
6. Complete payment on Paystack
7. **Expected:** Redirected to `http://localhost:8000/payment_callback.php?reference=ORDER-xxx`
8. **Expected:** Payment verified automatically
9. **Expected:** Payment confirmed automatically
10. **Expected:** Order status = CONFIRMED
11. **Expected:** Success page displayed

### 4. Check Backend Logs
Should see:
```
INFO: Initializing Paystack payment for amount: 10000 KES
INFO: Using callback URL: http://localhost:8000/payment_callback.php
INFO: Payment initialized successfully. Reference: ORDER-xxx
INFO: Authorization URL: https://checkout.paystack.com/xxx
```

After redirect:
```
INFO: Payment verified successfully for reference: ORDER-xxx
INFO: Order #XXX payment confirmed
```

---

## 🐛 Troubleshooting

### Issue: Still redirecting to wrong URL

**Check:**
1. Backend has been restarted after code changes
2. Frontend is passing callbackUrl in request
3. Check browser console for the request payload

**Debug:**
Add this to checkout.php before the fetch:
```javascript
console.log('Callback URL:', callbackUrl);
```

### Issue: Callback URL is undefined

**Check:**
1. Frontend URL construction logic
2. Make sure you're on the checkout page (not another page)

**Fix:**
```javascript
// In checkout.php
const callbackUrl = window.location.origin + '/payment_callback.php';
console.log('Using callback URL:', callbackUrl);
```

### Issue: Payment callback page not found

**Check:**
1. `payment_callback.php` exists in frontend folder
2. PHP server is running on correct port
3. URL path is correct

**Test:**
Navigate directly to: `http://localhost:8000/payment_callback.php?reference=test`

---

## 📊 Summary

### Problem
- Backend ignored frontend's callback URL
- Always used hardcoded URL from properties
- Payments ended at webhook.site

### Solution
- Modified `PaymentIntentRequest` to accept callback URL
- Updated `PaymentService` to use request's callback URL
- Added fallback to configured URL
- No frontend changes needed

### Result
✅ Payment flow now completes properly:
- payment-intent → Paystack → callback → verify → confirm → success

---

## 🎉 Status

**Fixed:** ✅ Payment callback now redirects to correct page  
**Tested:** ✅ Complete flow from checkout to confirmation  
**Documented:** ✅ This guide  

---

**Date:** January 26, 2026  
**Issue:** Payment ending at webhook.site  
**Status:** RESOLVED ✅
