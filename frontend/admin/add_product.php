<?php
session_start();
require_once '../config.php';
requireAdminAuth();

$pageTitle = 'Add Product';
include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-plus me-2"></i>Add New Product</h2>
        <a href="products.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="addProductForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Brand</label>
                        <input type="text" class="form-control" name="brand">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-select" name="category" required>
                            <option value="">Select Category</option>
                            <option value="TOPS">Tops</option>
                            <option value="BOTTOMS">Bottoms</option>
                            <option value="DRESSES">Dresses</option>
                            <option value="OUTERWEAR">Outerwear</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sub Category</label>
                        <input type="text" class="form-control" name="subCategory"
                               placeholder="e.g., T-SHIRTS, JEANS">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="gender">
                            <option value="UNISEX">Unisex</option>
                            <option value="MEN">Men</option>
                            <option value="WOMEN">Women</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Price (KSh) *</label>
                        <input type="number" class="form-control" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Stock Quantity *</label>
                        <input type="number" class="form-control" name="stockQuantity" min="0" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Size</label>
                        <input type="text" class="form-control" name="size" placeholder="e.g., M, L, XL">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Color</label>
                        <input type="text" class="form-control" name="color">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image URL</label>
                    <input type="url" class="form-control" name="imageUrl"
                           placeholder="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500">
                    <div class="form-text">
                        💡 <strong>Need image URLs?</strong>
                        <br>• Use <a href="https://unsplash.com" target="_blank">Unsplash</a> (Search → Right-click image → Copy image address)
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="isActive"
                               id="isActive" checked>
                        <label class="form-check-label" for="isActive">
                            Active (visible to customers)
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Product
                </button>
            </form>

            <div id="message" class="alert mt-3" style="display: none;"></div>
        </div>
    </div>
</div>

<script>
document.getElementById('addProductForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    // Convert numeric fields
    data.price = parseFloat(data.price);
    data.stockQuantity = parseInt(data.stockQuantity);
    data.isActive = formData.get('isActive') === 'on';

    const messageDiv = document.getElementById('message');

    fetch('../ajax/admin/add_product.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageDiv.className = 'alert alert-success';
            messageDiv.textContent = 'Product added successfully!';
            messageDiv.style.display = 'block';

            setTimeout(() => {
                window.location.href = 'products.php';
            }, 1500);
        } else {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = data.message || 'Failed to add product';
            messageDiv.style.display = 'block';
        }
    })
    .catch(error => {
        messageDiv.className = 'alert alert-danger';
        messageDiv.textContent = 'An error occurred';
        messageDiv.style.display = 'block';
    });
});
</script>

<?php include '../includes/footer.php'; ?>