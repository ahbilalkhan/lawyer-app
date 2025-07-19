<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Get the current file path to determine if we're in public/ or need to adjust paths
$isInPublicFolder = (strpos($_SERVER['SCRIPT_NAME'], '/public/') !== false);
$basePath = $isInPublicFolder ? '' : 'public/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>LawyerConnect</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="/public/img/favicon.webp">
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
                </ul>
                
                <div class="auth-buttons">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-avatar" style="display:inline-block;vertical-align:middle;margin-right:10px;">
                            <?php if (!empty($_SESSION['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Avatar" style="width:40px;height:40px;object-fit:cover;border-radius:50%;border:2px solid #ccc;" />
                            <?php else: ?>
                                <div style="width:40px;height:40px;background:#888;color:#fff;display:flex;align-items:center;justify-content:center;border-radius:50%;font-weight:bold;font-size:18px;border:2px solid #ccc;">
                                    <?php
                                        $initials = '';
                                        if (!empty($_SESSION['full_name'])) {
                                            $names = explode(' ', $_SESSION['full_name']);
                                            $initials .= strtoupper(substr($names[0], 0, 1));
                                            if (count($names) > 1) {
                                                $initials .= strtoupper(substr($names[count($names)-1], 0, 1));
                                            }
                                        }
                                        echo $initials;
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
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
