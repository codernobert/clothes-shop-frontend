# 🚀 QUICK START - M-Pesa Testing (5 Minutes)

## Setup (One-Time)
1. Import `MPESA_Test_Flow.postman_collection.json` into Postman
2. Make sure your Spring Boot app is running
3. Ensure PostgreSQL is running

---

## Testing Flow (Just Click & Run!)

### 1️⃣ Health Check
**Click:** `0. Health Check`  
**Expected:** `{"status":"UP"}`

---

### 2️⃣ Add Product to Database
**Click:** `1. Add Product to DB`  
**What it does:** Creates a T-Shirt (1500 KES) in database  
**Auto-saves:** Product ID

---

### 3️⃣ View Products
**Click:** `2. View Products`  
**What it does:** Shows all available products  
**Check:** You should see the T-Shirt

---

### 4️⃣ Add to Cart
**Click:** `3. Add to Cart`  
**What it does:** Adds 2 T-Shirts to cart  
**Check:** Subtotal = 3000 KES (2 × 1500)

---

### 5️⃣ View Cart
**Click:** `4. View Cart`  
**What it does:** Shows cart summary  
**Auto-saves:** Total amount  
**Check:** Total = 3000 KES

---

### 6️⃣ Create Order
**Click:** `5. Create Order (Checkout)`  
**What it does:** Creates order from cart  
**Auto-saves:** Order ID  
**Check:** Payment Status = PENDING

---

### 7️⃣ Initialize M-Pesa Payment
**Click:** `6. Initialize M-Pesa Payment`  
**What it does:** Gets payment URL from Paystack  
**Auto-saves:** Payment Reference

**📋 FROM CONSOLE OUTPUT:**
1. Copy the **Authorization URL**
2. Open it in your browser

---

### 8️⃣ Complete Payment (BROWSER)

**In Browser:**
1. You'll see Paystack payment page
2. Click **"Mobile Money"** or **"M-Pesa"**
3. Enter phone: `+254708374149` (test number)
4. Click **Pay**
5. On your phone: Enter M-Pesa PIN
6. Wait for confirmation

**⏱️ This takes 10-30 seconds**

---

### 9️⃣ Verify Payment
**Click:** `7. Verify Payment`  
**What it does:** Checks if payment succeeded  
**Expected:** `{"data": true}`

---

### 🔟 Confirm Order Payment
**Click:** `8. Confirm Order Payment`  
**What it does:** Updates order to COMPLETED  
**Check:** Payment Status = COMPLETED

---

### 1️⃣1️⃣ View Final Order
**Click:** `9. View Order Details`  
**What it does:** Shows complete order with payment info  
**Check:** 
- Payment Status = COMPLETED
- Payment Reference exists
- Order Status = CONFIRMED

---

## ✅ Success Checklist

- [ ] Product created (ID saved)
- [ ] Product in cart (qty: 2)
- [ ] Order created (Status: PENDING)
- [ ] Payment initialized (Got URL)
- [ ] Payment completed (In browser)
- [ ] Payment verified (data: true)
- [ ] Order confirmed (Status: COMPLETED)

---

## 🎯 Expected Results

### Before Payment:
```
Order Status: PENDING
Payment Status: PENDING
Payment Reference: null
```

### After Payment:
```
Order Status: CONFIRMED
Payment Status: COMPLETED
Payment Reference: REF-2026-xxxxx
```

---

## 🐛 Quick Troubleshooting

### Problem: Product not showing in cart
- **Fix:** Run "View Products" first, check stock > 0

### Problem: Order creation fails
- **Fix:** Make sure cart has items (run step 4)

### Problem: M-Pesa option not showing
- **Fix:** Check currency is KES, amount > 10

### Problem: Payment verification fails
- **Fix:** Wait 10 seconds after payment, then try again

### Problem: Authorization URL expired
- **Fix:** Run step 6 again to get new URL

---

## 📱 Test Phone Numbers

```
+254708374149  ✅ Always succeeds
+254111222333  ✅ Test number
```

---

## 💰 Amount Conversion

If you see wrong amounts:

**Current:** Amount = 3000 (sent as-is)  
**If needed:** Amount = 300000 (in cents)

Check Paystack docs for Kenya currency format.

---

## 🔗 After Testing

View all orders:
```
GET http://localhost:8080/api/orders/user/1
```

Check Paystack dashboard:
```
https://dashboard.paystack.com/transactions
```

---

**That's it! 🎉 Happy testing!**
