# Troubleshooting: Paystack 400 Bad Request Error

## Error Details

**Error:** `400 Bad Request from POST https://api.paystack.co/transaction/initialize`

This error typically means the request format doesn't match Paystack's API requirements.

---

## What We Fixed

### 1. ✅ JSON Field Naming
**Issue:** Paystack API expects `callback_url` (snake_case), not `callbackUrl` (camelCase)

**Fixed:** Added `@JsonProperty("callback_url")` annotation to the DTO

### 2. ✅ Enhanced Error Logging
Added detailed error response capture to see exactly what Paystack is complaining about.

---

## Common Causes of 400 Bad Request

### 1. Invalid API Key
**Check:** Your API key format
```properties
# Must start with sk_test_ or sk_live_
paystack.api.key=sk_test_3f4ed3ee01e4b3ea1dcf91b00caecdbadd0c8ade
```

**Verify:** 
- Go to https://dashboard.paystack.com/#/settings/developers
- Copy the exact Secret Key (not Public Key!)
- Test mode: starts with `sk_test_`
- Live mode: starts with `sk_live_`

### 2. Invalid Email Format
**Paystack requires:** Valid email address

**Fix:** Ensure email is provided in request:
```json
{
  "email": "customer@example.com"  // Required!
}
```

### 3. Invalid Amount
**Paystack requires:** Positive integer in smallest currency unit

**Correct:**
```json
{
  "amount": 10000  // 100 KES (in cents)
}
```

**Incorrect:**
```json
{
  "amount": 100.50  // ❌ No decimals!
}
```

### 4. Unsupported Currency
**For MPESA:** Use "KES" (Kenya Shillings)

**Supported currencies:**
- KES (Kenya)
- NGN (Nigeria)
- GHS (Ghana)
- ZAR (South Africa)
- USD (US Dollar)

### 5. Invalid Callback URL
**Must be:** Valid HTTP/HTTPS URL

**Correct:**
```properties
paystack.callback.url=http://localhost:8080/api/checkout/paystack/callback
```

**Incorrect:**
```properties
paystack.callback.url=callback  // ❌ Must be full URL
```

---

## Debugging Steps

### Step 1: Restart Application
The new code with better error logging needs to be loaded:

```powershell
# Stop your application (Ctrl+C)
# Then restart:
cd "C:\Users\LENOVO\OneDrive\PERSONAL PROJECTS 2026\personal 2026\e_commerce_V2\clothes-shop"
./mvnw spring-boot:run
```

### Step 2: Check Logs on Startup
You should see:
```
Paystack API initialized successfully with key: sk_test_****
```

If you see this, the API key is at least in the correct format.

### Step 3: Test with Script
Run the test script:
```powershell
.\test-paystack.ps1
```

### Step 4: Check Application Logs
Now when you get 400 error, you'll see the actual Paystack error message:
```
Paystack API error response: {"status":false,"message":"Actual error here"}
```

### Step 5: Verify Request Format
The logs will show:
```
Sending request to Paystack: PaystackInitializeRequest(amount=10000, email=test@example.com, ...)
```

---

## Testing Your API Key

### Quick Test (Direct Paystack API)
Test if your API key works directly with Paystack:

```powershell
$headers = @{
    "Authorization" = "Bearer sk_test_3f4ed3ee01e4b3ea1dcf91b00caecdbadd0c8ade"
    "Content-Type" = "application/json"
}

$body = @{
    email = "customer@email.com"
    amount = 10000
    currency = "KES"
} | ConvertTo-Json

Invoke-RestMethod -Method Post `
    -Uri "https://api.paystack.co/transaction/initialize" `
    -Headers $headers `
    -Body $body
```

**If this works:** Your key is valid, issue is in application code
**If this fails:** Your key is invalid, get a new one from dashboard

---

## Expected Successful Response

When it works, you'll see:

```json
{
  "status": true,
  "message": "Authorization URL created",
  "data": {
    "authorization_url": "https://checkout.paystack.com/xxxxx",
    "access_code": "xxxxx",
    "reference": "ORDER-uuid"
  }
}
```

---

## API Key Issues

### Get a Fresh Key

1. **Login to Paystack:**
   https://dashboard.paystack.com/login

2. **Go to Settings → API Keys & Webhooks:**
   https://dashboard.paystack.com/#/settings/developers

3. **Test Mode:**
   - Click the eye icon to reveal Secret Key
   - Copy the key starting with `sk_test_`

4. **Update Configuration:**
   ```properties
   paystack.api.key=sk_test_YOUR_NEW_KEY_HERE
   ```

5. **Restart Application**

### Key Format Verification

Valid test key example:
```
sk_test_b9d5cacf0e349460ce3c607be17aad9ee240ad24
```

Pattern:
- Starts with: `sk_test_`
- Followed by: 40 hexadecimal characters
- Total length: ~48 characters

---

## Request Payload Checklist

Your request must include:

- [ ] `email` - Valid email (required)
- [ ] `amount` - Positive integer (required)
- [ ] `currency` - Valid 3-letter code (optional, defaults to NGN)
- [ ] `reference` - Unique transaction ID (optional, auto-generated)
- [ ] `callback_url` - Valid URL (optional)

Example valid request:
```json
{
  "email": "customer@example.com",
  "amount": 10000,
  "currency": "KES",
  "reference": "ORDER-12345",
  "callback_url": "http://localhost:8080/api/checkout/paystack/callback"
}
```

---

## After Fixing

### Restart and Test

1. **Restart application** to load new code
2. **Run test:** `.\test-paystack.ps1`
3. **Check logs** for detailed error message
4. **Verify** the Paystack error response

### If Still Failing

Check the logs for:
```
Paystack API error response: {"status":false,"message":"ACTUAL_ERROR_HERE"}
```

Common error messages:
- **"email is required"** → Add email to request
- **"amount is required"** → Add amount to request
- **"Invalid authorization header"** → Check API key
- **"amount must be numeric"** → Amount should be integer, not string

---

## Next Steps

1. ✅ Code is updated and compiled
2. 🔄 Restart your application
3. 🧪 Run the test script: `.\test-paystack.ps1`
4. 📋 Check application logs for detailed error
5. 🔍 Share the "Paystack API error response" if still failing

---

## Quick Commands

```powershell
# Restart application
./mvnw spring-boot:run

# Run test (in new terminal)
.\test-paystack.ps1

# Check if app is running
curl http://localhost:8080/actuator/health

# Manual test
Invoke-RestMethod -Method Post -Uri "http://localhost:8080/api/checkout/payment-intent" `
  -ContentType "application/json" `
  -Body '{"amount": 10000, "currency": "KES", "email": "test@example.com"}'
```

---

## Support

If the error persists after these fixes:
1. Check the detailed error message in logs
2. Verify your Paystack account is activated
3. Contact Paystack support: support@paystack.com
4. Share the exact error message from logs

The enhanced error logging will now show you exactly what Paystack is complaining about!

