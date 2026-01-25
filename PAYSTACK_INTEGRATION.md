# Paystack Integration Guide - MPESA Support

## Overview

Your application now uses **Paystack** instead of Stripe for payment processing. Paystack supports:
- 💳 Card payments
- 📱 Mobile Money (including **MPESA** for Kenya)
- 🏦 Bank transfers
- 📞 USSD payments

## What Changed

### ✅ Removed
- ❌ Stripe Java SDK
- ❌ Stripe-specific code and configuration

### ✅ Added
- ✅ Paystack API integration via WebClient (reactive HTTP client)
- ✅ MPESA support through Paystack's mobile money channel
- ✅ New Paystack-specific DTOs:
  - `PaystackInitializeRequest`
  - `PaystackInitializeResponse`
  - `PaystackVerifyResponse`

## Current Configuration

Your `application.properties` already has the Paystack configuration:

```properties
# Paystack Configuration (supports MPESA)
paystack.api.key=sk_test_3f4ed3ee01e4b3ea1dcf91b00caecdbadd0c8ade
paystack.api.url=https://api.paystack.co
paystack.callback.url=http://localhost:8080/api/checkout/paystack/callback
```

## How Paystack Payment Works

### 1. Initialize Payment
**Endpoint:** `POST /api/checkout/payment-intent`

**Request:**
```json
{
  "amount": 500000,
  "currency": "KES",
  "email": "customer@example.com",
  "description": "Order #12345"
}
```

**Note:** Amount should be in the smallest currency unit:
- For KES (Kenya Shillings): 500000 = 5,000 KES (amount in cents)
- For NGN (Nigerian Naira): 100000 = 1,000 NGN (amount in kobo)

**Response:**
```json
{
  "success": true,
  "message": "Payment initialized successfully",
  "data": {
    "clientSecret": "access_code_here",
    "paymentIntentId": "reference_here",
    "status": "initialized",
    "authorizationUrl": "https://checkout.paystack.com/xxxxx"
  }
}
```

### 2. Redirect User to Payment Page
The user should be redirected to the `authorizationUrl` from the response. This page allows them to:
- Pay with card
- Pay with MPESA (mobile money)
- Pay with bank transfer
- Pay with USSD

### 3. Payment Callback
After payment, Paystack redirects the user to your callback URL with the transaction reference.

**Callback Endpoint:** `GET /api/checkout/paystack/callback?reference=ORDER-xxx`

This endpoint automatically verifies the payment and redirects to:
- Success: `/payment-success?reference=ORDER-xxx`
- Failure: `/payment-failed?reference=ORDER-xxx`

### 4. Verify Payment (Optional - for backend verification)
**Endpoint:** `POST /api/checkout/verify-payment?reference=ORDER-xxx`

**Response:**
```json
{
  "success": true,
  "message": "Payment verified successfully",
  "data": true
}
```

### 5. Confirm Payment for Order
**Endpoint:** `POST /api/checkout/confirm-payment/{orderId}?reference=ORDER-xxx`

This updates the order payment status to COMPLETED.

## Testing MPESA Payments

### Test Mode
Paystack test mode supports simulating mobile money payments:

1. Initialize payment with currency "KES"
2. Go to the authorization URL
3. Select "Mobile Money" or "MPESA" as payment method
4. Use Paystack's test phone numbers for simulation

### Live Mode
For live MPESA payments:
1. Get your live API key from Paystack dashboard
2. Update `paystack.api.key` with your live key (`sk_live_...`)
3. Ensure your Paystack account is verified and supports Kenya
4. Users can pay with their real MPESA accounts

## API Endpoints Summary

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/checkout/payment-intent` | Initialize payment |
| POST | `/api/checkout/verify-payment` | Verify payment status |
| GET | `/api/checkout/paystack/callback` | Payment callback (automatic) |
| POST | `/api/checkout/confirm-payment/{orderId}` | Update order payment status |

## Complete Payment Flow Example

### Step 1: Create an Order
```bash
POST http://localhost:8080/api/checkout
{
  "userId": 1,
  "shippingAddress": "123 Main St",
  "paymentMethod": "PAYSTACK"
}
```

Response:
```json
{
  "success": true,
  "data": {
    "orderId": 1,
    "totalAmount": 500000,
    "status": "PENDING"
  }
}
```

### Step 2: Initialize Payment
```bash
POST http://localhost:8080/api/checkout/payment-intent
{
  "amount": 500000,
  "currency": "KES",
  "email": "customer@example.com",
  "description": "Order #1"
}
```

Response:
```json
{
  "success": true,
  "data": {
    "authorizationUrl": "https://checkout.paystack.com/abc123",
    "paymentIntentId": "ORDER-uuid-here"
  }
}
```

### Step 3: Redirect User
Redirect user to the `authorizationUrl`. They will:
1. Choose MPESA as payment method
2. Enter their phone number
3. Receive MPESA prompt on their phone
4. Enter PIN to complete payment

### Step 4: Handle Callback
After payment, user is redirected to your callback URL automatically.

### Step 5: Confirm Payment
```bash
POST http://localhost:8080/api/checkout/confirm-payment/1?reference=ORDER-uuid-here
```

Response:
```json
{
  "success": true,
  "message": "Payment confirmed",
  "data": {
    "orderId": 1,
    "paymentStatus": "COMPLETED"
  }
}
```

## Important Notes

### Currency Support
- **KES** (Kenya Shillings) - Supports MPESA
- **NGN** (Nigerian Naira) - Primary Paystack currency
- **GHS** (Ghana Cedis)
- **ZAR** (South African Rand)
- **USD** (US Dollars)

### Amount Format
Always send amounts in the smallest currency unit:
- KES: multiply by 100 (5000 KES = 500000 cents)
- NGN: multiply by 100 (1000 NGN = 100000 kobo)

### Error Handling
The service includes:
- ✅ Circuit breaker pattern (via Resilience4j)
- ✅ Automatic retries (up to 3 attempts)
- ✅ Timeout handling (5 seconds)
- ✅ Detailed error logging

## Troubleshooting

### Issue: "PAYSTACK API KEY NOT CONFIGURED"
**Solution:** Check that your API key in `application.properties` is valid and starts with `sk_test_` or `sk_live_`

### Issue: Payment initialization fails
**Solution:** 
1. Check your API key is valid
2. Ensure you have internet connection
3. Check Paystack dashboard for API status
4. Verify amount is in correct format (smallest currency unit)

### Issue: MPESA not showing as option
**Solution:**
1. Ensure currency is "KES"
2. Verify your Paystack account supports Kenya
3. Check that channels include "mobile_money"

## Paystack Dashboard

Access your Paystack dashboard at:
- Test: https://dashboard.paystack.com/
- Live: https://dashboard.paystack.com/

Here you can:
- View all transactions
- Get API keys
- Set up webhooks
- View settlement reports
- Configure payment channels

## Support

- **Paystack Docs:** https://paystack.com/docs/
- **Paystack API Reference:** https://paystack.com/docs/api/
- **MPESA Integration:** https://paystack.com/docs/payments/mobile-money/
- **Support:** support@paystack.com

## Frontend Integration Tips

For a complete user experience:

1. **Payment Button:**
```javascript
// Initialize payment
const response = await fetch('/api/checkout/payment-intent', {
  method: 'POST',
  body: JSON.stringify({
    amount: 500000,
    currency: 'KES',
    email: user.email
  })
});

const data = await response.json();

// Redirect to Paystack checkout
if (data.success) {
  window.location.href = data.data.authorizationUrl;
}
```

2. **Success/Failure Pages:**
Create pages at `/payment-success` and `/payment-failed` to handle redirects from Paystack callback.

3. **Payment Verification:**
After redirect, verify payment on your success page:
```javascript
const urlParams = new URLSearchParams(window.location.search);
const reference = urlParams.get('reference');

// Confirm with backend
await fetch(`/api/checkout/verify-payment?reference=${reference}`);
```

---

🎉 **You're all set!** Your application now supports MPESA payments through Paystack.

