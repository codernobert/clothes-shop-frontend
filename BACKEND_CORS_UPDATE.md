# 🔧 Backend CORS Update - IMPORTANT

## ⚠️ MUST DO After Frontend Deployment

Once your frontend is deployed to Railway and you have the URL, you **MUST** update the backend CORS settings.

---

## 📍 Your Backend Repository

You need to update the backend code (Java/Spring Boot project).

### File to Edit: `WebConfig.java`

**Location:** `src/main/java/com/ecommerce/clothesshop/config/WebConfig.java`

### Current Code:
```java
@Configuration
public class WebConfig implements WebMvcConfigurer {
    @Override
    public void addCorsMappings(CorsRegistry registry) {
        registry.addMapping("/**")
                .allowedOrigins("http://localhost:3000", "http://localhost:8080")
                .allowedMethods("GET", "POST", "PUT", "DELETE", "OPTIONS")
                .allowedHeaders("*")
                .allowCredentials(true);
    }
}
```

### Updated Code:
```java
@Configuration
public class WebConfig implements WebMvcConfigurer {
    @Override
    public void addCorsMappings(CorsRegistry registry) {
        registry.addMapping("/**")
                .allowedOrigins(
                    "http://localhost:3000",
                    "http://localhost:8080",
                    "https://your-frontend-app-name.up.railway.app"  // 👈 ADD THIS
                )
                .allowedMethods("GET", "POST", "PUT", "DELETE", "OPTIONS")
                .allowedHeaders("*")
                .allowCredentials(true);
    }
}
```

---

## 🚀 Steps to Update

1. **Get your frontend Railway URL** (after frontend deployment)
   - Example: `https://clothes-shop-frontend-production.up.railway.app`

2. **Navigate to your backend project**
   ```powershell
   cd path\to\your\backend\project
   ```

3. **Edit WebConfig.java**
   - Add your Railway frontend URL to `.allowedOrigins()`

4. **Commit and push**
   ```powershell
   git add src/main/java/com/ecommerce/clothesshop/config/WebConfig.java
   git commit -m "Update CORS to allow Railway frontend"
   git push origin main
   ```

5. **Railway auto-deploys** the backend with new CORS settings

---

## ✅ Verify CORS is Working

After updating:

1. Open your frontend: `https://your-frontend.railway.app`
2. Open browser DevTools (F12) → Console tab
3. Navigate to products page
4. **No CORS errors** = ✅ Working!
5. **CORS errors** = ❌ Check the URL you added

---

## 🐛 Common CORS Errors

### Error: "Access-Control-Allow-Origin"
**Solution:** Make sure the URL in WebConfig.java exactly matches your Railway URL (including https://)

### Error: "Credentials mode"
**Solution:** Ensure `.allowCredentials(true)` is set in WebConfig.java

### Error: "Method not allowed"
**Solution:** Verify `.allowedMethods()` includes the method being used (GET, POST, etc.)

---

## 📋 Checklist

- [ ] Got Railway frontend URL
- [ ] Updated WebConfig.java with frontend URL
- [ ] Committed and pushed changes
- [ ] Backend redeployed on Railway
- [ ] Tested frontend - no CORS errors
- [ ] Products loading correctly
- [ ] API calls working

---

## 💡 Pro Tips

1. **Wildcard NOT recommended for production:**
   ```java
   .allowedOrigins("*")  // ❌ Don't do this
   ```

2. **Multiple domains:**
   ```java
   .allowedOrigins(
       "https://frontend-prod.railway.app",
       "https://frontend-staging.railway.app",
       "http://localhost:3000"  // For local dev
   )
   ```

3. **Environment variable approach (better):**
   ```java
   String[] allowedOrigins = System.getenv("ALLOWED_ORIGINS").split(",");
   .allowedOrigins(allowedOrigins)
   ```
   Then in Railway backend env vars:
   ```
   ALLOWED_ORIGINS=http://localhost:3000,https://your-frontend.railway.app
   ```

---

**Don't forget this step! CORS is the #1 reason frontend can't connect to backend! 🎯**
