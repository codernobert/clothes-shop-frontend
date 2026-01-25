# 🧪 Quick Test Guide - Payment Callback Fix

## ✅ What Was Fixed?
Payment was ending at webhook.site instead of redirecting to payment_callback.php for verification and confirmation.

---

## 🚀 How to Test

### Step 1: Restart Backend
```bash
cd "C:\Users\LENOVO\OneDrive\PERSONAL PROJECTS 2026\personal 2026\e_commerce_V2\front-end\clothes-shop"
./mvnw spring-boot:run
```

**Look for this in logs:**
```
INFO: Paystack API initialized successfully
INFO: Started ClothesShopApplication
```

### Step 2: Open Frontend
Navigate to: `http://localhost:8000/checkout.php`

### Step 3: Complete Checkout
1. Fill in all fields
2. Enter a valid email
3. Select payment method (Paystack or MPESA)
4. Click "Complete Order"

### Step 4: Check Browser Console
**Open Developer Tools (F12) → Console tab**

You should see:
```
Payment Intent Details:
- Amount: 10000
- Callback URL: http://localhost:8000/payment_callback.php
- Order Number: ORD-XXX
```

**✅ If callback URL is correct, continue!**  
**❌ If it shows webhook.site, frontend isn't updated properly**

### Step 5: Complete Payment on Paystack

**Test Mode Payment:**
- **Card Number:** 4084084084084081
- **Expiry:** 12/25
- **CVV:** 408
- **PIN:** 0000
- **OTP:** 123456

### Step 6: Verify Redirect

**After payment, you should be redirected to:**
```
http://localhost:8000/payment_callback.php?reference=ORDER-xxx
```

**✅ Expected:** Loading spinner, then success message  
**❌ If you end up at webhook.site:** Backend wasn't restarted with new code

### Step 7: Check Backend Logs

**Should see:**
```
INFO: Initializing Paystack payment for amount: 10000 KES
INFO: Using callback URL: http://localhost:8000/payment_callback.php
INFO: Payment initialized successfully
INFO: Authorization URL: https://checkout.paystack.com/xxx
```

**After payment:**
```
INFO: Payment verified successfully for reference: ORDER-xxx
```

---

## 🎯 Expected Results

### ✅ Success Indicators
1. ✅ Console shows correct callback URL (not webhook.site)
2. ✅ Redirected to Paystack payment page
3. ✅ After payment, redirected to payment_callback.php
4. ✅ Loading spinner shows "Verifying Payment..."
5. ✅ Success message appears with order details
6. ✅ "View My Orders" button visible
7. ✅ Order status in database = CONFIRMED

### ❌ Failure Indicators
1. ❌ Console shows webhook.site URL
2. ❌ Redirected to webhook.site after payment
3. ❌ Payment callback page never loads
4. ❌ Error message about missing order ID
5. ❌ Order status still PENDING

---

## 🐛 Troubleshooting

### Problem: Backend logs still show webhook.site

**Solution:**
1. Stop backend (Ctrl+C)
2. Clean and rebuild:
   ```bash
   ./mvnw clean compile
   ```
3. Restart:
   ```bash
   ./mvnw spring-boot:run
   ```

### Problem: Frontend console shows wrong URL

**Check:**
1. Are you on the correct page? (checkout.php)
2. Clear browser cache
3. Hard refresh (Ctrl+Shift+R)

**Manual fix:**
Open checkout.php and verify line ~169:
```javascript
const callbackUrl = window.location.origin + window.location.pathname.replace('checkout.php', 'payment_callback.php');
```

### Problem: 404 on payment_callback.php

**Check:**
1. File exists at: `frontend/payment_callback.php`
2. PHP server is running
3. Port is correct (8000 or whatever you're using)

**Test directly:**
```
http://localhost:8000/payment_callback.php?reference=test
```

Should see: "Order information not found" (expected without valid order)

### Problem: Payment verified but not confirmed

**Check:**
1. Backend logs for errors
2. Database connection
3. Order ID in sessionStorage

**Debug:**
Open browser console after payment:
```javascript
console.log(sessionStorage.getItem('pendingOrderId'));
```

Should show a number (order ID). If null, order wasn't created properly.

---

## 📊 Quick Verification Checklist

### Before Testing
- [ ] Backend code updated with callbackUrl field
- [ ] Backend restarted
- [ ] Frontend shows correct URL in console
- [ ] Database is running
- [ ] PHP server is running

### During Testing
- [ ] Checkout form submits successfully
- [ ] Console shows correct callback URL
- [ ] Redirected to Paystack
- [ ] Payment completes on Paystack
- [ ] Redirected back to payment_callback.php (NOT webhook.site)

### After Testing
- [ ] Payment verification succeeds
- [ ] Payment confirmation succeeds
- [ ] Success message displayed
- [ ] Order status = CONFIRMED in database
- [ ] Can view order in "My Orders"

---

## 🎉 Success!

If all checkboxes are ✅, the payment flow is working correctly!

**The complete flow now:**
1. Checkout → Create order
2. Payment intent → Get Paystack URL
3. Redirect → User pays on Paystack
4. **Callback → Redirected to payment_callback.php** ← FIXED!
5. Verify → Check payment with Paystack
6. Confirm → Update order status
7. Success → Show confirmation page

---

## 📝 What Changed?

### Backend
```java
// Before
.callbackUrl(callbackUrl) // Always hardcoded from properties

// After
.callbackUrl(effectiveCallbackUrl) // Uses request URL or falls back to properties
```

### Frontend
```javascript
// No changes - was already correct!
callbackUrl: callbackUrl // Sent in request
```

---

## 🔗 Related Documentation

- **`PAYMENT_CALLBACK_FIX.md`** - Detailed technical fix
- **`UPDATED_PAYMENT_FLOW.md`** - Complete payment flow
- **`PAYMENT_FLOW_VISUAL.md`** - Visual diagrams

---

**Date:** January 26, 2026  
**Status:** Ready to test! 🚀
