# 🎨 Deploy Frontend to Railway - Step by Step

## ✅ Prerequisites
- Backend deployed at: `https://clothes-shop-backend-production.up.railway.app/`
- Backend health check: ✅ UP
- GitHub repository with frontend code
- Railway account

---

## 🚀 Step-by-Step Deployment

### 1️⃣ Push Frontend to GitHub

```powershell
# Navigate to your frontend directory
cd "C:\Users\LENOVO\OneDrive\PERSONAL PROJECTS 2026\personal 2026\e_commerce_V2\frontend\clothes-shop-frontend"

# Add all files
git add .

# Commit changes
git commit -m "Prepare frontend for Railway deployment"

# Push to GitHub
git push origin main
```

---

### 2️⃣ Create Railway Project for Frontend

1. Go to https://railway.app/new
2. Click **"Deploy from GitHub repo"**
3. Select your **frontend repository**
4. Railway will auto-detect PHP project

---

### 3️⃣ Configure Environment Variables

In Railway project settings, click **"Variables"** and add:

```env
API_BASE_URL=https://clothes-shop-backend-production.up.railway.app/api
PAYSTACK_PUBLIC_KEY=pk_test_your_public_key_here
```

**Important:** Replace `PAYSTACK_PUBLIC_KEY` with your actual Paystack public key.

---

### 4️⃣ Configure Build Settings (if needed)

Railway should auto-detect PHP, but if you need custom settings:

1. In Railway project → **Settings**
2. **Build Command:** (leave empty - Railway handles PHP automatically)
3. **Start Command:** (leave empty - Railway uses default PHP server)

---

### 5️⃣ Deploy & Get URL

- Railway deploys automatically after detecting changes
- Once deployed, you'll get a URL like: `https://clothes-shop-frontend-production.up.railway.app`
- Click on the generated URL to access your frontend

---

### 6️⃣ Update Backend CORS Settings

**IMPORTANT:** Your backend needs to allow requests from the frontend domain.

Update your backend's `WebConfig.java`:

```java
@Configuration
public class WebConfig implements WebMvcConfigurer {
    @Override
    public void addCorsMappings(CorsRegistry registry) {
        registry.addMapping("/**")
                .allowedOrigins(
                    "http://localhost:3000",
                    "https://clothes-shop-frontend-production.up.railway.app"  // Add your Railway URL
                )
                .allowedMethods("GET", "POST", "PUT", "DELETE", "OPTIONS")
                .allowedHeaders("*")
                .allowCredentials(true);
    }
}
```

After updating, commit and push to trigger backend redeployment.

---

### 7️⃣ Update Paystack Callback URL

In your Railway **backend** environment variables, update:

```env
PAYSTACK_CALLBACK_URL=https://clothes-shop-frontend-production.up.railway.app/frontend/payment_callback.php
```

Also update in your Paystack dashboard:
1. Go to https://dashboard.paystack.com/
2. Settings → API Keys & Webhooks
3. Update **Callback URL** to your Railway frontend URL

---

## ✅ Verification Checklist

Test these in order:

- [ ] **Frontend loads:** Open your Railway frontend URL
- [ ] **Products display:** Homepage shows products from backend
- [ ] **Products page works:** Navigate to products page
- [ ] **Add to cart works:** Click add to cart button
- [ ] **Cart page loads:** View cart with items
- [ ] **Checkout works:** Navigate through checkout
- [ ] **Payment integration:** Test Paystack payment flow
- [ ] **Order confirmation:** Verify order appears in admin

---

## 🔍 Testing URLs

Once deployed, test these endpoints:

```
Frontend:
- Homepage: https://your-frontend.railway.app/
- Products: https://your-frontend.railway.app/frontend/products.php
- Cart: https://your-frontend.railway.app/frontend/cart.php
- Admin: https://your-frontend.railway.app/frontend/admin/

Backend:
- Health: https://clothes-shop-backend-production.up.railway.app/actuator/health
- Products API: https://clothes-shop-backend-production.up.railway.app/api/products
```

---

## 📁 Files Created for Railway

These files have been created to support Railway deployment:

- ✅ `composer.json` - PHP version and dependencies
- ✅ `index.php` (root) - Redirects to frontend folder
- ✅ `nginx.conf` - Server configuration
- ✅ `.env.example` - Environment variable template
- ✅ `frontend/config.php` - Updated to use environment variables

---

## 🐛 Troubleshooting

### Frontend Not Loading?
```powershell
# Check Railway logs
# In Railway Dashboard → Your Service → Logs
```

### Products Not Showing?
1. Check browser console for CORS errors
2. Verify `API_BASE_URL` environment variable
3. Ensure backend CORS allows your frontend domain
4. Test backend API directly: `https://clothes-shop-backend-production.up.railway.app/api/products`

### CSS/Images Not Loading?
- Check that paths are relative (not absolute)
- Verify files are committed to GitHub
- Check Railway deployment logs for errors

### Session Issues?
- Railway PHP includes session support by default
- Check that `session_start()` is called in each page
- Verify session files are writable

### Payment Not Working?
1. Check Paystack public key is correct
2. Verify callback URL points to Railway frontend
3. Check backend has correct Paystack secret key
4. Test with Paystack test cards

---

## 💰 Cost Estimate

**Railway Pricing:**
- **Hobby Plan:** $5/month (includes $5 credit)
- **Frontend:** ~$0-2/month (static/PHP app is lightweight)
- **Backend + Database:** ~$5-8/month (Java app + PostgreSQL)
- **Total:** ~$5-10/month for both frontend and backend

**Free Tier:**
- Railway offers $5 credit monthly on Hobby plan
- Suitable for testing and small projects

---

## 🎯 Quick Commands

```powershell
# Deploy frontend
git add . ; git commit -m "Deploy frontend" ; git push origin main

# View Railway logs (in dashboard)
# Railway Dashboard → Your Project → Logs tab

# Test API connection
curl https://clothes-shop-backend-production.up.railway.app/api/products
```

---

## 📊 Monitoring

Monitor your frontend:

1. **Railway Dashboard:**
   - View logs in real-time
   - Check memory and CPU usage
   - Monitor request counts

2. **Browser Console:**
   - Check for JavaScript errors
   - Monitor API calls
   - Verify no CORS issues

3. **Backend Health:**
   - https://clothes-shop-backend-production.up.railway.app/actuator/health

---

## 🚀 Next Steps After Deployment

1. ✅ Test complete user journey (browse → add to cart → checkout → payment)
2. ✅ Switch to production Paystack keys (not test keys)
3. ✅ Set up custom domain (optional)
4. ✅ Configure SSL (Railway provides free SSL)
5. ✅ Set up monitoring and alerts
6. ✅ Create backup strategy for database
7. ✅ Document API endpoints
8. ✅ Set up error logging

---

## 🔗 Important Links

- **Backend URL:** https://clothes-shop-backend-production.up.railway.app/
- **Backend Health:** https://clothes-shop-backend-production.up.railway.app/actuator/health
- **Railway Dashboard:** https://railway.app/dashboard
- **Paystack Dashboard:** https://dashboard.paystack.com/

---

## 🆘 Need Help?

**Common Issues:**
1. **CORS errors:** Update backend WebConfig.java with frontend URL
2. **API not connecting:** Verify API_BASE_URL environment variable
3. **Payment fails:** Check Paystack keys and callback URL
4. **Session issues:** Ensure session_start() is called on each page

**Railway Support:**
- Documentation: https://docs.railway.app/
- Discord: https://discord.gg/railway
- Twitter: @Railway

---

## ✨ Success Indicators

Your deployment is successful when:

✅ Frontend loads without errors  
✅ Products display from backend API  
✅ Cart functionality works  
✅ Admin panel is accessible  
✅ Payment flow completes  
✅ Orders appear in admin dashboard  

---

**🎉 Happy Deploying!**

*Last Updated: January 26, 2026*
