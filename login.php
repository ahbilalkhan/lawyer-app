<?php
$page_title = "Login";
include 'header.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>

<div class="form-container">
    <h2><i class="fas fa-sign-in-alt"></i> Login to Your Account</h2>
    
    <form id="loginForm" method="POST">
        <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-full">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>
    
    <div class="form-footer">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
        <p><a href="forgot-password.php">Forgot your password?</a></p>
    </div>
</div>

<div class="demo-accounts">
    <h3>Demo Accounts</h3>
    <div class="demo-grid">
        <div class="demo-account">
            <h4>Admin Account</h4>
            <p><strong>Username:</strong> admin</p>
            <p><strong>Password:</strong> password</p>
        </div>
        <div class="demo-account">
            <h4>Lawyer Account</h4>
            <p><strong>Username:</strong> john_lawyer</p>
            <p><strong>Password:</strong> password</p>
        </div>
        <div class="demo-account">
            <h4>Customer Account</h4>
            <p><strong>Username:</strong> customer</p>
            <p><strong>Password:</strong> password</p>
        </div>
    </div>
</div>

<style>
.form-footer {
    text-align: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
}

.form-footer a {
    color: #3498db;
    text-decoration: none;
}

.form-footer a:hover {
    text-decoration: underline;
}

.demo-accounts {
    max-width: 800px;
    margin: 2rem auto;
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.demo-accounts h3 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 1.5rem;
}

.demo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.demo-account {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.demo-account h4 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.demo-account p {
    margin-bottom: 0.5rem;
}
</style>

<?php include 'footer.php'; ?>
