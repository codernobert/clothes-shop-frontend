# E-Commerce Frontend (PHP)

A comprehensive PHP frontend for the Clothes Shop e-commerce application with Bootstrap 5 styling.

## Features

### Customer Features
- **Home Page**: Hero section, featured products, category navigation
- **Product Browsing**: Search, filter by category, brand, price, gender
- **Product Details**: Full product information with add to cart
- **Shopping Cart**: View cart, update quantities, remove items
- **Checkout**: Complete shipping info, payment integration
- **Order History**: View past orders with status tracking

### Admin Features
- **Dashboard**: Statistics overview, quick actions, recent orders
- **Product Management**: Add, edit, delete products
- **Order Management**: View and update order status
- **Inventory Control**: Stock management

## File Structure

```
frontend/
├── config.php                  # API configuration and helper functions
├── index.php                   # Home page
├── products.php                # Products listing with filters
├── product_detail.php          # Single product view
├── cart.php                    # Shopping cart
├── checkout.php                # Checkout page
├── orders.php                  # Customer order history
│
├── includes/
│   ├── header.php              # Common header with navigation
│   └── footer.php              # Common footer
│
├── admin/
│   ├── index.php               # Admin dashboard
│   ├── products.php            # Manage products
│   ├── add_product.php         # Add new product
│   ├── edit_product.php        # Edit product
│   └── orders.php              # Manage orders
│
└── ajax/
    ├── add_to_cart.php         # Add item to cart
    ├── get_cart_count.php      # Get cart item count
    ├── update_cart.php         # Update cart item quantity
    ├── remove_from_cart.php    # Remove cart item
    ├── checkout.php            # Process checkout
    ├── create_payment.php      # Initialize payment
    │
    └── admin/
        ├── add_product.php     # Admin add product
        ├── update_product.php  # Admin update product
        ├── delete_product.php  # Admin delete product
        └── update_order_status.php # Update order status
```

## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- Apache/Nginx web server
- Backend API running on `http://localhost:8080`

### Installation

1. **Extract files to web server directory**:
   ```bash
   # For Apache
   cp -r frontend/* /var/www/html/clothesshop/
   
   # For local development with PHP built-in server
   cd frontend
   php -S localhost:8000
   ```

2. **Configure API endpoint** (if different):
   Edit `config.php`:
   ```php
   define('API_BASE_URL', 'http://your-backend-url:8080/api');
   ```

3. **Ensure proper permissions**:
   ```bash
   chmod 755 -R /var/www/html/clothesshop/
   ```

4. **Access the application**:
    - Customer frontend: `http://localhost:8000/`
    - Admin panel: `http://localhost:8000/admin/`

## Configuration

### API Configuration (`config.php`)

The `config.php` file contains:
- API base URL configuration
- Session management
- Helper function for API requests
- Currency formatting function

### Session Management

The application uses PHP sessions to:
- Store user ID (currently set to default user ID: 1)
- Maintain cart state
- Track user authentication (ready for implementation)

## Usage Guide

### Customer Flow

1. **Browse Products**:
    - Visit home page to see featured products
    - Use category navigation or search
    - Apply filters for refined results

2. **View Product Details**:
    - Click on any product card
    - View full details, price, availability
    - Select quantity and add to cart

3. **Manage Cart**:
    - View cart icon for item count
    - Go to cart page to review items
    - Update quantities or remove items

4. **Checkout**:
    - Fill in shipping information
    - Select payment method (Paystack/M-Pesa)
    - Complete order

5. **Track Orders**:
    - View order history
    - Check order status and details

### Admin Flow

1. **Dashboard**:
    - View statistics (products, orders, revenue)
    - Quick access to management functions
    - See recent orders

2. **Manage Products**:
    - View all products in table format
    - Add new products with full details
    - Edit existing products
    - Delete products
    - Manage stock levels

3. **Manage Orders**:
    - View all orders
    - Update order status
    - View order details

## API Integration

The frontend communicates with the backend API using the `makeApiRequest()` function:

```php
// GET request
$products = makeApiRequest('/products');

// POST request
$data = ['productId' => 1, 'quantity' => 2];
$response = makeApiRequest('/cart/1/items', 'POST', $data);

// PUT request
$response = makeApiRequest('/cart/1/items/5?quantity=3', 'PUT');

// DELETE request
$response = makeApiRequest('/cart/1/items/5', 'DELETE');
```

## Customization

### Styling

The application uses Bootstrap 5 with custom CSS. Main style variables are in `includes/header.php`:

```css
:root {
    --primary-color: #2c3e50;
    --secondary-color: #e74c3c;
    --accent-color: #3498db;
}
```

### Payment Integration

Currently integrated with **Paystack** for Kenyan market. To configure:

1. Update backend `application.properties` with Paystack credentials
2. Implement payment callback handler
3. The frontend automatically handles payment flow

For M-Pesa integration, backend implementation is required.

## Security Considerations

### For Production

1. **Add Authentication**:
    - Implement user registration/login
    - Use JWT tokens or PHP sessions
    - Protect admin routes

2. **CSRF Protection**:
    - Add CSRF tokens to forms
    - Validate tokens on submission

3. **Input Validation**:
    - Server-side validation for all inputs
    - SQL injection prevention (handled by backend)
    - XSS prevention with `htmlspecialchars()`

4. **HTTPS**:
    - Use SSL certificates
    - Force HTTPS redirects

5. **Environment Variables**:
    - Move API keys to `.env` file
    - Use environment-specific configs

## Responsive Design

The application is fully responsive using Bootstrap 5:
- Mobile-first approach
- Breakpoints: sm (576px), md (768px), lg (992px), xl (1200px)
- Hamburger menu for mobile navigation
- Responsive grid layout for products

## Browser Compatibility

Tested and compatible with:
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Troubleshooting

### Cart not updating
- Check if backend API is running
- Verify API endpoint in `config.php`
- Check browser console for errors

### Products not displaying
- Ensure backend has sample data
- Check network tab for API response
- Verify database connection in backend

### Payment issues
- Confirm Paystack credentials in backend
- Check payment gateway status
- Review error logs

## Future Enhancements

- [ ] User authentication and registration
- [ ] Wishlist functionality
- [ ] Product reviews and ratings
- [ ] Email notifications
- [ ] Advanced search with autocomplete
- [ ] Product image gallery
- [ ] Social media integration
- [ ] Multi-language support
- [ ] Progressive Web App (PWA)

## License

This project is for educational purposes.