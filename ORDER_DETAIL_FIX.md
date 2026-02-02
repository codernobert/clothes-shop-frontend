# Order Detail Pages - API Response Handling Fix

## Problem
The order detail pages were not displaying order information because:
1. The API returns wrapped responses with a `data` property: `{ data: {...}, success: true }`
2. The frontend was trying to access order properties directly from the API response
3. This resulted in undefined array keys and missing order data

## Solution
Updated both `order_detail.php` files (customer and admin) to:
1. Extract the `data` wrapper from the API response
2. Handle both wrapped responses and direct responses gracefully
3. Display proper error messages when data is missing
4. Use null coalescing operators (`??`) for all field access to prevent deprecation warnings

## Updated Files
- `frontend/order_detail.php` (Customer page)
- `frontend/admin/order_detail.php` (Admin page)

## Changes Made

### API Response Handling
```php
// Before:
$order = makeApiRequest('/orders/' . $orderId, 'GET', null, true);

// After:
$apiResponse = makeApiRequest('/orders/' . $orderId, 'GET', null, true);

// Extract data from wrapped response
$order = null;
if ($apiResponse && is_array($apiResponse)) {
    if (isset($apiResponse['data']) && is_array($apiResponse['data'])) {
        $order = $apiResponse['data'];
    }
    elseif (!isset($apiResponse['success'])) {
        $order = $apiResponse;
    }
}
```

### Error Handling
- Checks for missing `id` field in order data
- Displays API error messages when available
- Shows appropriate error messages to users
- Provides clear back button to orders list

### Safety Improvements
- All array accesses use null coalescing operator (`??`)
- Empty checks before foreach loops
- Proper type validation before processing

## Result
✅ Order detail pages now correctly display:
- Order information (number, status, date)
- Order items with quantities and prices
- Shipping address and payment method
- Payment and order status
- Order summary with total amounts

✅ No PHP deprecation warnings
✅ Graceful error handling for missing/invalid orders
✅ Proper security checks for user order access
