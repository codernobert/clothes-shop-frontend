# 🛍️ Customer Journey - Postman API Documentation

## Complete E-Commerce Flow: Browse → Cart → Checkout with MPESA

This guide walks you through a complete customer journey using Postman to test all endpoints.

---

## 📋 Table of Contents

1. [Setup](#setup)
2. [Step 1: Browse Products](#step-1-browse-products)
3. [Step 2: View Product Details](#step-2-view-product-details)
4. [Step 3: Add Products to Cart](#step-3-add-products-to-cart)
5. [Step 4: View Cart](#step-4-view-cart)
6. [Step 5: Update Cart Item Quantity](#step-5-update-cart-item-quantity)
7. [Step 6: Create Order](#step-6-create-order)
8. [Step 7: Initialize Payment (MPESA)](#step-7-initialize-payment-mpesa)
9. [Step 8: Complete Payment](#step-8-complete-payment)
10. [Step 9: Verify Payment](#step-9-verify-payment)
11. [Step 10: Confirm Order Payment](#step-10-confirm-order-payment)
12. [Step 11: View Order Details](#step-11-view-order-details)

---

## Setup

### Base URL
```
http://localhost:8080/api
```

### Prerequisites
1. ✅ Application is running
2. ✅ PostgreSQL database is up
3. ✅ Products exist in database
4. ✅ Paystack API key is configured

### Check Application Health
**Endpoint:** `GET http://localhost:8080/actuator/health`

**Expected Response:**
```json
{
  "status": "UP"
}
```

---

## Step 1: Browse Products

### Get All Products

**Method:** `GET`  
**URL:** `http://localhost:8080/api/products`

**Headers:**
```
Content-Type: application/json
```

**Response:**
```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Classic T-Shirt",
      "description": "Comfortable cotton t-shirt",
      "price": 1500,
      "category": "TOPS",
      "brand": "Nike",
      "size": "M",
      "color": "Blue",
      "gender": "UNISEX",
      "stock": 50,
      "imageUrl": "https://example.com/tshirt.jpg",
      "active": true,
      "createdAt": "2026-01-08T10:00:00",
      "updatedAt": "2026-01-08T10:00:00"
    },
    {
      "id": 2,
      "name": "Denim Jeans",
      "description": "Classic blue denim jeans",
      "price": 3500,
      "category": "BOTTOMS",
      "brand": "Levi's",
      "size": "32",
      "color": "Blue",
      "gender": "MALE",
      "stock": 30,
      "imageUrl": "https://example.com/jeans.jpg",
      "active": true,
      "createdAt": "2026-01-08T10:00:00",
      "updatedAt": "2026-01-08T10:00:00"
    }
  ]
}
```

**Save for later:**
- Note the `id` of products you want to purchase
- Check the `price` (in smallest currency unit - cents for KES)
- Verify `stock` is available

---

### Search Products by Keyword

**Method:** `GET`  
**URL:** `http://localhost:8080/api/products/search?keyword=shirt`

**Query Parameters:**
- `keyword` (required) - Search term

**Response:**
```json
{
  "success": true,
  "message": "Search results",
  "data": [
    {
      "id": 1,
      "name": "Classic T-Shirt",
      "price": 1500,
      ...
    }
  ]
}
```

---

### Filter Products

**Method:** `GET`  
**URL:** `http://localhost:8080/api/products/filter`

**Query Parameters:**
- `category` (optional) - TOPS, BOTTOMS, DRESSES, OUTERWEAR, SHOES, ACCESSORIES
- `brand` (optional) - Nike, Adidas, Levi's, etc.
- `minPrice` (optional) - Minimum price in cents
- `maxPrice` (optional) - Maximum price in cents
- `gender` (optional) - MALE, FEMALE, UNISEX

**Example:**
```
GET http://localhost:8080/api/products/filter?category=TOPS&minPrice=1000&maxPrice=5000
```

**Response:** Same format as "Get All Products"

---

## Step 2: View Product Details

**Method:** `GET`  
**URL:** `http://localhost:8080/api/products/{id}`

**Example:**
```
GET http://localhost:8080/api/products/1
```

**Response:**
```json
{
  "success": true,
  "message": "Product found",
  "data": {
    "id": 1,
    "name": "Classic T-Shirt",
    "description": "Comfortable cotton t-shirt",
    "price": 1500,
    "category": "TOPS",
    "brand": "Nike",
    "size": "M",
    "color": "Blue",
    "gender": "UNISEX",
    "stock": 50,
    "imageUrl": "https://example.com/tshirt.jpg",
    "active": true,
    "createdAt": "2026-01-08T10:00:00",
    "updatedAt": "2026-01-08T10:00:00"
  }
}
```

---

## Step 3: Add Products to Cart

### Add First Product

**Method:** `POST`  
**URL:** `http://localhost:8080/api/cart/1/items`

**Path Parameters:**
- `userId` = 1 (or your user ID)

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "productId": 1,
  "quantity": 2
}
```

**Response:**
```json
{
  "success": true,
  "message": "Item added to cart",
  "data": {
    "id": 1,
    "userId": 1,
    "productId": 1,
    "quantity": 2,
    "createdAt": "2026-01-08T15:30:00",
    "updatedAt": "2026-01-08T15:30:00"
  }
}
```

**Save for later:** Note the cart item `id`

---

### Add Second Product

**Method:** `POST`  
**URL:** `http://localhost:8080/api/cart/1/items`

**Request Body:**
```json
{
  "productId": 2,
  "quantity": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "Item added to cart",
  "data": {
    "id": 2,
    "userId": 1,
    "productId": 2,
    "quantity": 1,
    "createdAt": "2026-01-08T15:31:00",
    "updatedAt": "2026-01-08T15:31:00"
  }
}
```

---

## Step 4: View Cart

**Method:** `GET`  
**URL:** `http://localhost:8080/api/cart/1`

**Path Parameters:**
- `userId` = 1

**Response:**
```json
{
  "success": true,
  "message": "Cart retrieved",
  "data": {
    "userId": 1,
    "items": [
      {
        "id": 1,
        "productId": 1,
        "productName": "Classic T-Shirt",
        "price": 1500,
        "quantity": 2,
        "subtotal": 3000
      },
      {
        "id": 2,
        "productId": 2,
        "productName": "Denim Jeans",
        "price": 3500,
        "quantity": 1,
        "subtotal": 3500
      }
    ],
    "totalAmount": 6500
  }
}
```

**Calculate Total:**
- T-Shirt: 1500 × 2 = 3000
- Jeans: 3500 × 1 = 3500
- **Total: 6500 (KES 65.00)**

---

## Step 5: Update Cart Item Quantity

### Increase Quantity

**Method:** `PUT`  
**URL:** `http://localhost:8080/api/cart/1/items/1`

**Path Parameters:**
- `userId` = 1
- `itemId` = 1 (cart item ID)

**Request Body:**
```json
{
  "quantity": 3
}
```

**Response:**
```json
{
  "success": true,
  "message": "Cart item updated",
  "data": {
    "id": 1,
    "productId": 1,
    "quantity": 3,
    "updatedAt": "2026-01-08T15:35:00"
  }
}
```

---

### Remove Item from Cart (Optional)

**Method:** `DELETE`  
**URL:** `http://localhost:8080/api/cart/1/items/2`

**Path Parameters:**
- `userId` = 1
- `itemId` = 2

**Response:**
```json
{
  "success": true,
  "message": "Item removed from cart",
  "data": null
}
```

---

## Step 6: Create Order

**Method:** `POST`  
**URL:** `http://localhost:8080/api/checkout`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "userId": 1,
  "shippingAddress": "123 Main Street, Nairobi, Kenya",
  "paymentMethod": "PAYSTACK"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "orderId": 1,
    "orderNumber": "ORD-2026-0001",
    "userId": 1,
    "orderStatus": "PENDING",
    "paymentStatus": "PENDING",
    "totalAmount": 6500,
    "shippingAddress": "123 Main Street, Nairobi, Kenya",
    "paymentMethod": "PAYSTACK",
    "items": [
      {
        "productId": 1,
        "productName": "Classic T-Shirt",
        "quantity": 3,
        "price": 1500,
        "subtotal": 4500
      },
      {
        "productId": 2,
        "productName": "Denim Jeans",
        "quantity": 1,
        "price": 3500,
        "subtotal": 3500
      }
    ],
    "createdAt": "2026-01-08T15:40:00"
  }
}
```

**Save for later:** Note the `orderId` (1) and `orderNumber` (ORD-2026-0001)

---

## Step 7: Initialize Payment (MPESA)

**Method:** `POST`  
**URL:** `http://localhost:8080/api/checkout/payment-intent`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "amount": 6500,
  "currency": "KES",
  "email": "customer@example.com",
  "description": "Order #ORD-2026-0001"
}
```

**Field Descriptions:**
- `amount` - Total order amount in cents (6500 = KES 65.00)
- `currency` - "KES" for Kenya Shillings (MPESA), "NGN" for Nigeria, etc.
- `email` - Customer email address (required by Paystack)
- `description` - Order description

**Response:**
```json
{
  "success": true,
  "message": "Payment initialized successfully",
  "data": {
    "clientSecret": "x5k7m9n2p4q6r8s0",
    "paymentIntentId": "ORDER-550e8400-e29b-41d4-a716-446655440000",
    "status": "initialized",
    "authorizationUrl": "https://checkout.paystack.com/x5k7m9n2p4q6r8s0"
  }
}
```

**Save for later:**
- `paymentIntentId` (reference) - "ORDER-550e8400-e29b-41d4-a716-446655440000"
- `authorizationUrl` - Use this to complete payment

---

## Step 8: Complete Payment

### Option A: Manual Payment (Browser)

1. **Copy the `authorizationUrl`** from Step 7 response
2. **Open in browser:** `https://checkout.paystack.com/x5k7m9n2p4q6r8s0`
3. **Select Payment Method:**
   - 📱 **Mobile Money (MPESA)** - For Kenya
   - 💳 **Card** - Visa/Mastercard
   - 🏦 **Bank Transfer**
   - 📞 **USSD**

4. **For MPESA:**
   - Enter phone number: `254XXXXXXXXX`
   - Receive MPESA prompt on phone
   - Enter PIN to authorize payment
   - Wait for confirmation

5. **Payment Complete!** - Paystack will redirect to your callback URL

---

### Option B: Test Payment (Test Mode)

For testing in Paystack test mode:

**Test Card Numbers:**
```
Card Number: 4084084084084081
Expiry: 01/30
CVV: 408
PIN: 0000
OTP: 123456
```

**Test MPESA:** Use Paystack's test environment simulation

---

## Step 9: Verify Payment

**Method:** `POST`  
**URL:** `http://localhost:8080/api/checkout/verify-payment`

**Query Parameters:**
- `reference` - The `paymentIntentId` from Step 7

**Example:**
```
POST http://localhost:8080/api/checkout/verify-payment?reference=ORDER-550e8400-e29b-41d4-a716-446655440000
```

**Headers:**
```
Content-Type: application/json
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Payment verified successfully",
  "data": true
}
```

**Response (Failed/Pending):**
```json
{
  "success": false,
  "message": "Payment verification failed",
  "data": false
}
```

---

## Step 10: Confirm Order Payment

**Method:** `POST`  
**URL:** `http://localhost:8080/api/checkout/confirm-payment/1`

**Path Parameters:**
- `orderId` = 1 (from Step 6)

**Query Parameters:**
- `reference` - The `paymentIntentId` from Step 7

**Example:**
```
POST http://localhost:8080/api/checkout/confirm-payment/1?reference=ORDER-550e8400-e29b-41d4-a716-446655440000
```

**Headers:**
```
Content-Type: application/json
```

**Response:**
```json
{
  "success": true,
  "message": "Payment confirmed",
  "data": {
    "orderId": 1,
    "orderNumber": "ORD-2026-0001",
    "orderStatus": "CONFIRMED",
    "paymentStatus": "COMPLETED",
    "paymentIntentId": "ORDER-550e8400-e29b-41d4-a716-446655440000",
    "totalAmount": 6500,
    "paidAt": "2026-01-08T15:45:00"
  }
}
```

---

## Step 11: View Order Details

**Method:** `GET`  
**URL:** `http://localhost:8080/api/orders/1`

**Path Parameters:**
- `orderId` = 1

**Response:**
```json
{
  "success": true,
  "message": "Order found",
  "data": {
    "orderId": 1,
    "orderNumber": "ORD-2026-0001",
    "userId": 1,
    "orderStatus": "CONFIRMED",
    "paymentStatus": "COMPLETED",
    "totalAmount": 6500,
    "shippingAddress": "123 Main Street, Nairobi, Kenya",
    "paymentMethod": "PAYSTACK",
    "paymentIntentId": "ORDER-550e8400-e29b-41d4-a716-446655440000",
    "items": [
      {
        "productId": 1,
        "productName": "Classic T-Shirt",
        "quantity": 3,
        "price": 1500,
        "subtotal": 4500
      },
      {
        "productId": 2,
        "productName": "Denim Jeans",
        "quantity": 1,
        "price": 3500,
        "subtotal": 3500
      }
    ],
    "createdAt": "2026-01-08T15:40:00",
    "updatedAt": "2026-01-08T15:45:00",
    "paidAt": "2026-01-08T15:45:00"
  }
}
```

---

## 📊 Complete Journey Summary

| Step | Action | Endpoint | Method | Key Data to Save |
|------|--------|----------|--------|------------------|
| 1 | Browse Products | `/api/products` | GET | Product IDs |
| 2 | View Product | `/api/products/{id}` | GET | Price, Stock |
| 3 | Add to Cart | `/api/cart/1/items` | POST | Cart Item ID |
| 4 | View Cart | `/api/cart/1` | GET | Total Amount |
| 5 | Update Quantity | `/api/cart/1/items/{id}` | PUT | - |
| 6 | Create Order | `/api/checkout` | POST | Order ID, Order Number |
| 7 | Initialize Payment | `/api/checkout/payment-intent` | POST | Payment Reference, Auth URL |
| 8 | Complete Payment | Open Auth URL in Browser | - | - |
| 9 | Verify Payment | `/api/checkout/verify-payment` | POST | Payment Status |
| 10 | Confirm Order | `/api/checkout/confirm-payment/{id}` | POST | - |
| 11 | View Order | `/api/orders/{id}` | GET | Order Details |

---

## 🎯 Quick Test Flow (Copy-Paste Ready)

### 1. Browse Products
```
GET http://localhost:8080/api/products
```

### 2. Add to Cart
```
POST http://localhost:8080/api/cart/1/items
Content-Type: application/json

{
  "productId": 1,
  "quantity": 2
}
```

### 3. View Cart
```
GET http://localhost:8080/api/cart/1
```

### 4. Create Order
```
POST http://localhost:8080/api/checkout
Content-Type: application/json

{
  "userId": 1,
  "shippingAddress": "123 Main St, Nairobi",
  "paymentMethod": "PAYSTACK"
}
```

### 5. Initialize Payment
```
POST http://localhost:8080/api/checkout/payment-intent
Content-Type: application/json

{
  "amount": 6500,
  "currency": "KES",
  "email": "customer@example.com",
  "description": "Order payment"
}
```

### 6. Verify Payment
```
POST http://localhost:8080/api/checkout/verify-payment?reference=ORDER-xxx-xxx-xxx
```

### 7. Confirm Order
```
POST http://localhost:8080/api/checkout/confirm-payment/1?reference=ORDER-xxx-xxx-xxx
```

---

## 💡 Important Notes

### Amount Format
- **Always use smallest currency unit (cents/kobo)**
- KES 1.00 = 100 (in API)
- KES 65.00 = 6500 (in API)
- **Formula:** `amount_in_api = actual_amount × 100`

### Currency Codes
- **KES** - Kenya Shillings (MPESA)
- **NGN** - Nigerian Naira
- **GHS** - Ghana Cedis
- **ZAR** - South African Rand
- **USD** - US Dollar

### Payment Status Flow
```
PENDING → INITIALIZED → COMPLETED
              ↓
            FAILED
```

### Order Status Flow
```
PENDING → CONFIRMED → PROCESSING → SHIPPED → DELIVERED
           ↓
       CANCELLED
```

---

## 🐛 Troubleshooting

### Issue: "Product not found"
**Solution:** Check that products exist in database. View all products first.

### Issue: "Cart item not found"
**Solution:** Ensure you're using the correct `userId` and `itemId`.

### Issue: "Payment initialization failed"
**Solution:** 
- Check amount is positive integer
- Verify email is valid
- Ensure Paystack API key is configured

### Issue: "Payment verification failed"
**Solution:**
- Complete payment in browser first
- Use correct payment reference
- Wait a few seconds after payment before verifying

---

## 📱 MPESA Payment Details

### For Kenya (KES Currency)

1. **Initialize payment** with `currency: "KES"`
2. **Open authorization URL** in browser
3. **Select "Mobile Money"** or "MPESA"
4. **Enter phone number:** `254XXXXXXXXX` (Kenya format)
5. **Receive STK Push** on your phone
6. **Enter MPESA PIN** to authorize
7. **Wait for confirmation**
8. **Verify payment** via API

### Test Mode MPESA
In test mode, Paystack simulates MPESA payments without real money.

---

## 🎉 Success Criteria

✅ **Journey Complete When:**
1. Products browsed successfully
2. Items added to cart
3. Cart total calculated correctly
4. Order created with PENDING status
5. Payment initialized with authorization URL
6. Payment completed (in browser)
7. Payment verified as successful
8. Order status updated to CONFIRMED
9. Payment status updated to COMPLETED
10. Order details show complete information

---

## 📥 Postman Collection Import

Create a new Postman collection and import these requests in sequence. Set variables:
- `baseUrl` = `http://localhost:8080/api`
- `userId` = `1`
- `orderId` = Save from Step 6 response
- `paymentReference` = Save from Step 7 response

---

## 🔗 Additional Endpoints

### Get User's Orders
```
GET http://localhost:8080/api/orders/user/1
```

### Get Order by Number
```
GET http://localhost:8080/api/orders/number/ORD-2026-0001
```

### Clear Cart
```
DELETE http://localhost:8080/api/cart/1
```

### Search Products
```
GET http://localhost:8080/api/products/search?keyword=shirt
```

### Filter Products by Category
```
GET http://localhost:8080/api/products/category/TOPS
```

---

## ✨ Complete Example Values

**Example calculation for real scenario:**

Cart Contents:
- Classic T-Shirt (KES 15.00) × 3 = KES 45.00
- Denim Jeans (KES 35.00) × 1 = KES 35.00
- **Total: KES 80.00**

API Values:
- T-Shirt price: `1500` (cents)
- T-Shirt quantity: `3`
- Jeans price: `3500` (cents)
- Jeans quantity: `1`
- **Payment amount: `8000`** (KES 80.00 in cents)

---

**🎊 You're ready to test the complete customer journey with MPESA payments!**

For questions or issues, check the troubleshooting section or application logs.

