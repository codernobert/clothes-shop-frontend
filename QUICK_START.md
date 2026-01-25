# 🚀 Quick Start - Paystack MPESA Payments

## Test Payment Right Now

### 1. Start Your Application
```powershell
cd "C:\Users\LENOVO\OneDrive\PERSONAL PROJECTS 2026\personal 2026\e_commerce_V2\clothes-shop"
./mvnw spring-boot:run
```

### 2. Initialize a Payment (Using Your Configured API Key)
```bash
curl -X POST http://localhost:8080/api/checkout/payment-intent \
  -H "Content-Type: application/json" \
  -d "{\"amount\": 10000, \"currency\": \"KES\", \"email\": \"test@example.com\", \"description\": \"Test Order\"}"
```

**PowerShell version:**
```powershell
Invoke-RestMethod -Method Post -Uri "http://localhost:8080/api/checkout/payment-intent" `
  -ContentType "application/json" `
  -Body '{"amount": 10000, "currency": "KES", "email": "test@example.com", "description": "Test Order"}'
```

### 3. Expected Response
```json
{
  "success": true,
  "message": "Payment initialized successfully",
  "data": {
    "clientSecret": "access_code_xyz",
    "paymentIntentId": "ORDER-uuid-here",
    "status": "initialized",
    "authorizationUrl": "https://checkout.paystack.com/xxxxx"
  }
}
```

### 4. Visit the Authorization URL
Copy the `authorizationUrl` and open it in your browser. You'll see payment options including:
- 💳 Card
- 📱 Mobile Money (MPESA)
- 🏦 Bank Transfer
- 📞 USSD

### 5. For MPESA:
1. Select "Mobile Money" or "MPESA"
2. Enter phone number: `254XXXXXXXXX`
3. You'll receive MPESA prompt
4. Enter your PIN
5. Payment complete!

---

## API Key Status
✅ **Configured:** `sk_test_3f4ed3ee01e4b3ea1dcf91b00caecdbadd0c8ade`

If this key doesn't work:
1. Go to: https://dashboard.paystack.com/#/settings/developers
2. Get your Secret Key (Test mode)
3. Update in: `src/main/resources/application.properties`

---

## Amount Conversion Helper

| You Want | Send to API |
|----------|-------------|
| 100 KES  | 10000       |
| 500 KES  | 50000       |
| 1,000 KES| 100000      |
| 5,000 KES| 500000      |

**Formula:** Multiply by 100

---

## Complete Payment Flow Test

### Step 1: Create Order
```bash
POST http://localhost:8080/api/checkout
{
  "userId": 1,
  "shippingAddress": "123 Test St, Nairobi",
  "paymentMethod": "PAYSTACK"
}
```

### Step 2: Initialize Payment
```bash
POST http://localhost:8080/api/checkout/payment-intent
{
  "amount": 50000,
  "currency": "KES",
  "email": "customer@example.com",
  "description": "Order from cart"
}
```

### Step 3: Get Authorization URL
From response, copy `authorizationUrl`

### Step 4: User Pays
Open authorization URL → Select MPESA → Complete payment

### Step 5: Verify Payment
```bash
POST http://localhost:8080/api/checkout/verify-payment?reference=ORDER-xxx
```

### Step 6: Confirm Order
```bash
POST http://localhost:8080/api/checkout/confirm-payment/1?reference=ORDER-xxx
```

---

## Troubleshooting Commands

### Check if app is running:
```powershell
curl http://localhost:8080/actuator/health
```

### View logs:
```powershell
# Check for "Paystack API initialized successfully"
```

### Test with minimal data:
```powershell
Invoke-RestMethod -Uri "http://localhost:8080/api/checkout/payment-intent" `
  -Method Post -ContentType "application/json" `
  -Body '{"amount":10000,"currency":"KES","email":"test@test.com"}'
```

---

## Production Checklist

When deploying to production:

- [ ] Get live API key from Paystack
- [ ] Update `paystack.api.key` with `sk_live_...`
- [ ] Update `paystack.callback.url` to production URL
- [ ] Test with small real transaction
- [ ] Monitor Paystack dashboard
- [ ] Set up webhooks for automatic notifications

---

## Need Help?

📖 **Full Guide:** See `PAYSTACK_INTEGRATION.md` (301 lines)
📋 **Migration Details:** See the summary I just showed
🌐 **Paystack Docs:** https://paystack.com/docs/

---

✅ **Status:** Ready to accept MPESA payments!
🔧 **Build:** Successful
📱 **MPESA:** Supported for KES currency

