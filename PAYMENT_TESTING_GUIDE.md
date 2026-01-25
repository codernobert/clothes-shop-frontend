# Quick Test Guide - Complete Payment Flow

## Prerequisites
✅ Backend running on `http://localhost:8080`
✅ Frontend running on `http://localhost:8000`
✅ Database initialized with products
✅ Paystack API key configured

## Step-by-Step Testing

### 1. Add Items to Cart
```
1. Navigate to: http://localhost:8000/
2. Browse products
3. Click "Add to Cart" on any product
4. Verify cart count increases in navbar
```

### 2. View Cart
```
1. Click on cart icon in navbar
2. Navigate to: http://localhost:8000/cart.php
3. Verify items are displayed
4. Update quantities if needed
5. Click "Proceed to Checkout"
```

### 3. Checkout & Create Order
```
1. You'll be at: http://localhost:8000/checkout.php
2. Fill in the form:
   - First Name: John
   - Last Name: Doe
   - Email: john.doe@example.com
   - Phone: +254700000000
   - Address: 123 Main Street
   - City: Nairobi
   - Postal Code: 00100
   
3. Select Payment Method: Paystack
4. Click "Complete Order"
5. Watch browser console - order should be created
6. Button changes to "Processing..."
```

**What happens:**
- POST to `/api/checkout` creates order
- Order ID stored in sessionStorage
- POST to `/api/checkout/payment-intent` initializes payment
- You get redirected to Paystack

### 4. Paystack Payment Page
```
You'll be redirected to Paystack's hosted payment page.

Test Card Details:
- Card Number: 4084 0840 8408 4081
- CVV: 408
- Expiry: Any future date (e.g., 12/26)
- PIN: 0000 (if prompted)
- OTP: 123456 (if prompted)
```

**What happens:**
- Paystack processes payment
- On success, redirects to: 
  `http://localhost:8000/payment_callback.php?reference=ORDER-xxxxx`

### 5. Payment Verification & Confirmation
```
You'll automatically land on: payment_callback.php

Watch the loading screen:
1. "Verifying Payment..." appears
2. POST to /api/checkout/verify-payment
3. If successful, POST to /api/checkout/confirm-payment/{orderId}
4. Success message appears
```

**What happens:**
- Frontend retrieves orderId from sessionStorage
- Calls verify-payment endpoint
- If verified, calls confirm-payment endpoint
- Order status updated to COMPLETED
- Payment status updated to COMPLETED

### 6. Success Page
```
You should see:
✓ Green checkmark icon
✓ "Payment Successful!"
✓ Order Number
✓ Total Amount
✓ Buttons: "View My Orders" and "Continue Shopping"
```

### 7. Verify in Database (Optional)
```sql
-- Check order status
SELECT id, order_number, status, payment_status, payment_reference, total_amount
FROM orders
ORDER BY created_at DESC
LIMIT 5;

-- Should show:
-- status: COMPLETED
-- payment_status: COMPLETED
-- payment_reference: ORDER-xxxxx
```

---

## Browser Console Debugging

Open Developer Tools (F12) and watch the Console tab:

### Expected Console Logs:

#### During Checkout:
```
1. Checkout request sent...
2. Order created successfully: { orderId: 123, orderNumber: "ORD-20260126-..." }
3. Payment intent request sent...
4. Redirecting to Paystack...
```

#### During Callback:
```
1. Reference: ORDER-xxxxx
2. Order ID: 123
3. Verifying payment...
4. Payment verified: true
5. Confirming payment...
6. Payment confirmed successfully
```

---

## Network Tab Debugging

### During Checkout (checkout.php):

**Request 1: Create Order**
```
POST http://localhost:8000/ajax/checkout.php
Status: 200

Request Body:
{
  "userId": 1,
  "shippingAddress": "123 Main Street, Nairobi, 00100",
  "paymentMethod": "PAYSTACK"
}

Response:
{
  "success": true,
  "orderId": 123,
  "orderNumber": "ORD-20260126-..."
}
```

**Request 2: Initialize Payment**
```
POST http://localhost:8000/ajax/create_payment.php
Status: 200

Request Body:
{
  "amount": 500000,
  "currency": "KES",
  "description": "Order #ORD-20260126-...",
  "email": "john.doe@example.com",
  "callbackUrl": "http://localhost:8000/payment_callback.php"
}

Response:
{
  "success": true,
  "authorizationUrl": "https://checkout.paystack.com/...",
  "reference": "ORDER-xxxxx"
}
```

### During Callback (payment_callback.php):

**Request 3: Verify Payment**
```
GET http://localhost:8000/ajax/verify_payment.php?reference=ORDER-xxxxx
Status: 200

Response:
{
  "success": true,
  "data": true
}
```

**Request 4: Confirm Payment**
```
POST http://localhost:8000/ajax/confirm_payment.php
Status: 200

Request Body:
{
  "orderId": 123,
  "reference": "ORDER-xxxxx"
}

Response:
{
  "success": true,
  "data": {
    "id": 123,
    "orderNumber": "ORD-20260126-...",
    "status": "COMPLETED",
    "paymentStatus": "COMPLETED",
    "totalAmount": 5000.00
  }
}
```

---

## Troubleshooting

### Issue: "Order information not found"
**Cause:** sessionStorage was cleared or checkout wasn't completed
**Fix:** Start from step 1 again

### Issue: "Payment verification failed"
**Cause:** Payment wasn't successful on Paystack
**Fix:** Use correct test card details

### Issue: "Failed to confirm payment"
**Cause:** Backend couldn't update order
**Fix:** Check backend logs, verify order exists

### Issue: Paystack redirect doesn't work
**Cause:** Callback URL misconfigured
**Fix:** Verify `application.properties` has correct callback URL

### Issue: CORS errors
**Cause:** Backend CORS not configured
**Fix:** Verify `@CrossOrigin(origins = "*")` in CheckoutController

---

## Backend Logs to Watch

When testing, watch the Spring Boot console for these logs:

```
✓ Initializing Paystack payment for amount: 500000 KES
✓ Payment initialized successfully. Reference: ORDER-xxxxx
✓ Authorization URL: https://checkout.paystack.com/...
✓ Verifying payment status for reference: ORDER-xxxxx
✓ Payment verification successful for reference: ORDER-xxxxx
✓ Updating payment status for order: 123
✓ Payment status updated to: COMPLETED
```

---

## Success Criteria

✅ Order created with PENDING status
✅ Payment initialized and redirected to Paystack
✅ Payment completed on Paystack
✅ Redirected back to payment_callback.php
✅ Payment verified successfully
✅ Payment confirmed successfully
✅ Order status updated to COMPLETED
✅ Payment status updated to COMPLETED
✅ Success page displayed with order details

---

## Next Steps After Testing

1. Test with different payment scenarios:
   - Declined card
   - Canceled payment
   - Different amounts

2. Test error handling:
   - Network failures
   - Invalid references
   - Non-existent orders

3. Check orders page:
   - Navigate to http://localhost:8000/orders.php
   - Verify completed order appears

4. Admin verification:
   - Navigate to http://localhost:8000/admin/orders.php
   - Verify order shows as COMPLETED
