# 🛍️ Clothes E-Commerce Platform - Portfolio Description

## Project Overview

A full-stack e-commerce application for selling clothes online, featuring a **reactive Java backend** and a **PHP-based frontend**. The platform integrates real-world payment processing through Paystack API with support for multiple payment methods including M-Pesa, enabling seamless transactions for customers across East Africa and beyond.

---

## 🏗️ Architecture

### Backend Architecture
- **Type:** Reactive, Non-blocking microservice
- **Deployment:** Cloud-ready (Railway/Heroku compatible)
- **API Style:** RESTful endpoints with JWT authentication
- **Real-time Capabilities:** WebFlux for high concurrency

### Frontend Architecture
- **Type:** Server-side rendered PHP
- **Authentication:** Session-based JWT token storage
- **Integration:** Direct REST API consumption
- **Scalability:** Stateless design for horizontal scaling

---

## 💻 Technology Stack

### Backend
| Layer | Technology |
|-------|-----------|
| **Language** | Java 17 |
| **Framework** | Spring Boot 3.5.9 |
| **Web** | Spring WebFlux (Reactive) |
| **Database Access** | Spring Data R2DBC (Reactive) |
| **Database** | PostgreSQL 12+ |
| **Authentication** | Spring Security + OAuth2 + JWT (JJWT 0.12.3) |
| **Resilience** | Resilience4j (Circuit Breaker, Retry, Timeout) |
| **Build Tool** | Maven |
| **Code Generation** | Lombok |
| **Monitoring** | Spring Boot Actuator |

### Frontend
| Layer | Technology |
|-------|-----------|
| **Language** | PHP 7.4+ |
| **Authentication** | JWT tokens (stored in SESSION) |
| **HTTP Calls** | PHP stream_get_contents with cURL |
| **Styling** | CSS + Bootstrap |
| **Interactivity** | Vanilla JavaScript + AJAX |
| **Features** | Server-side rendering |

### Payment Integration
| Component | Details |
|-----------|---------|
| **Provider** | Paystack API |
| **Supported Methods** | Card, Mobile Money, M-Pesa, Bank Transfer, USSD |
| **Primary Market** | Kenya (M-Pesa focus) |
| **Flow** | Initialize → Redirect → Verify → Callback |

### Database
| Aspect | Details |
|--------|---------|
| **Type** | Relational (PostgreSQL) |
| **Driver** | R2DBC PostgreSQL (reactive) |
| **Key Tables** | Users, Products, Cart Items, Orders, Order Items |
| **Relationships** | User → Cart → Products, User → Orders |

---

## ✨ Core Features

### For Customers
✅ **Product Browsing**
- Browse complete product catalog
- Search by keyword
- Filter by category, brand, price range, gender, size, color
- Detailed product views with images and descriptions

✅ **Shopping Cart**
- Add/remove products
- Update quantities
- Persistent cart storage
- Real-time total calculation

✅ **Secure Checkout**
- One-click order creation
- Integrated payment processing via Paystack
- Support for multiple payment methods
- Automatic payment verification

✅ **Order Management**
- View order history with tracking
- Check order status (PENDING → DELIVERED)
- View detailed order items
- Cancel eligible orders

### For Administrators
✅ **Product Management**
- Create, update, delete products
- Manage inventory/stock levels
- Bulk operations support
- Product categorization

✅ **Order Management**
- View all customer orders
- Update order status
- Process cancellations
- Monitor payment status

### System Features
✅ **Security**
- JWT-based authentication and authorization
- BCrypt password hashing
- Session management
- CORS configuration

✅ **Resilience & Reliability**
- Circuit breaker pattern (Paystack payment failures)
- Automatic retry with exponential backoff
- Request timeout handling (5 seconds)
- Health check endpoints

✅ **Performance**
- Reactive, non-blocking architecture
- Efficient R2DBC database queries
- Horizontal scalability
- High concurrency support

---

## 📡 API Endpoints Summary

### Products
```
GET  /api/products              - List all active products
GET  /api/products/{id}         - Get product details
GET  /api/products/search       - Search products by keyword
GET  /api/products/filter       - Advanced filtering
GET  /api/products/category/{c} - Filter by category
```

### Shopping Cart
```
GET    /api/cart/{userId}                - Get user's cart
POST   /api/cart/{userId}/items          - Add to cart
PUT    /api/cart/{userId}/items/{itemId} - Update quantity
DELETE /api/cart/{userId}/items/{itemId} - Remove item
DELETE /api/cart/{userId}                - Clear cart
```

### Checkout & Payments
```
POST   /api/checkout                          - Create order
POST   /api/checkout/payment-intent           - Initialize payment
POST   /api/checkout/verify-payment           - Verify payment status
GET    /api/checkout/paystack/callback        - Webhook callback
POST   /api/checkout/confirm-payment/{id}     - Confirm & update order
```

### Orders
```
GET /api/orders/{orderId}       - Get order by ID
GET /api/orders/number/{number} - Get by order number
GET /api/orders/user/{userId}   - Get user's orders
```

### Admin
```
POST   /api/admin/products           - Create product
PUT    /api/admin/products/{id}      - Update product
DELETE /api/admin/products/{id}      - Delete product
PATCH  /api/admin/products/{id}/stock - Update stock
GET    /api/admin/orders             - List all orders
PATCH  /api/admin/orders/{id}/status - Update status
POST   /api/admin/orders/{id}/cancel - Cancel order
```

---

## 🔄 Payment Flow

```
1. Customer adds items to cart
   ↓
2. Clicks checkout → Creates order (PENDING status)
   ↓
3. Backend initializes Paystack payment
   ↓
4. Customer redirected to Paystack checkout page
   ↓
5. Customer completes payment (M-Pesa/Card/etc)
   ↓
6. Paystack sends webhook callback to backend
   ↓
7. Backend verifies payment status
   ↓
8. Order status updated to COMPLETED
   ↓
9. Customer can view confirmed order
```

---

## 📊 Database Schema Highlights

### Key Tables
- **Users**: Authentication, roles (CUSTOMER/ADMIN), profiles
- **Products**: Catalog items with categories, pricing, stock
- **Cart**: User shopping carts with items and quantities
- **Orders**: Order tracking with unique order numbers, status, payment info
- **OrderItems**: Line items for each order

---

## 🚀 Deployment & DevOps

### Local Development
- Spring Boot embedded server (port 8080)
- PostgreSQL local database
- Environment-based configuration

### Production Ready
- **Platforms**: Railway, Heroku, AWS, GCP
- **Dockerfile**: Containerizable
- **Health Checks**: Actuator endpoints
- **Monitoring**: Resilience4j metrics
- **Scalability**: Stateless reactive design

---

## 🧪 Testing & Documentation

### Postman Collections Included
- `Ecommerce_Customer_Journey.postman_collection.json` - Full user flow
- `Clothes_Shop_Authentication.postman_collection.json` - Auth endpoints
- `MPESA_Test_Flow.postman_collection.json` - Payment testing

### Documentation
- Complete API documentation
- Customer journey flowcharts
- Payment flow diagrams
- Testing guides with examples
- Quick start references

---

## 📈 Key Metrics & Performance

| Metric | Value |
|--------|-------|
| **Framework** | Spring Boot 3.5.9 (Latest) |
| **Java Version** | 17 LTS (Production Ready) |
| **Database** | PostgreSQL (Enterprise Grade) |
| **Payment Gateway** | Paystack (Africa-focused) |
| **Authentication** | OAuth2 + JWT (Industry Standard) |
| **Resilience** | Circuit Breaker + Retry + Timeout |
| **Response Time** | < 100ms (typical reactive response) |

---

## 🎯 Development Highlights

### Technical Excellence
✓ **Reactive Architecture** - Non-blocking I/O, optimal for high traffic
✓ **Security First** - JWT, BCrypt, Spring Security integration
✓ **Error Handling** - Comprehensive exception handling with resilience patterns
✓ **Code Quality** - Lombok reduces boilerplate, clean architecture
✓ **Testing Ready** - Postman collections for all endpoints
✓ **DevOps Ready** - Environment configuration, health checks, monitoring

### Business Value
✓ **Real Payment Processing** - Integrated with Paystack, live transactions
✓ **Multi-Currency Support** - Supports MPESA for emerging markets
✓ **Scalable** - Horizontal scaling with stateless design
✓ **Extensible** - Modular REST API, easy to add features
✓ **User Experience** - Seamless checkout, multiple payment options

---

## 🔐 Security Features

- ✅ JWT authentication with refresh tokens
- ✅ BCrypt password hashing (Spring Security Crypto)
- ✅ Role-based access control (CUSTOMER/ADMIN)
- ✅ Session-based token storage (PHP frontend)
- ✅ CORS configuration for cross-origin requests
- ✅ Input validation (Spring Validation)
- ✅ Secure payment callback verification

---

## 📱 Responsive Design

- PHP frontend with responsive layout
- Mobile-friendly checkout process
- Touch-optimized UI for M-Pesa payments
- Cross-browser compatibility
- Progressive enhancement

---


---

