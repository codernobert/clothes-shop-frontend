<?php
require_once dirname(__DIR__) . '/config.php';

// Detect if we're in a subdirectory
$basePath = '';
if (basename(dirname($_SERVER['SCRIPT_FILENAME'])) !== 'frontend') {
    $basePath = '../';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($pageTitle) && $pageTitle
                ? "Molly’s Clothing Line | $pageTitle"
                : "Molly’s Clothing Line";
        ?>
    </title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --accent-color: #3498db;
            --navbar-height: 60px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding-top: var(--navbar-height);
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: #fff !important;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: #fff !important;
            transform: translateY(-2px);
        }

        .cart-badge {
            background-color: var(--secondary-color);
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
        }

        .product-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .product-image {
            height: 250px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .btn-primary {
            background-color: var(--accent-color);
            border: none;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        .btn-danger {
            background-color: var(--secondary-color);
            border: none;
        }

        .price-tag {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .footer {
            background-color: var(--primary-color);
            color: white;
            margin-top: 50px;
            padding: 30px 0;
            clear: both;
            position: relative;
            z-index: 1;
        }

        .filter-section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
        }

        .category-badge {
            display: inline-block;
            padding: 5px 15px;
            background-color: rgba(52, 152, 219, 0.1);
            border-radius: 20px;
            font-size: 0.85rem;
            color: var(--accent-color);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <?php
            // Determine home link based on user role and current directory
            if (isAuthenticated() && isAdmin()) {
                // If in admin directory, use relative path to admin/home.php
                if ($basePath === '../') {
                    $homeLink = 'home.php';
                } else {
                    $homeLink = $basePath . 'admin/home.php';
                }
            } else {
                $homeLink = $basePath . 'index.php';
            }
            ?>
            <a class="navbar-brand" href="<?php echo $homeLink; ?>">
                <i class="fas fa-shopping-bag me-2"></i>Clothes Shop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $homeLink; ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>products.php">Products</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown">
                            Categories
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $basePath; ?>products.php?category=TOPS">Tops</a></li>
                            <li><a class="dropdown-item" href="<?php echo $basePath; ?>products.php?category=BOTTOMS">Bottoms</a></li>
                            <li><a class="dropdown-item" href="<?php echo $basePath; ?>products.php?category=DRESSES">Dresses</a></li>
                            <li><a class="dropdown-item" href="<?php echo $basePath; ?>products.php?category=OUTERWEAR">Outerwear</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>orders.php">My Orders</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isAuthenticated()):
                        $user = getCurrentUser();
                    ?>
                        <!-- Authenticated User Menu - Horizontal Layout -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>orders.php">
                                <i class="fas fa-box me-1"></i>My Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>cart.php">
                                <i class="fas fa-shopping-cart me-1"></i>Cart
                                <span class="cart-badge" id="cartCount">0</span>
                            </a>
                        </li>
                        <?php if ($user['role'] === 'ADMIN'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>admin/index.php">
                                <i class="fas fa-user-shield me-1"></i>Admin Panel
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>admin/index.php">
                                <i class="fas fa-tachometer-alt me-1"></i>Admin Dashboard
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo htmlspecialchars($user['firstName'] ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo $basePath; ?>logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Guest User Menu -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>register.php">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <script>
        const basePath = '<?php echo $basePath; ?>';

        // Update cart count - Make globally accessible
        function updateCartCount() {
            // Only try to update if user is authenticated (check if cartCount element exists)
            const badge = document.getElementById('cartCount');
            if (!badge) {
                console.log('Cart badge element not found');
                return;
            }

            fetch(basePath + 'ajax/get_cart_count.php')
                .then(response => {
                    if (!response.ok) {
                        console.error('Cart count response error:', response.status);
                        return null;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.count !== undefined) {
                        console.log('Cart count updated to:', data.count);
                        badge.textContent = data.count;
                        // Show badge only if count > 0
                        if (data.count > 0) {
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'inline-block';
                        }
                    } else {
                        console.error('Invalid cart count response:', data);
                    }
                })
                .catch(error => {
                    console.error('Error updating cart count:', error);
                    // Show 0 if there's an error
                    badge.textContent = '0';
                });
        }

        // Make updateCartCount globally available
        window.updateCartCount = updateCartCount;

        // Update on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded - updating cart count');
            updateCartCount();
        });

        // Also try immediate update
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', updateCartCount);
        } else {
            // DOM is already loaded
            updateCartCount();
        }

        // Also listen for custom cart update events
        document.addEventListener('cartUpdated', function() {
            console.log('cartUpdated event received - updating badge');
            updateCartCount();
        });
    </script>