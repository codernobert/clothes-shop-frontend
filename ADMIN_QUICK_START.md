# ⚡ Quick Admin Access Guide

## 🎯 TL;DR - Get Admin Access in 3 Steps

### Step 1: Know Your Admin Credentials
The admin account is created during backend setup. You need:
- **Email:** (Ask backend developer or check database)
- **Password:** (Ask backend developer or check database)

Common defaults:
- `admin@clothesshop.com` / `admin123` (development only)
- Check your backend's database initialization files

### Step 2: Login to Frontend
1. Go to: `http://localhost:8000/login.php`
2. Enter admin email and password
3. Click "Login"

### Step 3: Access Admin Dashboard
1. After login, go to: `http://localhost:8000/admin/index.php`
2. Or navigate from menu if available

---

## ✅ Admin Dashboard Features

Once logged in as admin, you can:

| Feature | URL | What You Can Do |
|---------|-----|-----------------|
| Dashboard | `/admin/index.php` | View stats, products, orders |
| Products | `/admin/products.php` | View all products |
| Add Product | `/admin/add_product.php` | Create new products |
| Orders | `/admin/orders.php` | View and update order status |

---

## 🔒 Authentication Protection

**These pages are protected:**
- ✅ `/admin/index.php` - Only admins
- ✅ `/admin/products.php` - Only admins
- ✅ `/admin/add_product.php` - Only admins
- ✅ `/admin/orders.php` - Only admins

**If you try to access without admin role:**
- Redirects to `/login.php` if not logged in
- Redirects to `/index.php` (home) if logged in but not admin

---

## 🐛 Troubleshooting

### Can't see admin menu after login?
→ Check that your user role is set to `ADMIN` in the database

### Getting redirected when accessing /admin/
→ Likely not logged in or not an admin. Try:
1. Clear browser cookies
2. Login again at `/login.php`
3. Check your role in database

### "Invalid credentials" at login?
→ Check backend is running: `http://localhost:8080/api/actuator/health`

---

## 📞 Need Admin Credentials?

Contact your backend developer or check:
1. Backend database initialization file (SQL)
2. Backend environment setup documentation
3. Your team's credential management system

Common locations:
```
backend/
├── src/main/resources/
│   ├── db/migration/     ← Check here
│   └── data.sql          ← Or here
└── README.md             ← Or here
```

---

## 🔐 Security Note

In **production**, admin credentials should be:
- ✅ Strong and unique
- ✅ Securely stored (never in code)
- ✅ Changed from defaults
- ✅ Protected by strong authentication

