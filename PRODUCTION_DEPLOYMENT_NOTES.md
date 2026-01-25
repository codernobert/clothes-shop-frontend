# 🚨 IMPORTANT: Before Deploying to Production

## Callback URL Configuration

### Current (Development)
```properties
paystack.callback.url=http://localhost:8000/payment_callback.php
```

### For Production - MUST CHANGE!
```properties
paystack.callback.url=https://yourdomain.com/payment_callback.php
```

⚠️ Replace `yourdomain.com` with your actual domain name.

---

## Deployment Checklist

### Backend (Spring Boot)
- [ ] Update `paystack.callback.url` to production URL
- [ ] Update Paystack API key to **live** key (starts with `sk_live_`)
- [ ] Update database connection to production database
- [ ] Configure CORS to allow only your frontend domain
- [ ] Enable HTTPS/SSL

### Frontend (PHP)
- [ ] Update `API_BASE_URL` in `config.php` to production backend URL
- [ ] Configure web server (Apache/Nginx) for PHP
- [ ] Enable HTTPS/SSL
- [ ] Test all payment flows on production

### Paystack Dashboard
- [ ] Add production callback URL to allowed origins
- [ ] Configure webhook URL (optional but recommended)
- [ ] Test with live API keys
- [ ] Set up email notifications

---

## Environment-Specific Configuration

### Option 1: Environment Variables (Recommended)
```properties
# application.properties
paystack.callback.url=${PAYSTACK_CALLBACK_URL:http://localhost:8000/payment_callback.php}
```

```bash
# Production
export PAYSTACK_CALLBACK_URL=https://yourdomain.com/payment_callback.php
```

### Option 2: Profile-Based Configuration
```properties
# application-dev.properties
paystack.callback.url=http://localhost:8000/payment_callback.php

# application-prod.properties
paystack.callback.url=https://yourdomain.com/payment_callback.php
```

Run with: `java -jar app.jar --spring.profiles.active=prod`

---

## Security Reminders

### Never Commit:
- ❌ Live Paystack API keys
- ❌ Production database credentials
- ❌ Secret keys or tokens

### Use .gitignore:
```
application-prod.properties
.env
*.secret
```

### Best Practices:
- ✅ Use environment variables for secrets
- ✅ Rotate API keys regularly
- ✅ Monitor payment transactions
- ✅ Set up alerts for failed payments
- ✅ Implement webhook verification
- ✅ Add rate limiting
- ✅ Log all payment attempts

---

## Testing on Production

### Before Going Live:
1. Test with Paystack test keys on staging environment
2. Test successful payment flow
3. Test failed payment scenarios
4. Test callback timeout handling
5. Verify webhook notifications (if implemented)
6. Test order status updates
7. Verify email notifications work

### After Going Live:
1. Monitor logs for errors
2. Check payment success rate
3. Verify all orders are confirmed
4. Set up alerts for anomalies
5. Have rollback plan ready

---

## Paystack Live Mode Differences

### Test Mode (Current):
- Uses `sk_test_...` API key
- Test cards work (4084084084084081)
- No real money charged
- Callback URL can be localhost

### Live Mode (Production):
- Uses `sk_test_...` API key  
- Real payment cards only
- Real money charged
- Callback URL must be public HTTPS

---

## Support & Monitoring

### Paystack Dashboard:
- Monitor transactions: https://dashboard.paystack.com/transactions
- View failed payments
- Download reports
- Configure webhooks

### Application Monitoring:
- Set up application logs
- Monitor error rates
- Track payment success/failure ratio
- Alert on high failure rates

### Customer Support:
- Provide order number for refunds
- Have payment reference lookup
- Monitor support tickets
- Keep audit trail

---

## Quick Reference

| Environment | Callback URL | API Key |
|------------|--------------|---------|
| Development | http://localhost:8000/payment_callback.php | sk_test_... |
| Staging | https://staging.yourdomain.com/payment_callback.php | sk_test_... |
| Production | https://yourdomain.com/payment_callback.php | sk_live_... |

---

**Last Updated:** January 26, 2026  
**Remember:** Always test thoroughly before deploying to production!
