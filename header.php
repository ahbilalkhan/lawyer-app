<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Determine correct asset path based on where the including script is
$baseDir = dirname($_SERVER['SCRIPT_NAME']);
$cssPath = ($baseDir === '/admin') ? '../css/style.css' : 'css/style.css';
$jsPath = ($baseDir === '/admin') ? '../js/main.js' : 'js/main.js';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>LawyerConnect</title>
    <link rel="stylesheet" href="<?php echo $cssPath; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">
                    <i class="fas fa-balance-scale"></i> LawyerConnect
                </a>
                
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="lawyers.php">Find Lawyers</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                        <li><a href="admin/dashboard.php">Admin</a></li>
                    <?php endif; ?>
                </ul>
                
                <div class="auth-buttons">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-user"></i> Dashboard
                        </a>
                        <a href="/logout.php" class="btn btn-primary">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-secondary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="register.php" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
