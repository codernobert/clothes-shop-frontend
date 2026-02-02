# Order Detail Pages - Testing Guide

## How to Test

### Customer Order Detail Page
```
URL: http://localhost:8000/order_detail.php?id=38
```

**Requirements:**
- Must be logged in as a customer
- The order ID (38 in this example) must exist in the database
- The logged-in user must own the order

**Expected Output:**
- Order number and status badge
- Order date
- Payment status
- Total amount
- List of order items with quantities and prices
- Shipping address
- Payment method
- Order summary with totals

### Admin Order Detail Page
```
URL: http://localhost:8000/admin/order_detail.php?id=38
```

**Requirements:**
- Must be logged in as an admin user
- The order ID must exist in the database

**Expected Features:**
- All customer page features
- Ability to change order status (dropdown selector)
- Customer ID displayed
- Order status update functionality

## API Endpoints Used

### Customer View
```
GET /api/orders/{orderId}
Headers: Authorization: Bearer {access_token}
```

### Admin View
```
GET /api/admin/orders/{orderId}
Headers: Authorization: Bearer {admin_access_token}
```

## Response Structure
Both endpoints return responses wrapped in a data object:
```json
{
  "success": true,
  "data": {
    "id": 38,
    "orderNumber": "ORD-12345",
    "userId": 1,
    "status": "PROCESSING",
    "paymentStatus": "COMPLETED",
    "totalAmount": 299.99,
    "shippingAddress": "123 Main St",
    "paymentMethod": "Paystack",
    "createdAt": "2024-02-01T10:30:00",
    "items": [
      {
        "productName": "T-Shirt",
        "quantity": 2,
        "price": 49.99,
        "subtotal": 99.98
      }
    ]
  }
}
```

## Troubleshooting

### Issue: "Order not found"
- **Cause:** Order ID doesn't exist or API returned null
- **Solution:** Check that the order ID exists in the database

### Issue: "You do not have permission to view this order"
- **Cause:** Customer trying to view someone else's order
- **Solution:** Only users can view their own orders (unless admin)

### Issue: Missing order information
- **Cause:** API response missing expected fields
- **Solution:** Check backend API response structure, ensure all required fields are returned

### Issue: Blank page or error
- **Check:**
  1. Are you logged in?
  2. Does the order ID exist?
  3. Is the API running and accessible?
  4. Check browser console for JavaScript errors
