# 🧪 Paystack M-Pesa Testing Guide - Postman

## 📋 Testing Flow: Product → Cart → Checkout → M-Pesa Payment

This guide will walk you through testing the complete customer journey with Paystack M-Pesa integration.

---

## 🚀 Prerequisites

### 1. Start the Application
```bash
# Make sure PostgreSQL is running
# Start Spring Boot application
mvn spring-boot:run
```

### 2. Import Postman Collection
- File: `Ecommerce_Customer_Journey.postman_collection.json`
- Import this file into Postman
- All endpoints are pre-configured with variables

### 3. Verify Application is Running
**Test Health Endpoint:**
```
GET http://localhost:8080/actuator/health
```
Expected: `{"status":"UP"}`

---

## 📝 Step-by-Step Testing Process

### **Step 1: Add Product to Database**

Since you need products in the database first, use the Admin endpoint to create a product.

**Endpoint:** `POST http://localhost:8080/api/admin/products`

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
  "name": "Classic T-Shirt",
  "description": "Comfortable cotton t-shirt",
  "price": 1500,
  "category": "TOPS",
  "brand": "Nike",
  "size": "M",
  "color": "Blue",
  "gender": "UNISEX",
  "stock": 50,
  "imageUrl": "https://example.com/tshirt.jpg"
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "id": 1,
    "name": "Classic T-Shirt",
    "price": 1500,
    "stock": 50,
    ...
  }
}
```

**✅ Action:** Save the `product id` (e.g., 1) for the next step.

---

### **Step 2: Browse Products** ✅ 

Verify the product is available.

**Endpoint:** `GET http://localhost:8080/api/products`

**Expected Response:**
```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Classic T-Shirt",
      "price": 1500,
      "stock": 50
    }
  ]
}
```

*Note: The Postman collection automatically saves the first product's ID to `{{productId}}` variable.*

---

### **Step 3: Add Product to Cart** 🛒

**Endpoint:** `POST http://localhost:8080/api/cart/1/items`

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
  "productId": 1,
  "quantity": 2
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Product added to cart",
  "data": {
    "id": 1,
    "productId": 1,
    "productName": "Classic T-Shirt",
    "quantity": 2,
    "price": 1500,
    "subtotal": 3000
  }
}
```

**✅ Action:** Note the cart item ID and total amount.

---

### **Step 4: View Cart** 👀

**Endpoint:** `GET http://localhost:8080/api/cart/1`

**Expected Response:**
```json
{
  "success": true,
  "message": "Cart retrieved successfully",
  "data": {
    "userId": 1,
    "items": [
      {
        "id": 1,
        "productId": 1,
        "productName": "Classic T-Shirt",
        "quantity": 2,
        "price": 1500,
        "subtotal": 3000
      }
    ],
    "totalAmount": 3000,
    "itemCount": 1
  }
}
```

**✅ Action:** Confirm total amount matches expectations (2 × 1500 = 3000 KES).

---

### **Step 5: Create Order (Checkout)** 📦

**Endpoint:** `POST http://localhost:8080/api/checkout`

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
  "userId": 1,
  "shippingAddress": "123 Main Street, Nairobi, Kenya",
  "paymentMethod": "PAYSTACK"
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "orderId": 1,
    "orderNumber": "ORD-2026-0001",
    "userId": 1,
    "totalAmount": 3000,
    "paymentStatus": "PENDING",
    "orderStatus": "PENDING",
    "shippingAddress": "123 Main Street, Nairobi, Kenya",
    "paymentMethod": "PAYSTACK",
    "items": [
      {
        "productId": 1,
        "productName": "Classic T-Shirt",
        "quantity": 2,
        "price": 1500,
        "subtotal": 3000
      }
    ]
  }
}
```

**✅ Action:** Save the `orderId` (e.g., 1) and `orderNumber` (e.g., ORD-2026-0001).

---

### **Step 6: Initialize Payment (M-Pesa via Paystack)** 💰

This is where Paystack M-Pesa integration happens.

**Endpoint:** `POST http://localhost:8080/api/checkout/payment-intent`

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
  "amount": 3000,
  "currency": "KES",
  "email": "customer@example.com",
  "description": "Order #ORD-2026-0001"
}
```

**⚠️ IMPORTANT:** 
- Amount is in **smallest currency unit** (cents/kobo)
- For KES: 3000 = 30.00 KES (if Paystack expects cents)
- For KES: If Paystack expects actual amount, use 3000 for 3000 KES
- Check Paystack documentation for Kenya

**Expected Response:**
```json
{
  "success": true,
  "message": "Payment initialized successfully",
  "data": {
    "clientSecret": "sk_test_xxxxx",
    "paymentIntentId": "REF-2026-xxxxx",
    "status": "initialized",
    "authorizationUrl": "https://checkout.paystack.com/xxxxx"
  }
}
```

**✅ Actions:**
1. Save the `paymentIntentId` (this is your payment reference)
2. Copy the `authorizationUrl`
3. Open this URL in your browser

---

### **Step 7: Complete Payment (Browser)** 📱

1. **Open the Authorization URL** from Step 6 in your browser
2. You'll see Paystack's payment page with options:
   - 💳 Card Payment
   - 📱 Mobile Money (M-Pesa)
   - 🏦 Bank Transfer
   - 📞 USSD

3. **Select "Mobile Money" or "M-Pesa"**
4. **Enter your M-Pesa phone number** (Kenyan format: +254...)
5. **Follow M-Pesa prompts on your phone**:
   - You'll receive an M-Pesa payment request
   - Enter your M-Pesa PIN
   - Confirm the payment

6. **Wait for confirmation**:
   - Paystack will redirect you to the callback URL after payment
   - The callback URL is: `http://localhost:8080/api/checkout/paystack/callback?reference=REF-xxx`

---

### **Step 8: Verify Payment** ✅

After completing payment in browser, verify it succeeded.

**Endpoint:** `POST http://localhost:8080/api/checkout/verify-payment?reference=REF-2026-xxxxx`

**Replace `REF-2026-xxxxx` with the actual payment reference from Step 6.**

**Expected Response (Success):**
```json
{
  "success": true,
  "message": "Payment verified successfully",
  "data": true
}
```

**Expected Response (Failed):**
```json
{
  "success": false,
  "message": "Payment verification failed",
  "data": false
}
```

---

### **Step 9: Confirm Order Payment** 🎉

Update the order with the successful payment.

**Endpoint:** `POST http://localhost:8080/api/checkout/confirm-payment/1?reference=REF-2026-xxxxx`

**Replace:**
- `1` with your actual `orderId` from Step 5
- `REF-2026-xxxxx` with your payment reference

**Expected Response:**
```json
{
  "success": true,
  "message": "Payment confirmed",
  "data": {
    "orderId": 1,
    "orderNumber": "ORD-2026-0001",
    "paymentStatus": "COMPLETED",
    "orderStatus": "CONFIRMED",
    "paymentReference": "REF-2026-xxxxx",
    "totalAmount": 3000
  }
}
```

---

### **Step 10: View Order Details** 📋

Verify the complete order with payment information.

**Endpoint:** `GET http://localhost:8080/api/orders/1`

**Expected Response:**
```json
{
  "orderId": 1,
  "orderNumber": "ORD-2026-0001",
  "userId": 1,
  "totalAmount": 3000,
  "paymentStatus": "COMPLETED",
  "orderStatus": "CONFIRMED",
  "paymentMethod": "PAYSTACK",
  "paymentReference": "REF-2026-xxxxx",
  "shippingAddress": "123 Main Street, Nairobi, Kenya",
  "items": [
    {
      "productId": 1,
      "productName": "Classic T-Shirt",
      "quantity": 2,
      "price": 1500,
      "subtotal": 3000
    }
  ],
  "createdAt": "2026-01-25T...",
  "updatedAt": "2026-01-25T..."
}
```

---

## 🧪 Testing with Paystack Test Mode

### Test Credentials

Paystack provides test cards and phone numbers for testing:

#### Test M-Pesa Phone Numbers:
```
+254708374149 (Test number - always succeeds)
+254111222333 (Test number - can be used)
```

#### Test Cards (if needed):
```
Card Number: 5060666666666666666
CVV: 123
Expiry: Any future date
PIN: 1234
OTP: 123456
```

### Test Mode Indicators:
- Your API key starts with `sk_test_` (not `sk_live_`)
- Payments won't deduct real money
- M-Pesa prompts are simulated

---

## 🐛 Troubleshooting

### Issue 1: Payment Amount Mismatch
**Problem:** Paystack shows wrong amount (e.g., 30.00 instead of 3000)

**Solution:** Check if Paystack expects amount in cents:
- For KES, multiply by 100: `3000 KES = 300000 cents`
- Update the payment-intent request body:
```json
{
  "amount": 300000,  // 3000 KES in cents
  "currency": "KES",
  ...
}
```

### Issue 2: M-Pesa Option Not Showing
**Problem:** Only card payment option appears

**Possible Causes:**
1. M-Pesa not enabled on your Paystack account
2. Currency not KES
3. Amount below minimum (usually 10 KES)

**Solution:**
- Ensure currency is `"KES"`
- Check Paystack dashboard settings
- Enable Mobile Money in payment channels

### Issue 3: Payment Reference Not Found
**Problem:** Verify payment returns error

**Solution:**
- Wait 5-10 seconds after payment before verifying
- Check the reference format matches Paystack's format
- Look in browser URL after callback redirect

### Issue 4: Order Not Updating
**Problem:** Order payment status stays PENDING

**Solution:**
- Verify payment was successful first (Step 8)
- Check orderId matches the created order
- Ensure payment reference is correct

---

## 📊 Quick Testing Checklist

- [ ] Application is running (health check passes)
- [ ] Database has products
- [ ] Product added to cart successfully
- [ ] Cart total is correct
- [ ] Order created with PENDING status
- [ ] Payment initialized (got authorization URL)
- [ ] Payment completed via M-Pesa
- [ ] Payment verified successfully
- [ ] Order updated to COMPLETED
- [ ] Order details show payment reference

---

## 💡 Pro Tips

1. **Use Postman Variables:**
   - The collection auto-saves `productId`, `orderId`, `paymentReference`
   - This makes testing faster

2. **Test Multiple Products:**
   - Add multiple products to cart
   - Verify total amount calculation

3. **Test Failed Payments:**
   - Use invalid phone numbers to test failure flow
   - Verify order status remains PENDING or becomes FAILED

4. **Check Logs:**
   - Watch application logs for Paystack API responses
   - Look for any error messages

5. **Paystack Dashboard:**
   - View transactions in Paystack dashboard
   - Check webhook deliveries
   - Monitor test payments

---

## 🔗 Quick Links

- **Paystack Dashboard:** https://dashboard.paystack.com/
- **Paystack API Docs:** https://paystack.com/docs/api/
- **M-Pesa Integration:** https://paystack.com/docs/payments/mobile-money/
- **Test Data:** https://paystack.com/docs/payments/test-payments/

---

## 📞 Need Help?

If you encounter issues:
1. Check application logs: `mvn spring-boot:run`
2. Review `TROUBLESHOOTING_400_ERROR.md`
3. Check Paystack dashboard for transaction details
4. Verify API key is correct in `application.properties`

---

**Happy Testing! 🎉**
