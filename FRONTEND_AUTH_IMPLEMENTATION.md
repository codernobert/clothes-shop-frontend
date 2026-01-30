# 🔐 Frontend Authentication Implementation

## Overview

The frontend now has complete authentication protection for admin pages. This document explains how it works.

---

## Authentication Flow Diagram

```
User Access         Authentication Check        Result
│                   │                            │
├─ /login.php ──────────────────────────────────→ Login Page (No auth needed)
├─ /register.php ───────────────────────────────→ Register Page (No auth needed)
├─ /products.php ───→ isAuthenticated() ────────→ Product List (Any user)
├─ /cart.php ────────→ requireAuth() ───────────→ Redirect if not logged in
├─ /orders.php ──────→ requireAuth() ───────────→ User orders (if logged in)
│
├─ /admin/ ──────────→ requireAdminAuth() ──────→ Admin Dashboard (ADMIN role only)
├─ /admin/products.php → requireAdminAuth() ───→ Manage Products (ADMIN only)
├─ /admin/add_product.php → requireAdminAuth() → Add Product (ADMIN only)
├─ /admin/orders.php → requireAdminAuth() ─────→ Manage Orders (ADMIN only)
│
└─ /ajax/admin/* ────→ isAdmin() check ────────→ API endpoints (ADMIN only)
```

---

## 🔧 How Authentication Works

### 1. Session Storage

When user logs in, the backend returns:
```json
{
  "accessToken": "eyJhbGciOiJIUzI1NiIs...",
  "refreshToken": "...",
  "userId": 1,
  "email": "user@example.com",
  "firstName": "John",
  "lastName": "Doe",
  "role": "ADMIN"  // or "USER"
}
```

Frontend stores in session (`$_SESSION`):
```php
$_SESSION['access_token'] = 'eyJhbGciOiJIUzI1NiIs...';
$_SESSION['user'] = [
  'userId' => 1,
  'email' => 'user@example.com',
  'firstName' => 'John',
  'lastName' => 'Doe',
  'role' => 'ADMIN'
];
```

### 2. Authentication Functions (config.php)

```php
// Check if user is logged in
isAuthenticated()
  ├─ Checks: isset($_SESSION['access_token'])
  └─ Checks: isset($_SESSION['user'])

// Get current user info
getCurrentUser()
  └─ Returns: $_SESSION['user']

// Check if user is admin
isAdmin()
  ├─ Get user from session
  └─ Check if role === 'ADMIN'

// Require authentication (page-level)
requireAuth()
  ├─ If not authenticated → Redirect to /login.php
  └─ If authenticated → Continue

// Require admin authentication (page-level)
requireAdminAuth()
  ├─ If not authenticated → Redirect to /login.php
  ├─ If authenticated but not admin → Redirect to /index.php
  └─ If admin → Continue
```

---

## 📂 Protected Admin Pages

### Main Admin Pages (with page-level auth)

```
frontend/admin/
├── index.php           ✅ requireAdminAuth() - Admin Dashboard
├── products.php        ✅ requireAdminAuth() - Manage Products
├── add_product.php     ✅ requireAdminAuth() - Add Product
└── orders.php          ✅ requireAdminAuth() - Manage Orders
```

**Example - admin/index.php:**
```php
<?php
session_start();
require_once '../config.php';
requireAdminAuth();  // ← Checks authentication and admin role

// Rest of page...
?>
```

### AJAX API Endpoints (with API-level auth)

```
frontend/ajax/admin/
├── add_product.php           ✅ isAdmin() check
├── delete_product.php        ✅ isAdmin() check
├── update_product.php        ✅ isAdmin() check
└── update_order_status.php   ✅ isAdmin() check
```

**Example - ajax/admin/add_product.php:**
```php
<?php
session_start();
require_once '../../config.php';

header('Content-Type: application/json');

// Check authentication
if (!isAuthenticated()) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Check admin role
if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit;
}

// Process request...
?>
```

---

## 🔄 User Role Types

| Role | Pages | Features |
|------|-------|----------|
| **ADMIN** | `/admin/*` | View dashboard, manage products, manage orders |
| **USER** | `/products`, `/cart`, `/orders` | Browse products, add to cart, view orders |
| **NOT LOGGED IN** | `/login`, `/register` | Login or register |

---

## 🚀 Using Authentication in Pages

### Example 1: Regular User Page (Cart)

```php
<?php
session_start();
require_once 'config.php';

// Check if user is logged in
requireAuth();

// Only logged-in users reach here
$user = getCurrentUser();
echo "Welcome, " . $user['firstName'];
?>
```

### Example 2: Admin Page (Dashboard)

```php
<?php
session_start();
require_once 'config.php';

// Check if admin
requireAdminAuth();

// Only admins reach here
$user = getCurrentUser();
echo "Admin Panel - Hello " . $user['firstName'];
?>
```

### Example 3: API Endpoint (Admin Add Product)

```php
<?php
session_start();
require_once '../../config.php';

header('Content-Type: application/json');

// Check both authentication and admin role
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

// Only authenticated admins reach here
$input = json_decode(file_get_contents('php://input'), true);
// Process...
?>
```

---

## 🔑 JWT Token Handling

The frontend sends JWT token in API requests:

```php
// In config.php makeApiRequest() function:
$headers = "Content-Type: application/json\r\n";

if ($requireAuth && isset($_SESSION['access_token'])) {
    $token = $_SESSION['access_token'];
    $headers .= "Authorization: Bearer $token\r\n";
    // Backend validates token and returns user info
}
```

**Example API Request:**
```http
POST /api/admin/products HTTP/1.1
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
Content-Type: application/json

{
  "name": "T-Shirt",
  "price": 29.99,
  "category": "TOPS"
}
```

---

## 🛡️ Security Features Implemented

✅ Session-based authentication  
✅ JWT token validation  
✅ Role-based access control (RBAC)  
✅ Page-level access checks  
✅ API-level access checks  
✅ Automatic token inclusion in API calls  
✅ Redirect on auth failure  
✅ CORS headers with token support  

---

## 🔍 Debugging Authentication Issues

### Issue: "Authentication required" on admin page

**Check 1: Is user logged in?**
```php
// Add to page for debugging
var_dump(isAuthenticated());
var_dump(getCurrentUser());
```

**Check 2: Is user an admin?**
```php
var_dump(isAdmin());
var_dump(getCurrentUser()['role']);
```

**Check 3: Is session working?**
```php
session_start();
var_dump($_SESSION);
// Should show 'access_token' and 'user' keys
```

### Issue: API calls failing with 401

**Check:**
1. `$_SESSION['access_token']` is set
2. Token is valid (not expired)
3. Backend is validating token correctly
4. CORS headers are correct

---

## 📝 Files Modified for Admin Auth

```
frontend/
├── config.php                    ✅ Added isAdmin(), requireAdminAuth()
├── admin/
│   ├── index.php                 ✅ Added requireAdminAuth()
│   ├── products.php              ✅ Added requireAdminAuth()
│   ├── add_product.php           ✅ Added requireAdminAuth()
│   └── orders.php                ✅ Added requireAdminAuth()
└── ajax/admin/
    ├── add_product.php           ✅ Already had isAdmin() check
    ├── delete_product.php        ✅ Already had isAdmin() check
    ├── update_product.php        ✅ Already had isAdmin() check
    └── update_order_status.php   ✅ Already had isAdmin() check
```

---

## 🔗 Related Documentation

- `ADMIN_SETUP_GUIDE.md` - How to create admin credentials
- `QUICK_START_FRONTEND.md` - Frontend deployment guide
- `BACKEND_CORS_UPDATE.md` - Backend CORS configuration

