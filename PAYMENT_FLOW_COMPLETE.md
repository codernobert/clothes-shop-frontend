# Complete Payment Flow with Verification & Confirmation

## Overview
This document describes the complete end-to-end payment flow including payment initialization, verification, and confirmation.

## Payment Flow Steps

### 1. **Checkout & Order Creation** (checkout.php)
**Frontend Action:**
- User fills in shipping details and selects payment method
- User clicks "Complete Order" button
- Form data is sent to `ajax/checkout.php`

**Backend Endpoint:** `POST /api/checkout`
- Creates order with status: `PENDING`
- Payment status: `PENDING`
- Returns: `{ orderId, orderNumber }`

**Frontend Response:**
- Stores `orderId` in sessionStorage for later use
- Proceeds to Step 2

---

### 2. **Payment Intent Initialization** (checkout.php)
**Frontend Action:**
- Sends payment details to `ajax/create_payment.php`
- Includes: amount, currency, email, callbackUrl

**Backend Endpoint:** `POST /api/checkout/payment-intent`
- Calls Paystack API to initialize transaction
- Paystack creates a payment session
- Returns: `{ authorizationUrl, reference }`

**Frontend Response:**
- Redirects user to Paystack's `authorizationUrl`
- User completes payment on Paystack's page

---

### 3. **Payment Completion** (External - Paystack)
**Paystack Hosted Page:**
- User enters card details or selects M-Pesa
- Completes payment authentication
- Paystack processes the payment

**After Payment:**
- Paystack redirects to: `http://localhost:8000/payment_callback.php?reference=ORDER-xxxxx`

---

### 4. **Payment Verification** (payment_callback.php)
**Frontend Action:**
- Retrieves `orderId` from sessionStorage
- Retrieves `reference` from URL query parameter
- Calls `ajax/verify_payment.php`

**Backend Endpoint:** `POST /api/checkout/verify-payment?reference={reference}`
- Calls Paystack API to verify transaction
- Checks if payment was successful
- Returns: `{ success: true/false }`

**Frontend Response:**
- If verification succeeds → Proceed to Step 5
- If verification fails → Show error message

---

### 5. **Payment Confirmation** (payment_callback.php)
**Frontend Action:**
- Calls `ajax/confirm_payment.php` with orderId and reference

**Backend Endpoint:** `POST /api/checkout/confirm-payment/{orderId}?reference={reference}`
- Verifies payment again (double-check)
- Updates order payment status to `COMPLETED`
- Stores payment reference
- Returns: `{ order: OrderResponse }`

**Frontend Response:**
- Clears `orderId` from sessionStorage
- Shows success message with order details
- Provides links to view orders or continue shopping

---

## API Endpoints Summary

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/api/checkout` | POST | Create order | ✅ Implemented |
| `/api/checkout/payment-intent` | POST | Initialize Paystack payment | ✅ Implemented |
| `/api/checkout/verify-payment` | POST | Verify payment with Paystack | ✅ Implemented |
| `/api/checkout/confirm-payment/{orderId}` | POST | Confirm payment & update order | ✅ Implemented |

---

## Frontend Files

| File | Purpose |
|------|---------|
| `checkout.php` | Order creation & payment initialization |
| `payment_callback.php` | Payment verification & confirmation |
| `ajax/checkout.php` | Proxy to backend checkout API |
| `ajax/create_payment.php` | Proxy to payment-intent API |
| `ajax/verify_payment.php` | Proxy to verify-payment API |
| `ajax/confirm_payment.php` | Proxy to confirm-payment API |

---

## Configuration

### application.properties
```properties
paystack.callback.url=http://localhost:8000/payment_callback.php
```

This URL is where Paystack redirects users after payment completion.

---

## Flow Diagram

```
┌─────────────┐
│   User      │
│ (checkout)  │
└──────┬──────┘
       │
       ▼
┌─────────────────────────────────┐
│  1. POST /api/checkout          │
│  Create Order (PENDING)         │
│  Returns: orderId               │
└──────┬──────────────────────────┘
       │
       ▼ (store orderId in session)
┌─────────────────────────────────┐
│  2. POST /api/checkout/         │
│     payment-intent              │
│  Initialize Paystack payment    │
│  Returns: authorizationUrl      │
└──────┬──────────────────────────┘
       │
       ▼ (redirect to Paystack)
┌─────────────────────────────────┐
│  3. Paystack Hosted Page        │
│  User completes payment         │
└──────┬──────────────────────────┘
       │
       ▼ (redirect with reference)
┌─────────────────────────────────┐
│  payment_callback.php           │
│  ?reference=ORDER-xxxxx         │
└──────┬──────────────────────────┘
       │
       ▼ (verify payment)
┌─────────────────────────────────┐
│  4. POST /api/checkout/         │
│     verify-payment              │
│  Verify with Paystack           │
│  Returns: success = true/false  │
└──────┬──────────────────────────┘
       │
       ▼ (if verified)
┌─────────────────────────────────┐
│  5. POST /api/checkout/         │
│     confirm-payment/{orderId}   │
│  Update order to COMPLETED      │
│  Returns: order details         │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│  Success Page                   │
│  Show order confirmation        │
└─────────────────────────────────┘
```

---

## Error Handling

### Payment Verification Failed
- Order remains in `PENDING` status
- User can retry payment from orders page (future feature)
- Or contact support with order number

### Payment Confirmation Failed
- Payment was successful but order update failed
- This requires manual intervention
- Admin can verify payment reference and manually update order

---

## Testing Steps

1. **Start Backend:**
   ```bash
   ./mvnw spring-boot:run
   ```

2. **Start Frontend:**
   ```bash
   cd frontend
   php -S localhost:8000
   ```

3. **Test Flow:**
   - Navigate to `http://localhost:8000/checkout.php`
   - Fill in shipping details
   - Click "Complete Order"
   - You'll be redirected to Paystack (or test page)
   - After payment, redirected to `payment_callback.php`
   - Verification and confirmation happen automatically
   - Success page shows order details

4. **Paystack Test Cards:**
   - Success: `4084084084084081` (any CVV, future expiry)
   - Declined: `5060990580000217408`

---

## Security Considerations

1. **Backend Validation:**
   - All payment amounts verified server-side
   - Order ownership validated before confirmation

2. **Double Verification:**
   - Payment verified with Paystack before order update
   - Reference number checked to prevent replay attacks

3. **Session Management:**
   - OrderId stored in sessionStorage (client-side only)
   - Cleared after successful confirmation

---

## Future Enhancements

1. **Webhook Integration:**
   - Handle Paystack webhooks for asynchronous notifications
   - Update order status even if user closes browser

2. **Retry Logic:**
   - Allow users to retry failed payments
   - Resume incomplete orders

3. **Admin Dashboard:**
   - View pending payments
   - Manually verify and confirm payments
   - Refund processing

4. **Email Notifications:**
   - Send order confirmation emails
   - Payment receipt with invoice
