# Clothes E-Commerce Backend

A reactive e-commerce backend application built with Spring Boot WebFlux for selling clothes online.

## 📚 Quick Documentation Links

- **🛍️ [Customer Journey Testing (Postman)](POSTMAN_CUSTOMER_JOURNEY.md)** - Complete API testing guide
- **📦 [Postman Collection](Ecommerce_Customer_Journey.postman_collection.json)** - Import into Postman
- **💳 [Paystack MPESA Integration](PAYSTACK_INTEGRATION.md)** - Payment setup guide
- **🚀 [Quick Start](POSTMAN_QUICK_START.md)** - Fast testing reference
- **📊 [Journey Flowchart](CUSTOMER_JOURNEY_FLOWCHART.md)** - Visual guide
- **📖 [Documentation Index](DOCUMENTATION_INDEX.md)** - Find anything

---

## Tech Stack

- **Java 17**
- **Spring Boot 3.2.0**
- **Spring WebFlux** (Reactive Web)
- **Spring Data R2DBC** (Reactive Database Access)
- **PostgreSQL** with R2DBC driver
- **Paystack API** for payment processing (supports MPESA, card, mobile money, bank transfers)
- **Resilience4j** for circuit breaker, retry, and timeout patterns
- **Lombok** for reducing boilerplate code
- **Maven** for dependency management

## Features

### Product Catalog
- Browse all active products
- Search products by keyword
- Filter products by category, brand, price range, and gender
- View product details
- Product images and descriptions

### Shopping Cart
- Add products to cart
- Update cart item quantities
- Remove items from cart
- View cart with total amount
- Clear entire cart

### Checkout & Payment
- Create orders from cart
- Paystack payment integration (supports MPESA for Kenya)
- Multiple payment methods: Card, Mobile Money, Bank Transfer, USSD
- Payment verification
- Order confirmation

### Order Management
- View order history
- Track order status
- Order details with line items
- Order cancellation (for eligible orders)

### Admin Features
- Create, update, and delete products
- Manage product inventory/stock
- View all orders
- Update order status (PENDING, CONFIRMED, PROCESSING, SHIPPED, DELIVERED, CANCELLED)
- Cancel orders

### Resilience Patterns
- **Circuit Breaker**: Prevents cascading failures in payment service
- **Retry**: Automatic retry with exponential backoff for payment operations
- **Timeout**: 5-second timeout for payment operations
- Health indicators for monitoring circuit breaker state

## Database Schema

### Users Table
- User authentication and profile information
- Roles: CUSTOMER, ADMIN

### Products Table
- Product catalog with details
- Categories, brands, sizes, colors
- Stock management
- Active/inactive status

### Shopping Carts & Cart Items
- User shopping carts
- Cart items with product references and quantities

### Orders & Order Items
- Order tracking with unique order numbers
- Order status and payment status
- Shipping information
- Order line items

## API Endpoints

### Product APIs
```
GET    /api/products                  - Get all active products
GET    /api/products/{id}             - Get product by ID
GET    /api/products/search           - Search products (keyword)
GET    /api/products/filter           - Filter products
GET    /api/products/category/{cat}   - Get products by category
```

### Shopping Cart APIs
```
GET    /api/cart/{userId}                    - Get user's cart
POST   /api/cart/{userId}/items              - Add item to cart
PUT    /api/cart/{userId}/items/{itemId}     - Update cart item
DELETE /api/cart/{userId}/items/{itemId}     - Remove item from cart
DELETE /api/cart/{userId}                    - Clear cart
```

### Checkout APIs
```
POST   /api/checkout                          - Create order from cart
POST   /api/checkout/payment-intent           - Initialize Paystack payment
POST   /api/checkout/verify-payment           - Verify payment status
GET    /api/checkout/paystack/callback        - Payment callback (automatic)
POST   /api/checkout/confirm-payment/{id}     - Confirm payment and update order
```

### Order APIs
```
GET    /api/orders/{orderId}          - Get order by ID
GET    /api/orders/number/{number}    - Get order by order number
GET    /api/orders/user/{userId}      - Get user's orders
```

### Admin APIs
```
POST   /api/admin/products                    - Create product
PUT    /api/admin/products/{id}               - Update product
DELETE /api/admin/products/{id}               - Delete product
PATCH  /api/admin/products/{id}/stock         - Update stock
GET    /api/admin/orders                      - Get all orders
PATCH  /api/admin/orders/{id}/status          - Update order status
POST   /api/admin/orders/{id}/cancel          - Cancel order
```

## Setup Instructions

### Prerequisites
- Java 17 or higher
- Maven 3.6+
- PostgreSQL 12+ installed and running

### Database Setup

1. Create PostgreSQL database:
```sql
CREATE DATABASE ecommerce_db;
```

2. Update `application.properties` with your database credentials:
```properties
spring.r2dbc.url=r2dbc:postgresql://localhost:5432/ecommerce_db
spring.r2dbc.username=your_username
spring.r2dbc.password=your_password
```

3. The schema will be automatically initialized on application startup

### Paystack Configuration (MPESA Support)

1. Get your Paystack API keys from [Paystack Dashboard](https://dashboard.paystack.com/#/settings/developers)

2. Update `application.properties`:
```properties
paystack.api.key=sk_test_your_paystack_secret_key
paystack.api.url=https://api.paystack.co
paystack.callback.url=http://localhost:8080/api/checkout/paystack/callback
```

**📱 MPESA Support:** Paystack supports MPESA payments for Kenya (KES currency). See detailed integration guide below.

**⚠️ Important:** For complete Paystack setup instructions, MPESA integration, and payment flow, see [PAYSTACK_INTEGRATION.md](PAYSTACK_INTEGRATION.md)

### Build and Run

1. Clone the repository

2. Build the project:
```bash
mvn clean install
```

3. Run the application:
```bash
mvn spring-boot:run
```

The application will start on `http://localhost:8080`

### Testing the API

You can use tools like Postman, cURL, or any HTTP client to test the endpoints.

Example - Get all products:
```bash
curl http://localhost:8080/api/products
```

Example - Add item to cart:
```bash
curl -X POST http://localhost:8080/api/cart/1/items \
  -H "Content-Type: application/json" \
  -d '{"productId": 1, "quantity": 2}'
```

## Monitoring

The application exposes actuator endpoints for monitoring:

- Health: `http://localhost:8080/actuator/health`
- Metrics: `http://localhost:8080/actuator/metrics`
- Circuit Breakers: `http://localhost:8080/actuator/circuitbreakers`

## Project Structure

```
src/main/java/com/ecommerce/clothesshop/
├── config/              # Configuration classes
├── controller/          # REST controllers
├── dto/                 # Data Transfer Objects
├── exception/           # Exception handling
├── model/               # Entity models
├── repository/          # R2DBC repositories
└── service/             # Business logic services

src/main/resources/
├── application.properties
└── schema.sql
```

## Future Enhancements

- JWT authentication and authorization
- Email notifications for orders
- Product reviews and ratings
- Wishlist functionality
- Advanced search with Elasticsearch
- Image upload to cloud storage
- Complete PayPal integration
- Webhook handlers for payment events
- Order shipment tracking
- Inventory alerts
- Analytics dashboard

