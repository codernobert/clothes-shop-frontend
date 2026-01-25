# 🚀 Quick Start - Postman Customer Journey

## Import Postman Collection

1. Open Postman
2. Click **Import** button
3. Select file: `Ecommerce_Customer_Journey.postman_collection.json`
4. Click **Import**

---

## 📋 Step-by-Step Testing Guide

### Prerequisites
✅ Application running on `http://localhost:8080`
✅ Database has products
✅ Paystack API key configured

---

## 🎯 Complete Journey (11 Steps)

### **STEP 1: Browse Products**
```
GET http://localhost:8080/api/products
```
**Action:** Copy a `productId` from response (e.g., `1`)

---

### **STEP 2: Add to Cart**
```
POST http://localhost:8080/api/cart/1/items
Content-Type: application/json

{
  "productId": 1,
  "quantity": 2
}
```
**Action:** Save the `cartItemId` from response

---

### **STEP 3: View Cart**
```
GET http://localhost:8080/api/cart/1
```
**Action:** Note the `totalAmount` (e.g., `6500` = KES 65.00)

---

### **STEP 4: Create Order**
```
POST http://localhost:8080/api/checkout
Content-Type: application/json

{
  "userId": 1,
  "shippingAddress": "123 Main St, Nairobi, Kenya",
  "paymentMethod": "PAYSTACK"
}
```
**Action:** Save `orderId` from response (e.g., `1`)

---

### **STEP 5: Initialize Payment**
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
**Action:** 
- Copy `paymentIntentId` (the reference)
- Copy `authorizationUrl`

---

### **STEP 6: Complete Payment**
```
Open authorizationUrl in browser
→ Select MPESA / Card
→ Complete payment
```
**For MPESA:**
- Enter phone: `254XXXXXXXXX`
- Authorize on phone
- Wait for confirmation

---

### **STEP 7: Verify Payment**
```
POST http://localhost:8080/api/checkout/verify-payment?reference=ORDER-xxx-xxx-xxx
```
Replace `ORDER-xxx-xxx-xxx` with your `paymentIntentId`

**Expected:** `"data": true` (payment successful)

---

### **STEP 8: Confirm Order**
```
POST http://localhost:8080/api/checkout/confirm-payment/1?reference=ORDER-xxx-xxx-xxx
```
Replace:
- `1` with your `orderId`
- `ORDER-xxx-xxx-xxx` with your `paymentIntentId`

**Expected:** Order status updated to COMPLETED

---

### **STEP 9: View Order**
```
GET http://localhost:8080/api/orders/1
```
Replace `1` with your `orderId`

**Expected:** Complete order details with payment info

---

## 🎨 Postman Collection Features

### Auto-Save Variables
The collection automatically saves:
- ✅ `productId` - From Step 1
- ✅ `cartItemId` - From Step 2
- ✅ `orderId` - From Step 4
- ✅ `paymentReference` - From Step 5

### Console Logging
Check Postman Console to see saved variables:
```
Product ID saved: 1
Cart Item ID saved: 1
Order ID saved: 1
Payment Reference saved: ORDER-xxx-xxx-xxx
Authorization URL: https://checkout.paystack.com/xxxxx
```

---

## 💰 Amount Examples

| Display | API Value | Calculation |
|---------|-----------|-------------|
| KES 10 | 1000 | 10 × 100 |
| KES 65 | 6500 | 65 × 100 |
| KES 100 | 10000 | 100 × 100 |
| KES 500 | 50000 | 500 × 100 |

**Formula:** API amount = Display amount × 100

---

## 🔄 Testing Multiple Items

### Add Multiple Products:

**Item 1:**
```json
{
  "productId": 1,
  "quantity": 2
}
```

**Item 2:**
```json
{
  "productId": 2,
  "quantity": 1
}
```

### Calculate Total:
```
Product 1: 1500 × 2 = 3000
Product 2: 3500 × 1 = 3500
Total: 6500
```

Use `6500` as amount in payment initialization.

---

## 🧪 Test Scenarios

### Scenario 1: Single Item Purchase
1. Add 1 product (qty: 1)
2. Total: Product price
3. Checkout and pay

### Scenario 2: Multiple Items
1. Add product 1 (qty: 2)
2. Add product 2 (qty: 1)
3. View cart (check total)
4. Checkout and pay

### Scenario 3: Update Quantity
1. Add product (qty: 1)
2. Update to qty: 3
3. View cart (verify change)
4. Checkout and pay

### Scenario 4: Remove Item
1. Add product 1
2. Add product 2
3. Remove product 1
4. Checkout with product 2 only

---

## ⚡ Quick Tips

### Use Collection Variables
Set once, use everywhere:
```
{{baseUrl}} = http://localhost:8080/api
{{userId}} = 1
{{orderId}} = Auto-saved
{{paymentReference}} = Auto-saved
```

### Test Order
Execute requests in this sequence:
```
Browse → Cart → Checkout → Payment → Verify → Confirm
```

### Payment Testing
**Test Mode:** Use test cards or MPESA simulation
**Live Mode:** Real payments with real accounts

---

## 🐛 Common Issues

### Issue 1: "Product not found"
**Solution:** Run "Get All Products" first to see available products

### Issue 2: "Cart is empty"
**Solution:** Add products to cart before creating order

### Issue 3: "Payment verification failed"
**Solution:** Complete payment in browser first, then verify

### Issue 4: Variables not saving
**Solution:** Check Postman Console for script errors

---

## 📊 Success Checklist

After complete journey, verify:

- [ ] Products listed successfully
- [ ] Cart shows correct items
- [ ] Cart total calculated correctly
- [ ] Order created with PENDING status
- [ ] Payment initialized with auth URL
- [ ] Payment completed in browser
- [ ] Payment verified as successful
- [ ] Order status = CONFIRMED
- [ ] Payment status = COMPLETED
- [ ] Order details show payment reference

---

## 🎯 Test Data

### Sample Product IDs
```
1, 2, 3, 4, 5
```

### Sample User IDs
```
1, 2, 3
```

### Sample Amounts (KES)
```
1000 = KES 10
6500 = KES 65
10000 = KES 100
50000 = KES 500
```

### Sample Email
```
customer@example.com
testuser@example.com
```

---

## 🌐 MPESA Payment Flow

```
1. Initialize Payment
   ↓
2. Get Authorization URL
   ↓
3. Open URL in Browser
   ↓
4. Select "Mobile Money"
   ↓
5. Enter Phone: 254XXXXXXXXX
   ↓
6. Receive STK Push
   ↓
7. Enter MPESA PIN
   ↓
8. Payment Confirmed
   ↓
9. Redirect to Callback
   ↓
10. Verify in API
```

---

## 📱 Payment Methods Available

When you open authorization URL:

- 📱 **Mobile Money** - MPESA (Kenya), MTN, etc.
- 💳 **Card** - Visa, Mastercard
- 🏦 **Bank Transfer** - Direct bank payment
- 📞 **USSD** - Bank USSD codes

---

## ✨ Pro Tips

1. **Use Environment Variables** - Switch between dev/prod easily
2. **Save Responses** - Keep examples for reference
3. **Use Tests Tab** - Auto-validate responses
4. **Check Console** - See all variable updates
5. **Use Pre-request Scripts** - Generate dynamic data

---

## 🎉 Success!

When you see:
```json
{
  "success": true,
  "message": "Payment confirmed",
  "data": {
    "orderStatus": "CONFIRMED",
    "paymentStatus": "COMPLETED"
  }
}
```

**Your customer journey is complete!** 🎊

---

## 📚 Additional Resources

- **Full Documentation:** `POSTMAN_CUSTOMER_JOURNEY.md`
- **Postman Collection:** `Ecommerce_Customer_Journey.postman_collection.json`
- **Paystack Guide:** `PAYSTACK_INTEGRATION.md`
- **Quick Start:** `QUICK_START.md`

---

**Happy Testing!** 🚀

