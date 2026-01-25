# Updated Payment Flow Documentation

## Overview
The payment flow has been updated to include **verify-payment** and **confirm-payment** endpoints in the front-end flow, ensuring complete payment processing regardless of the payment method used.

## Complete Payment Flow

### 1. Checkout Flow (checkout.php)
The checkout process now supports two payment scenarios:

#### Scenario A: Redirect Payment Flow (Paystack)
**Flow**: checkout → payment-intent → redirect → verify-payment → confirm-payment

1. **Create Order** (`ajax/checkout.php`)
   - User fills out shipping information
   - Order is created with status `PENDING`
   - Returns `orderId` and `orderNumber`

2. **Initialize Payment** (`ajax/create_payment.php`)
   - Calls `/checkout/payment-intent` endpoint
   - Returns `authorizationUrl` and `reference`
   - User is redirected to Paystack payment page

3. **User Completes Payment on Paystack**
   - User enters card details on Paystack
   - Paystack processes the payment
   - User is redirected back to `payment_callback.php?reference=xxx`

4. **Verify Payment** (`ajax/verify_payment.php`)
   - Calls `/checkout/verify-payment?reference=xxx` endpoint
   - Backend verifies payment with Paystack
   - Returns verification status

5. **Confirm Payment** (`ajax/confirm_payment.php`)
   - Calls `/checkout/confirm-payment/{orderId}?reference=xxx` endpoint
   - Updates order status to `CONFIRMED`
   - Returns order details

#### Scenario B: Non-Redirect Payment Flow (MPESA/Future Methods)
**Flow**: checkout → payment-intent → verify-payment → confirm-payment (synchronous)

1. **Create Order** (`ajax/checkout.php`)
   - Same as Scenario A

2. **Initialize Payment** (`ajax/create_payment.php`)
   - Calls `/checkout/payment-intent` endpoint
   - Returns `reference` (no `authorizationUrl`)
   - Payment is initiated (e.g., STK Push for MPESA)

3. **Verify Payment** (`ajax/verify_payment.php`)
   - Called automatically in the same session
   - Polls backend to check payment status
   - Waits for user to complete payment on their device

4. **Confirm Payment** (`ajax/confirm_payment.php`)
   - Called automatically after successful verification
   - Updates order status to `CONFIRMED`
   - User is redirected to orders page

## Implementation Details

### checkout.php
```javascript
// Main checkout handler
1. Create order via ajax/checkout.php
2. Create payment intent via ajax/create_payment.php
3. Check if authorizationUrl exists:
   - YES: Redirect to Paystack (verification happens in callback)
   - NO: Call verifyAndConfirmPayment() function

// verifyAndConfirmPayment() function
1. Call ajax/verify_payment.php
2. If verified, call ajax/confirm_payment.php
3. Show success message and redirect to orders page
```

### payment_callback.php
```javascript
// Handles Paystack redirect callback
1. Get reference from URL parameter
2. Get orderId from sessionStorage
3. Call verifyAndConfirmPayment():
   - Verify payment via ajax/verify_payment.php
   - Confirm payment via ajax/confirm_payment.php
4. Show success or failure message
```

## API Endpoints Used

### 1. POST /checkout/payment-intent
**Purpose**: Initialize payment with payment provider

**Request**:
```json
{
  "amount": 10000,
  "currency": "KES",
  "description": "Order #123",
  "email": "customer@example.com",
  "callbackUrl": "https://example.com/payment_callback.php"
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "authorizationUrl": "https://checkout.paystack.com/xxx",
    "reference": "ref_xxx123"
  }
}
```

### 2. POST /checkout/verify-payment?reference={ref}
**Purpose**: Verify payment status with payment provider

**Request**: Query parameter `reference`

**Response**:
```json
{
  "success": true,
  "data": true  // true if payment verified
}
```

### 3. POST /checkout/confirm-payment/{orderId}?reference={ref}
**Purpose**: Confirm payment and update order status

**Request**: 
- Path parameter: `orderId`
- Query parameter: `reference`

**Response**:
```json
{
  "success": true,
  "data": {
    "orderId": 1,
    "orderNumber": "ORD-123",
    "totalAmount": 100.00,
    "status": "CONFIRMED"
  }
}
```

## Payment Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     CHECKOUT PAGE                            │
│  User fills form → Submit                                    │
└──────────────────────────┬──────────────────────────────────┘
                           ↓
                  ┌────────────────┐
                  │ Create Order   │
                  │ (ajax/checkout)│
                  └────────┬───────┘
                           ↓
                ┌──────────────────────┐
                │ Payment Intent        │
                │ (ajax/create_payment) │
                └──────────┬───────────┘
                           ↓
              ┌────────────┴────────────┐
              │                         │
    ┌─────────▼─────────┐    ┌─────────▼──────────┐
    │ Has Authorization │    │ No Authorization   │
    │ URL (Paystack)    │    │ URL (MPESA/Other)  │
    └─────────┬─────────┘    └─────────┬──────────┘
              │                         │
    ┌─────────▼──────────┐   ┌─────────▼──────────┐
    │ Redirect to        │   │ Verify Payment     │
    │ Paystack           │   │ (ajax/verify)      │
    └─────────┬──────────┘   └─────────┬──────────┘
              │                         │
    ┌─────────▼──────────┐              │
    │ User pays on       │              │
    │ Paystack           │              │
    └─────────┬──────────┘              │
              │                         │
    ┌─────────▼──────────┐              │
    │ Callback Page      │              │
    │ (payment_callback) │              │
    └─────────┬──────────┘              │
              │                         │
    ┌─────────▼──────────┐   ┌─────────▼──────────┐
    │ Verify Payment     │   │ Confirm Payment    │
    │ (ajax/verify)      │   │ (ajax/confirm)     │
    └─────────┬──────────┘   └─────────┬──────────┘
              │                         │
    ┌─────────▼──────────┐              │
    │ Confirm Payment    │              │
    │ (ajax/confirm)     │              │
    └─────────┬──────────┘              │
              │                         │
              └─────────┬───────────────┘
                        ↓
              ┌─────────────────┐
              │ Order Confirmed │
              │ Redirect to     │
              │ Orders Page     │
              └─────────────────┘
```

## Key Changes

### Before
- Front-end flow ended at payment-intent endpoint
- Verify and confirm only happened in callback page
- No support for non-redirect payment methods

### After
- Front-end flow includes all three endpoints: payment-intent → verify-payment → confirm-payment
- Automatic handling of both redirect and non-redirect flows
- Complete payment processing in the same session for MPESA-like methods

## Testing

### Test Paystack Flow
1. Add items to cart
2. Go to checkout
3. Fill in shipping details
4. Select "Paystack" as payment method
5. Click "Complete Order"
6. Should redirect to Paystack
7. Complete payment on Paystack
8. Should redirect back and show success
9. Verify order status is "CONFIRMED"

### Test Non-Redirect Flow (Future MPESA)
1. Add items to cart
2. Go to checkout
3. Fill in shipping details
4. Select "M-Pesa" as payment method
5. Click "Complete Order"
6. Should stay on same page with loading indicator
7. Complete payment on phone
8. Should show success message and redirect to orders
9. Verify order status is "CONFIRMED"

## Error Handling

Both flows include comprehensive error handling:
- Order creation failure
- Payment initialization failure
- Payment verification failure
- Payment confirmation failure

All errors are displayed to the user with appropriate messages and the ability to retry.

## Security Considerations

1. **Order ID Storage**: Stored in sessionStorage, not vulnerable to XSS
2. **Reference Validation**: Payment reference must match on backend
3. **Double-Spend Prevention**: Backend validates payment hasn't been confirmed before
4. **Amount Verification**: Backend verifies payment amount matches order total

## Future Enhancements

1. **Polling for Verification**: Add retry logic for slow payment verifications
2. **Webhook Support**: Add webhook handler for immediate payment notifications
3. **Multiple Payment Methods**: Extend to support more providers
4. **Payment Status Page**: Show real-time payment status updates
