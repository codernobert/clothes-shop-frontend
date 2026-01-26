# 🚀 Quick Deploy to Railway - Backend
## Step-by-Step Deployment
### 1. Push to GitHub
```bash
git add .
git commit -m "Prepare for Railway deployment"
git push origin main
```
### 2. Create Railway Project
1. Go to https://railway.app/new
2. Click "Deploy from GitHub repo"
3. Select your backend repository
4. Railway auto-detects Java/Maven project
### 3. Add PostgreSQL Database
1. In Railway project → Click "+ New"
2. Select "Database" → "PostgreSQL"
3. Database credentials auto-injected as env vars
### 4. Configure Environment Variables
In Railway project settings, add:
```
PAYSTACK_API_KEY=sk_live_your_production_key_here
PAYSTACK_CALLBACK_URL=https://your-frontend.railway.app/payment_callback.php
```
Railway auto-provides: `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD`, `PORT`
### 5. Deploy & Test
- Railway deploys automatically
- Get your URL: `https://your-app.railway.app`
- Test: `https://your-app.railway.app/actuator/health`
### 6. Connect Frontend
Update frontend `config.php`:
```php
define('API_BASE_URL', 'https://your-backend.railway.app/api');
```
## ✅ Verification Checklist
- [ ] Health endpoint responds: `/actuator/health`
- [ ] Products endpoint works: `/api/products`
- [ ] Database tables created (check Railway DB Data tab)
- [ ] Payment configuration set (PAYSTACK_API_KEY)
- [ ] Frontend can reach backend API
- [ ] CORS allows frontend domain
## 🔧 Files Created for Railway
- `Procfile` - Start command
- `system.properties` - Java version
- `railway.json` - Railway config
- `.env.example` - Env var template
- `application.properties` - Environment variable support
## 📊 Monitoring
- Logs: Railway Dashboard → Your Service → Logs
- Health: `https://your-app.railway.app/actuator/health`
- Metrics: `https://your-app.railway.app/actuator/metrics`
## 💡 Pro Tips
1. **Database**: Railway PostgreSQL is automatically configured
2. **Auto Deploy**: Push to main branch triggers deployment
3. **Logs**: Check Railway logs for any startup issues
4. **CORS**: Update `WebConfig.java` to allow your frontend URL
5. **Cost**: Start with Hobby plan (` $5/month includes $5 credit)
## 🐛 Common Issues
**App won't start?**
- Check Railway logs for errors
- Verify Java 17 in `system.properties`
- Ensure `mvn clean package` succeeds locally
**Database connection fails?**
- Verify PostgreSQL service is running
- Check env vars are properly set
- Review connection string format
**CORS errors?**
Update `src/main/java/com/ecommerce/clothesshop/config/WebConfig.java`:
```java
.allowedOrigins("https://your-frontend.railway.app")
```
## 📚 Next Steps
1. Deploy frontend to separate Railway project
2. Update frontend API URL
3. Test end-to-end customer journey
4. Switch to production Paystack keys
5. Monitor logs and metrics
**Need help?** Check `RAILWAY_DEPLOYMENT_GUIDE.md` for detailed instructions.
