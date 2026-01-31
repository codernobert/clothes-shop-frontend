# 🛍️ Clothes Shop E-Commerce Platform - Portfolio Project

## Project Overview

**Clothes Shop** is a full-stack e-commerce application demonstrating modern web development practices. This project showcases a complete customer-facing online store with an advanced admin dashboard for product and order management.

**Status:** ✅ Fully Functional Portfolio Project (January 2026)

---

## 🎯 Project Features

### Customer Features
- ✅ **Product Browsing** - Browse products by category (Tops, Bottoms, Dresses, Outerwear)
- ✅ **Shopping Cart** - Add/remove items, update quantities, persistent cart
- ✅ **Product Details** - Detailed product information with images and descriptions
- ✅ **User Authentication** - Secure login and registration system
- ✅ **Order Management** - View order history and track purchases
- ✅ **Cart Badge** - Real-time cart count updates in navigation
- ✅ **Responsive Design** - Mobile, tablet, and desktop friendly

### Admin Features
- ✅ **Product Management** - Create, read, update, and delete products
- ✅ **Order Management** - View and update order statuses
- ✅ **Dashboard** - Statistics and analytics overview
- ✅ **Admin Authentication** - Secure admin login with role-based access
- ✅ **Separate Admin Interface** - Dedicated admin home page without portfolio demo
- ✅ **Edit Products** - Full product editing capabilities

### Security Features
- ✅ **JWT Authentication** - Secure token-based authentication
- ✅ **Role-Based Access Control** - Admin vs Customer roles
- ✅ **Protected Routes** - Admin pages require authentication and authorization
- ✅ **Session Management** - Secure session handling
- ✅ **Input Validation** - Server-side validation and sanitization

---

## 💻 Technology Stack

### Frontend
```
├─ PHP 7.4+ 🐘
├─ HTML5 📄
├─ CSS3 🎨
├─ Bootstrap 5 📦
│  └─ Responsive grid system & components
├─ JavaScript (Vanilla) ⚙️
│  └─ AJAX for asynchronous operations
├─ Font Awesome 6  🎯
│  └─ 600+ professional icons
└─ Charts.js (Optional) 📊
   └─ For analytics visualization
```

### Backend API
```
├─ Spring Boot 🍃
├─ Java ☕
├─ REST API Architecture 🔌
└─ JWT Token Authentication 🔐
```

### Database
```
├─ MySQL 8.0+ 💾
├─ Relational Schema
├─ Product Management 📦
├─ User Accounts 👥
├─ Orders & Transactions 🛒
└─ Order Items 📋
```

### DevOps & Deployment
```
├─ Docker (Containerization) 🐳
├─ Railway (Cloud Deployment) 🚀
├─ Nginx (Web Server) 🌐
├─ Environment Variables 🔑
└─ CORS Configuration ✅
```

### Development Tools
```
├─ Git & GitHub 📖
├─ VS Code / JetBrains IDE 💡
├─ Postman API Testing 🧪
└─ PowerShell Scripts 📜
```

---

## 📁 Project Structure

```
clothes-shop-frontend/
├─ frontend/
│  ├─ index.php                 (Customer homepage)
│  ├─ products.php              (Product listing)
│  ├─ product_detail.php        (Product details)
│  ├─ cart.php                  (Shopping cart)
│  ├─ checkout.php              (Checkout page)
│  ├─ login.php                 (Customer login)
│  ├─ register.php              (User registration)
│  ├─ orders.php                (Order history)
│  ├─ logout.php                (Logout handler)
│  ├─ config.php                (API configuration)
│  ├─ admin/
│  │  ├─ index.php              (Admin dashboard)
│  │  ├─ home.php               (Admin home page)
│  │  ├─ login.php              (Admin login)
│  │  ├─ products.php           (Product management)
│  │  ├─ add_product.php        (Add product form)
│  │  ├─ edit_product.php       (Edit product form)
│  │  └─ orders.php             (Order management)
│  ├─ ajax/
│  │  ├─ add_to_cart.php        (Add item to cart)
│  │  ├─ remove_from_cart.php   (Remove item)
│  │  ├─ update_cart.php        (Update quantity)
│  │  ├─ get_cart_count.php     (Cart count endpoint)
│  │  ├─ checkout.php           (Process checkout)
│  │  ├─ create_payment.php     (Payment creation)
│  │  ├─ verify_payment.php     (Payment verification)
│  │  └─ admin/
│  │     ├─ add_product.php
│  │     ├─ update_product.php
│  │     ├─ delete_product.php
│  │     └─ update_order_status.php
│  ├─ includes/
│  │  ├─ header.php             (Navigation & styles)
│  │  └─ footer.php             (Footer component)
│  ├─ js/
│  │  ├─ auth-api.js            (Authentication functions)
│  │  └─ auth-react-example.jsx (React example)
│  └─ payment_callback.php      (Payment webhook)
├─ nginx.conf                   (Nginx configuration)
├─ railway.json                 (Railway deployment config)
└─ composer.json                (PHP dependencies)
```

---

## 🔄 API Integration

### Backend API Base URL
```
http://localhost:8080/api  (Development)
Production: Deploy on Railway/Cloud
```

### Key API Endpoints

**Authentication:**
```
POST   /auth/login              - User login
POST   /auth/register           - User registration
POST   /auth/refresh            - Refresh JWT token
```

**Products:**
```
GET    /products                - Get all products
GET    /products/{id}           - Get product details
POST   /admin/products          - Create product (Admin)
PUT    /admin/products/{id}     - Update product (Admin)
DELETE /admin/products/{id}     - Delete product (Admin)
```

**Cart:**
```
GET    /cart/{userId}           - Get user's cart
POST   /cart/{userId}/items     - Add item to cart
PUT    /cart/{userId}/items/{itemId} - Update item quantity
DELETE /cart/{userId}/items/{itemId} - Remove item
```

**Orders:**
```
POST   /orders                  - Create order
GET    /orders/{userId}         - Get user's orders
GET    /admin/orders            - Get all orders (Admin)
PUT    /admin/orders/{id}       - Update order status (Admin)
```

---

## 🎨 Frontend Architecture

### Page Structure
1. **Homepage** - Modern hero section with quick links, portfolio demo, categories, and featured products
2. **Products** - Grid view with filtering and search
3. **Product Detail** - Detailed view with images, descriptions, and add to cart
4. **Cart** - Review items, update quantities, and proceed to checkout
5. **Checkout** - Order summary and payment processing
6. **Orders** - User's order history and tracking
7. **Admin Dashboard** - Statistics and quick links
8. **Admin Products** - CRUD operations for products
9. **Admin Orders** - Order management and status updates

### Key Components
- Responsive navigation bar with role-based menus
- Modern card-based layouts with icons
- Real-time cart badge updates
- Flash messages for user feedback
- Secure session management
- Professional forms with validation

---

## 🔐 Authentication & Authorization

### Authentication Flow
```
User Login
  ↓
API validates credentials
  ↓
JWT tokens issued (access + refresh)
  ↓
Tokens stored in PHP session
  ↓
User authenticated
```

### Authorization Levels
```
├─ Guest User
│  ├─ View products
│  ├─ View product details
│  └─ Access login/register
│
├─ Authenticated Customer
│  ├─ All guest permissions
│  ├─ Add to cart
│  ├─ Place orders
│  ├─ View own orders
│  └─ Access admin demo (redirected to admin login)
│
└─ Admin User
   ├─ All customer permissions
   ├─ Access admin dashboard
   ├─ Manage products (CRUD)
   ├─ View all orders
   └─ Update order status
```

---

## 🚀 Deployment

### Local Development
```bash
1. Clone repository
2. Set up PHP local server
3. Configure API_BASE_URL to Spring Boot backend
4. Run: php -S localhost:8000
5. Visit: http://localhost:8000
```

### Production Deployment (Railway)
```
├─ Docker containerization
├─ Nginx web server configuration
├─ Environment variables for API URL
├─ SSL/TLS certificates (Railway provides)
└─ Automatic deployments from GitHub
```

---

## 📊 Database Schema

### Key Tables
```
users
├─ id (PK)
├─ email (UNIQUE)
├─ firstName
├─ lastName
├─ password (hashed)
├─ role (CUSTOMER/ADMIN)
└─ createdAt

products
├─ id (PK)
├─ name
├─ description
├─ price
├─ category
├─ imageUrl
├─ stockQuantity
├─ isActive
└─ createdAt

orders
├─ id (PK)
├─ userId (FK)
├─ status (PENDING/COMPLETED/CANCELLED)
├─ totalAmount
├─ paymentStatus
└─ createdAt

orderItems
├─ id (PK)
├─ orderId (FK)
├─ productId (FK)
├─ quantity
└─ price
```

---

## ✨ Key Features & Highlights

### For Customers
- 🛍️ Browse 100+ products with filters
- 🛒 Add to cart with real-time updates
- 💳 Secure checkout process
- 📦 Order tracking and history
- 👤 User account management
- 📱 Mobile-responsive design

### For Admins
- 📊 Dashboard with statistics
- ➕ Add new products with image URLs
- ✏️ Edit existing products
- 🗑️ Delete products
- 📋 Manage customer orders
- 🔄 Update order statuses

### Technical Highlights
- JWT-based authentication
- Responsive Bootstrap design
- AJAX for smooth interactions
- Real-time cart updates
- Professional error handling
- Security best practices
- Clean, maintainable code

---

## 🎓 Learning Outcomes

This project demonstrates proficiency in:
- ✅ Full-stack web development
- ✅ RESTful API integration
- ✅ Database design and queries
- ✅ Authentication & authorization
- ✅ Responsive web design
- ✅ Frontend frameworks (Bootstrap)
- ✅ AJAX and asynchronous programming
- ✅ Security best practices
- ✅ Cloud deployment
- ✅ Version control (Git)

---

## 🌐 Live Demo

**Homepage:** http://localhost:8000/index.php
**Admin Demo:** http://localhost:8000/admin/login.php

**Demo Credentials:**
```
Admin Email:    admin@clothesshop.com
Admin Password: password123
```

---

## 📈 Future Enhancements

Potential features for expansion:
- Payment gateway integration (Stripe, PayPal)
- Product reviews and ratings
- Wishlist functionality
- Advanced search and filters
- Email notifications
- Inventory alerts
- Analytics dashboard
- Promotion codes and discounts
- Multi-language support
- Performance optimizations

---

## 📚 Technologies Used (Summary)

| Layer | Technology |
|-------|-----------|
| **Frontend** | PHP, HTML5, CSS3, Bootstrap 5, JavaScript |
| **Backend** | Spring Boot, Java, REST API |
| **Database** | MySQL |
| **Authentication** | JWT (JSON Web Tokens) |
| **Deployment** | Docker, Railway, Nginx |
| **Icons** | Font Awesome 6 |
| **Version Control** | Git & GitHub |

---

## 🎯 Project Statistics

```
├─ ~15 PHP pages
├─ ~10 AJAX endpoints
├─ ~8 Admin functions
├─ Responsive design (3 breakpoints)
├─ 100% functional e-commerce flow
└─ Production-ready code
```

---

## 👨‍💻 Developer Notes

This portfolio project demonstrates a professional approach to e-commerce development, showcasing:
- Clean code architecture
- Proper separation of concerns
- Security-first approach
- User experience optimization
- Professional UI/UX design
- Real-world best practices

The application is **fully functional** and ready for user testing, with a complete customer journey from product browsing to order placement, complemented by a powerful admin interface for store management.

---

**Project Created:** January 2026  
**Status:** ✅ Complete & Production-Ready  
**License:** Private Portfolio Project

