# ✅ Pre-Deployment Checklist

## Before You Deploy - Verify Everything Works Locally

### 1. Backend Verification
- [ ] Backend is running on Railway
- [ ] Backend URL: https://clothes-shop-backend-production.up.railway.app/
- [ ] Health check is UP: https://clothes-shop-backend-production.up.railway.app/actuator/health
- [ ] Products API works: https://clothes-shop-backend-production.up.railway.app/api/products

### 2. Local Frontend Test
- [ ] Start local PHP server: `php -S localhost:8000 -t .`
- [ ] Open: http://localhost:8000/frontend/index.php
- [ ] Products load (they should come from Railway backend)
- [ ] Add to cart works
- [ ] Cart page shows items
- [ ] Checkout page loads

### 3. Code Review
- [ ] No hardcoded URLs (except localhost fallback)
- [ ] `config.php` uses environment variables ✅ (already done)
- [ ] Session starts on each page
- [ ] All files committed to git

### 4. Railway Preparation
- [ ] GitHub repository is up to date
- [ ] All new files added to git
- [ ] Railway account is ready
- [ ] Know which branch to deploy (main or ft-deployment)

---

## Deployment Checklist

### 1. Push Code
```powershell
git add .
git commit -m "Deploy frontend to Railway"
git push origin ft-deployment
```
- [ ] Code pushed successfully
- [ ] No merge conflicts

### 2. Railway Deployment
- [ ] Go to https://railway.app/new
- [ ] Select "Deploy from GitHub repo"
- [ ] Choose your repository
- [ ] Wait for deployment
- [ ] Get Railway URL

### 3. Configuration
- [ ] Add environment variable: `API_BASE_URL=https://clothes-shop-backend-production.up.railway.app/api`
- [ ] Copy your Railway frontend URL
- [ ] Keep it handy for next step

### 4. Backend CORS Update
- [ ] Open backend repository
- [ ] Edit `WebConfig.java`
- [ ] Add Railway frontend URL to `.allowedOrigins()`
- [ ] Commit and push
- [ ] Wait for backend to redeploy

### 5. Testing
- [ ] Open Railway frontend URL
- [ ] Homepage loads
- [ ] Products display
- [ ] Check browser console - no CORS errors
- [ ] Add item to cart
- [ ] View cart
- [ ] Go to checkout
- [ ] Test payment flow (with test card)
- [ ] Check admin panel works

---

## Post-Deployment Checklist

### 1. Functionality Tests
- [ ] All pages load without errors
- [ ] Products fetched from backend API
- [ ] Cart operations work (add, update, remove)
- [ ] Checkout flow completes
- [ ] Payment integration works
- [ ] Admin panel accessible
- [ ] Orders display in admin

### 2. Performance Checks
- [ ] Pages load in <3 seconds
- [ ] Images display correctly
- [ ] CSS/JS files load
- [ ] No console errors
- [ ] API calls complete quickly

### 3. Security Checks
- [ ] HTTPS enabled (Railway provides free SSL)
- [ ] No sensitive data in client-side code
- [ ] API keys not exposed in frontend
- [ ] Session security working
- [ ] CORS properly configured

### 4. Monitoring Setup
- [ ] Railway logs accessible
- [ ] Backend health endpoint monitored
- [ ] Error logging enabled
- [ ] Payment webhook configured

---

## Optional but Recommended

### 1. Custom Domain (Optional)
- [ ] Purchase domain (if desired)
- [ ] Configure in Railway settings
- [ ] Update DNS records
- [ ] Wait for SSL certificate
- [ ] Update backend CORS with custom domain

### 2. Production Settings
- [ ] Switch to production Paystack keys
- [ ] Update Paystack callback URL
- [ ] Configure payment webhook
- [ ] Set up error notifications
- [ ] Enable production logging

### 3. Documentation
- [ ] Document API endpoints
- [ ] Create admin user guide
- [ ] Write customer FAQ
- [ ] Document deployment process
- [ ] Create troubleshooting guide

### 4. Backups
- [ ] Database backup strategy
- [ ] Code repository backed up
- [ ] Environment variables documented
- [ ] Recovery plan documented

---

## Common Issues Checklist

If something doesn't work:

### Products Not Loading
- [ ] Check browser console for errors
- [ ] Verify API_BASE_URL environment variable
- [ ] Test backend API directly
- [ ] Check CORS configuration
- [ ] Verify backend is running

### CORS Errors
- [ ] Backend CORS includes frontend URL
- [ ] URL matches exactly (https://, no trailing slash)
- [ ] Backend redeployed after CORS update
- [ ] Clear browser cache
- [ ] Try incognito mode

### Session Issues
- [ ] `session_start()` called on each page
- [ ] Session configuration correct
- [ ] Railway supports PHP sessions
- [ ] Check Railway logs

### Payment Fails
- [ ] Paystack keys are correct
- [ ] Callback URL set correctly
- [ ] Using test card for testing
- [ ] Backend receiving payment notifications
- [ ] Check Railway backend logs

### Images Not Loading
- [ ] Images committed to repository
- [ ] Paths are relative, not absolute
- [ ] Check Railway deployment logs
- [ ] Verify file permissions

---

## Emergency Rollback Plan

If deployment fails:

1. **Keep old version running** (don't delete until new version works)
2. **Railway allows instant rollback:**
   - Go to Railway dashboard
   - Click on deployment history
   - Click "Redeploy" on previous version
3. **Check logs** to identify issue
4. **Fix locally** and redeploy

---

## Success Criteria

Your deployment is successful when:

✅ Frontend loads at Railway URL  
✅ Products display from backend  
✅ Cart functionality works  
✅ Checkout completes  
✅ Payments process (test mode)  
✅ Admin panel accessible  
✅ No console errors  
✅ No CORS errors  
✅ Backend health check UP  
✅ End-to-end customer journey works  

---

## Contact Information

- **Railway Support:** https://discord.gg/railway
- **Railway Docs:** https://docs.railway.app/
- **Paystack Support:** support@paystack.com
- **Paystack Docs:** https://paystack.com/docs

---

**Print this checklist and check off items as you complete them! 📋✅**

*Last Updated: January 26, 2026*
