# Payment Flow Visual Guide

## 🔄 Complete Payment Flow (Updated)

### Overview
The payment flow now includes **ALL THREE ENDPOINTS** in the front-end:
1. ✅ payment-intent
2. ✅ verify-payment  
3. ✅ confirm-payment

---

## 🎯 Two Payment Scenarios

### Scenario 1: Paystack Redirect Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    CHECKOUT PAGE                             │
│  [User fills form] → [Submit Button Clicked]                │
└──────────────────────────┬──────────────────────────────────┘
                           ↓
                   ┌───────────────┐
                   │ Step 1:       │
                   │ Create Order  │
                   │ (PENDING)     │
                   └───────┬───────┘
                           ↓
              ┌────────────────────────┐
              │ Step 2:                │
              │ Payment Intent         │
              │ GET authorization URL  │
              └────────┬───────────────┘
                       ↓
         ┌─────────────────────────────┐
         │ Step 3:                     │
         │ REDIRECT to Paystack        │
         │ (User leaves your site)     │
         └─────────┬───────────────────┘
                   ↓
      ┌────────────────────────────────┐
      │ Paystack Payment Page          │
      │ [User enters card/M-Pesa]      │
      │ [User completes payment]       │
      └────────────┬───────────────────┘
                   ↓
         ┌─────────────────────────────┐
         │ Step 4:                     │
         │ REDIRECT BACK to callback   │
         │ (User returns to your site) │
         └─────────┬───────────────────┘
                   ↓
              ┌────────────────────────┐
              │ PAYMENT_CALLBACK.PHP   │
              └────────┬───────────────┘
                       ↓
              ┌────────────────────────┐
              │ Step 5:                │
              │ Verify Payment         │
              │ (Check with Paystack)  │
              └────────┬───────────────┘
                       ↓
              ┌────────────────────────┐
              │ Step 6:                │
              │ Confirm Payment        │
              │ (Update Order Status)  │
              └────────┬───────────────┘
                       ↓
              ┌────────────────────────┐
              │ ✅ Order CONFIRMED     │
              │ Show Success Message   │
              └────────────────────────┘
```

**Key Points:**
- Steps 1-3 happen in `checkout.php`
- Steps 5-6 happen in `payment_callback.php`
- User leaves and returns to your site

---

### Scenario 2: Non-Redirect Flow (MPESA STK/Future Methods)

```
┌─────────────────────────────────────────────────────────────┐
│                    CHECKOUT PAGE                             │
│  [User fills form] → [Submit Button Clicked]                │
└──────────────────────────┬──────────────────────────────────┘
                           ↓
                   ┌───────────────┐
                   │ Step 1:       │
                   │ Create Order  │
                   │ (PENDING)     │
                   └───────┬───────┘
                           ↓
              ┌────────────────────────┐
              │ Step 2:                │
              │ Payment Intent         │
              │ Initiate STK Push      │
              │ NO authorization URL   │
              └────────┬───────────────┘
                       ↓
         ┌─────────────────────────────┐
         │ [User stays on same page]   │
         │ [Loading indicator shows]   │
         │ [Popup on phone - pay now]  │
         └─────────┬───────────────────┘
                   ↓
              ┌────────────────────────┐
              │ Step 3:                │
              │ Verify Payment         │
              │ (Check payment status) │
              │ ← Called automatically │
              └────────┬───────────────┘
                       ↓
              ┌────────────────────────┐
              │ Step 4:                │
              │ Confirm Payment        │
              │ (Update Order Status)  │
              │ ← Called automatically │
              └────────┬───────────────┘
                       ↓
              ┌────────────────────────┐
              │ ✅ Order CONFIRMED     │
              │ Show Success Message   │
              │ Redirect to Orders     │
              └────────────────────────┘
```

**Key Points:**
- ALL steps happen in `checkout.php` (same page)
- User never leaves your site
- Automatic flow from intent → verify → confirm
- User pays on their phone while waiting

---

## 🔧 Technical Flow

### Checkout.php Logic

```javascript
// Step 1: Create Order
fetch('ajax/checkout.php') 
    ↓
// Step 2: Payment Intent
fetch('ajax/create_payment.php')
    ↓
// Decision Point
if (response.authorizationUrl) {
    // SCENARIO 1: Redirect Flow
    window.location.href = authorizationUrl;
    // (verify & confirm happen in callback page)
} else {
    // SCENARIO 2: Non-Redirect Flow
    verifyAndConfirmPayment();
    // (verify & confirm happen here)
}

// New Function: verifyAndConfirmPayment()
function verifyAndConfirmPayment(orderId, reference) {
    // Step 3: Verify
    fetch('ajax/verify_payment.php?reference=' + reference)
        ↓
    // Step 4: Confirm
    fetch('ajax/confirm_payment.php', {
        body: { orderId, reference }
    })
        ↓
    // Success!
    redirect to orders.php
}
```

---

## 📊 API Endpoints Involved

### 1️⃣ Payment Intent
```
POST /checkout/payment-intent
Request: { amount, currency, email, callbackUrl }
Response: { authorizationUrl?, reference }
```

### 2️⃣ Verify Payment
```
POST /checkout/verify-payment?reference=xxx
Response: { success: true/false }
```

### 3️⃣ Confirm Payment
```
POST /checkout/confirm-payment/{orderId}?reference=xxx
Response: { orderId, orderNumber, status: "CONFIRMED" }
```

---

## ✅ What Changed?

### Before ❌
```
checkout.php:
  1. Create order
  2. Payment intent
  3. Redirect (END HERE) ← Incomplete!

payment_callback.php:
  4. Verify
  5. Confirm
```
**Problem:** Non-redirect payments couldn't complete!

### After ✅
```
checkout.php:
  1. Create order
  2. Payment intent
  3a. IF redirect: Redirect to provider
  3b. IF non-redirect: 
      → Verify payment
      → Confirm payment
      → Complete! ✅

payment_callback.php:
  4. Verify payment
  5. Confirm payment
  (Only for redirect flows)
```
**Solution:** All payment methods can complete the full flow!

---

## 🎯 Benefits

### ✅ Complete Flow
- All 3 endpoints used in front-end
- No orphaned payments

### ✅ Flexible Payment Methods
- Supports redirect (Paystack)
- Supports non-redirect (MPESA)
- Easy to add new methods

### ✅ Better UX
- Clear loading states
- Automatic verification
- Proper error handling

### ✅ Consistent Behavior
- Same endpoints used for all methods
- Same error handling
- Same success flow

---

## 🧪 Testing

### Test Redirect Flow (Paystack)
1. Select Paystack payment method
2. Complete checkout
3. You'll be redirected away
4. Complete payment
5. You'll be redirected back
6. ✅ Order confirmed automatically

### Test Non-Redirect Flow (Future MPESA)
1. Select MPESA payment method
2. Complete checkout
3. You stay on same page
4. Complete payment on phone
5. ✅ Order confirmed automatically

---

## 📝 Files Modified

- ✏️ **frontend/checkout.php** - Added verify & confirm flow
- 📄 **UPDATED_PAYMENT_FLOW.md** - Complete documentation
- 📄 **PAYMENT_FLOW_UPDATE_SUMMARY.md** - Summary of changes
- 📄 **PAYMENT_FLOW_VISUAL.md** - This visual guide

---

## 🎉 Summary

**Before:** Payment flow was incomplete for non-redirect methods.

**After:** Complete payment flow for ALL methods:
- payment-intent ✅
- verify-payment ✅  
- confirm-payment ✅

All three endpoints are now properly integrated into the front-end flow!
