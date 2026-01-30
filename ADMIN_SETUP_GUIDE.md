# 🔐 Admin Setup Guide

## How to Get/Create Admin Credentials

The admin credentials are managed by the **backend (Java/Spring Boot)** application. This guide explains how to set up an admin user.

---

## 🚀 Option 1: Create Admin During Backend Setup (RECOMMENDED)

If you're setting up the backend from scratch, the admin user is typically created during **database initialization**.

### Steps:

1. **Check your backend's database initialization script**
   - Look for `data.sql` or migration files in:
     ```
     src/main/resources/db/migration/
     src/main/resources/data.sql
     ```

2. **Admin user is usually seeded with:**
   ```sql
   INSERT INTO users (email, password, first_name, last_name, role, created_at)
   VALUES ('admin@clothesshop.com', 'encrypted_password', 'Admin', 'User', 'ADMIN', NOW());
   ```

3. **Default credentials (before your backend deployment):**
   - **Email:** `admin@clothesshop.com`
   - **Password:** Check with your backend documentation or database migration files

---

## 🔧 Option 2: Create Admin User via Backend API

If the backend is running and you have database access:

### 1. Register a Regular User First
```bash
POST http://localhost:8080/api/auth/register
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "SecurePassword123!",
  "firstName": "Admin",
  "lastName": "User",
  "phone": "0700000000"
}
```

### 2. Promote User to Admin (Database)
Connect to your database and run:
```sql
UPDATE users SET role = 'ADMIN' WHERE email = 'admin@example.com';
```

### 3. Login with Admin Credentials
Go to: `http://localhost:8000/login.php`

**Credentials:**
- Email: `admin@example.com`
- Password: `SecurePassword123!`

---

## ✅ Verify Admin Access

After creating the admin user:

1. **Login to Frontend**
   - URL: `http://localhost:8000/login.php`
   - Use admin credentials

2. **Access Admin Dashboard**
   - URL: `http://localhost:8000/admin/index.php`
   - Should see: Dashboard, Products, Orders menus

3. **Test Admin Features**
   - ✅ View Dashboard
   - ✅ Manage Products (Add, Edit, Delete)
   - ✅ Manage Orders

---

## 🔍 Troubleshooting

### "Access Denied" / Redirected to Home Page
**Problem:** User is logged in but doesn't have ADMIN role
**Solution:** 
- Verify the user's role in database is set to `'ADMIN'`
- Run: `SELECT email, role FROM users WHERE email = 'your-admin@email.com';`

### Can't Login at All
**Problem:** Invalid credentials
**Solution:**
- Verify backend is running: `http://localhost:8080/api/actuator/health`
- Check backend logs for auth errors
- Verify database has the user record

### "Authentication required" Error
**Problem:** Session/JWT token issues
**Solution:**
- Clear browser cookies: DevTools → Application → Cookies → Delete all
- Log out and log in again
- Check backend's `API_BASE_URL` in frontend config

---

## 🛡️ Security Notes

- ✅ Admin credentials should be **strong passwords**
- ✅ Change default admin credentials in **production**
- ✅ Admin password should be encrypted in database (bcrypt/argon2)
- ✅ Protect `/admin/` pages with authentication ✅ (Already implemented)

---

## 📋 Admin Account Details

The admin account has access to:

| Feature | Available |
|---------|-----------|
| Dashboard | ✅ Yes |
| View Products | ✅ Yes |
| Add Products | ✅ Yes |
| Edit Products | ✅ Yes |
| Delete Products | ✅ Yes |
| View Orders | ✅ Yes |
| Update Order Status | ✅ Yes |
| Customer Management | ❌ No (Frontend only) |
| Reports | ❌ No (Future feature) |

---

## 🔗 Related Files

**Frontend:**
- Authentication: `frontend/config.php`
- Admin Pages: `frontend/admin/`
- Login: `frontend/login.php`

**Backend:**
- User Service: `src/main/java/com/ecommerce/clothesshop/service/UserService.java`
- User Repository: `src/main/java/com/ecommerce/clothesshop/repository/UserRepository.java`
- Database Schema: `src/main/resources/db/migration/`

---

## ❓ Need More Help?

1. **Check backend logs** for authentication errors
2. **Verify database connection** is working
3. **Ensure API_BASE_URL** is correctly set in frontend `config.php`
4. **Check CORS settings** in backend `WebConfig.java`

