<?php
session_start();
require_once '../config.php';

// Get redirect parameter if present
$redirect = $_GET['redirect'] ?? 'home.php';

// If already logged in as admin, redirect to admin dashboard
if (isAuthenticated() && isAdmin()) {
    header('Location: ' . $redirect);
    exit;
}

// If logged in but not admin, show message
if (isAuthenticated() && !isAdmin()) {
    $error = 'You do not have admin access. Please log in with admin credentials.';
}

$error = $error ?? '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? 'home.php';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        // Call login API
        $response = makeApiRequest('/auth/login', 'POST', [
            'email' => $email,
            'password' => $password
        ]);

        if ($response && isset($response['accessToken'])) {
            // Login successful
            loginUser($response);

            // Check if user is admin
            if (isAdmin()) {
                header('Location: ' . $redirect);
                exit;
            } else {
                // Not admin, show error
                $error = 'You do not have admin access. Please log in with admin credentials.';
                session_destroy();
            }
        } else {
            $error = 'Invalid email or password';
        }
    }
}

$pageTitle = 'Admin Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Clothes Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            border-top: 4px solid #e74c3c;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: bold;
        }
        .logo p {
            color: #e74c3c;
            font-weight: 600;
            margin-top: 10px;
        }
        .admin-badge {
            display: inline-block;
            background-color: #e74c3c;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 62, 80, 0.4);
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .alert {
            border-radius: 5px;
        }
        .admin-info {
            background-color: #f8f9fa;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-user-shield" style="font-size: 40px; color: #2c3e50;"></i>
            <h1 style="margin-top: 10px;">Admin Panel</h1>
            <p>Administrator Access</p>
        </div>

        <div class="admin-badge">
            <i class="fas fa-lock me-1"></i>ADMIN ONLY
        </div>

        <div class="admin-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Admin Access Required</strong><br>
            Only administrators can access the admin panel. Please log in with your admin credentials.
        </div>

        <!-- Demo Credentials Card -->
        <div class="card border-primary mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-lock me-2"></i><strong>Demo Admin Credentials</strong>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Email:</strong>
                    <code class="bg-light p-2 rounded d-inline-block">admin@clothesshop.com</code>
                </p>
                <p class="mb-3">
                    <strong>Password:</strong>
                    <code class="bg-light p-2 rounded d-inline-block">password123</code>
                </p>
                <p class="text-muted small mb-0">
                    <i class="fas fa-info-circle me-1"></i>Use these credentials to access the admin panel and explore product management, order management, and dashboard features.
                </p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">

            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Admin Email
                </label>
                <input type="email" class="form-control" id="email" name="email" required
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       placeholder="admin@example.com">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Password
                </label>
                <input type="password" class="form-control" id="password" name="password" required
                       placeholder="Enter your admin password">
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-sign-in-alt me-2"></i>Admin Login
            </button>
        </form>

        <div class="back-link">
            <a href="../index.php" class="text-muted text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i>Back to Home
            </a>
            <br>
            <small class="text-muted">Not an admin?</small><br>
            <a href="../login.php" class="text-muted text-decoration-none">
                <i class="fas fa-sign-in-alt me-1"></i>Regular Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
