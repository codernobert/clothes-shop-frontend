# Test Paystack Payment Initialization

Write-Host "Testing Paystack Payment Integration..." -ForegroundColor Cyan
Write-Host ""

# Test 1: Simple payment initialization
Write-Host "Test 1: Initialize payment with KES currency (MPESA support)" -ForegroundColor Yellow

$body = @{
    amount = 10000
    currency = "KES"
    email = "test@example.com"
    description = "Test Order"
} | ConvertTo-Json

Write-Host "Request body:" -ForegroundColor Gray
Write-Host $body -ForegroundColor Gray
Write-Host ""

try {
    $response = Invoke-RestMethod -Method Post `
        -Uri "http://localhost:8080/api/checkout/payment-intent" `
        -ContentType "application/json" `
        -Body $body `
        -ErrorAction Stop

    Write-Host "✅ Success!" -ForegroundColor Green
    Write-Host "Response:" -ForegroundColor Green
    $response | ConvertTo-Json -Depth 10

    if ($response.data.authorizationUrl) {
        Write-Host ""
        Write-Host "🌐 Authorization URL: $($response.data.authorizationUrl)" -ForegroundColor Cyan
        Write-Host "💡 Copy this URL and open in browser to test payment" -ForegroundColor Cyan
    }
} catch {
    Write-Host "❌ Error occurred!" -ForegroundColor Red
    Write-Host "Status Code: $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
    Write-Host "Error Message: $($_.Exception.Message)" -ForegroundColor Red

    # Try to get response body
    try {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response Body:" -ForegroundColor Red
        Write-Host $responseBody -ForegroundColor Red
    } catch {
        Write-Host "Could not read error response body" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Test 2: Different amount
Write-Host "Test 2: Initialize payment with larger amount" -ForegroundColor Yellow

$body2 = @{
    amount = 500000
    currency = "KES"
    email = "customer@example.com"
    description = "Order #12345"
} | ConvertTo-Json

try {
    $response2 = Invoke-RestMethod -Method Post `
        -Uri "http://localhost:8080/api/checkout/payment-intent" `
        -ContentType "application/json" `
        -Body $body2 `
        -ErrorAction Stop

    Write-Host "✅ Success!" -ForegroundColor Green
    Write-Host "Payment Intent ID: $($response2.data.paymentIntentId)" -ForegroundColor Green
} catch {
    Write-Host "❌ Error occurred!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Testing complete!" -ForegroundColor Cyan

