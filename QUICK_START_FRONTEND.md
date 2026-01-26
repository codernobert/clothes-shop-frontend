# 🚀 QUICK START - Deploy Frontend to Railway

## ⚡ Fast Track (5 Minutes)

### 1. Push to GitHub
```powershell
git add .
git commit -m "Deploy frontend to Railway"
git push origin main
```

### 2. Deploy on Railway
1. Go to https://railway.app/new
2. Click "Deploy from GitHub repo"
3. Select your frontend repository
4. Wait for deployment (~2-3 minutes)

### 3. Add Environment Variable
In Railway project → Variables:
```
API_BASE_URL=https://clothes-shop-backend-production.up.railway.app/api
```

### 4. Update Backend CORS
Add your frontend URL to backend's `WebConfig.java`:
```java
.allowedOrigins(
    "http://localhost:3000",
    "https://your-frontend.railway.app"  // 👈 Add this
)
```

### 5. Test
Visit: `https://your-frontend.railway.app`

---

## ✅ What's Already Done

✅ `composer.json` - PHP configuration  
✅ `frontend/config.php` - Uses environment variables  
✅ `index.php` - Root redirect  
✅ `nginx.conf` - Server config  
✅ `.env.example` - Template for env vars  

---

## 🔗 Your URLs

**Backend:**
- URL: https://clothes-shop-backend-production.up.railway.app/
- Health: https://clothes-shop-backend-production.up.railway.app/actuator/health ✅

**Frontend:**
- URL: `https://your-frontend.railway.app` (after deployment)

---

## 📋 Environment Variables to Set

```env
API_BASE_URL=https://clothes-shop-backend-production.up.railway.app/api
PAYSTACK_PUBLIC_KEY=pk_test_your_key_here
```

---

## 🆘 Issues?

**Products not loading?**
→ Update backend CORS with your Railway frontend URL

**Payment not working?**
→ Update Paystack callback URL in backend environment variables

**Need more help?**
→ See `RAILWAY_FRONTEND_DEPLOY.md` for detailed guide

---

**Ready? Run the first command above! 🚀**
