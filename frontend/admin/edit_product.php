<?php
session_start();
require_once '../config.php';
requireAdminAuth();

$productId = $_GET['id'] ?? null;

if (!$productId) {
    header('Location: products.php');
    exit;
}

// Fetch product details
$product = null;
$products = makeApiRequest('/products', 'GET', null, true);

if ($products && !empty($products)) {
    foreach ($products as $p) {
        if ($p['id'] == $productId) {
            $product = $p;
            break;
        }
    }
}

if (!$product) {
    header('Location: products.php');
    exit;
}

$pageTitle = 'Edit Product';
include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-edit me-2"></i>Edit Product</h2>
        <a href="products.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="editProductForm">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product Name *</label>
                        <input type="text" class="form-control" name="name" required
                               value="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Brand</label>
                        <input type="text" class="form-control" name="brand"
                               value="<?php echo htmlspecialchars($product['brand'] ?? ''); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-select" name="category" required>
                            <option value="">Select Category</option>
                            <option value="TOPS" <?php echo ($product['category'] === 'TOPS') ? 'selected' : ''; ?>>Tops</option>
                            <option value="BOTTOMS" <?php echo ($product['category'] === 'BOTTOMS') ? 'selected' : ''; ?>>Bottoms</option>
                            <option value="DRESSES" <?php echo ($product['category'] === 'DRESSES') ? 'selected' : ''; ?>>Dresses</option>
                            <option value="OUTERWEAR" <?php echo ($product['category'] === 'OUTERWEAR') ? 'selected' : ''; ?>>Outerwear</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sub Category</label>
                        <input type="text" class="form-control" name="subCategory"
                               placeholder="e.g., T-SHIRTS, JEANS"
                               value="<?php echo htmlspecialchars($product['subCategory'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="gender">
                            <option value="UNISEX" <?php echo ($product['gender'] === 'UNISEX' || !isset($product['gender'])) ? 'selected' : ''; ?>>Unisex</option>
                            <option value="MEN" <?php echo ($product['gender'] === 'MEN') ? 'selected' : ''; ?>>Men</option>
                            <option value="WOMEN" <?php echo ($product['gender'] === 'WOMEN') ? 'selected' : ''; ?>>Women</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Price (KSh) *</label>
                        <input type="number" class="form-control" name="price" step="0.01" min="0" required
                               value="<?php echo $product['price']; ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Stock Quantity *</label>
                        <input type="number" class="form-control" name="stockQuantity" min="0" required
                               value="<?php echo $product['stockQuantity']; ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Size</label>
                        <input type="text" class="form-control" name="size" placeholder="e.g., M, L, XL"
                               value="<?php echo htmlspecialchars($product['size'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Color</label>
                        <input type="text" class="form-control" name="color"
                               value="<?php echo htmlspecialchars($product['color'] ?? ''); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image URL</label>
                    <input type="url" class="form-control" name="imageUrl"
                           placeholder="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500"
                           value="<?php echo htmlspecialchars($product['imageUrl'] ?? ''); ?>">
                    <div class="form-text">
                        💡 <strong>Need image URLs?</strong>
                        <br>• Use <a href="https://unsplash.com" target="_blank">Unsplash</a> (Search → Right-click image → Copy image address)
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="isActive"
                               id="isActive" <?php echo ($product['isActive']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="isActive">
                            Active (visible to customers)
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Product
                </button>
                <a href="products.php" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </form>

            <div id="message" class="alert mt-3" style="display: none;"></div>
        </div>
    </div>
</div>

<script>
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    const productId = data.id;
    delete data.id; // Remove ID from data object

    // Convert numeric fields
    data.price = parseFloat(data.price);
    data.stockQuantity = parseInt(data.stockQuantity);
    data.isActive = formData.get('isActive') === 'on';

    const messageDiv = document.getElementById('message');

    fetch(`../ajax/admin/update_product.php?id=${productId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageDiv.className = 'alert alert-success';
            messageDiv.textContent = 'Product updated successfully!';
            messageDiv.style.display = 'block';

            setTimeout(() => {
                window.location.href = 'products.php';
            }, 1500);
        } else {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = data.message || 'Failed to update product';
            messageDiv.style.display = 'block';
        }
    })
    .catch(error => {
        messageDiv.className = 'alert alert-danger';
        messageDiv.textContent = 'An error occurred';
        messageDiv.style.display = 'block';
        console.error('Error:', error);
    });
});
</script>

<?php include '../includes/footer.php'; ?>
