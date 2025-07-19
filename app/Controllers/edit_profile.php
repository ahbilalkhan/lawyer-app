<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$page_title = 'Edit Profile';
include __DIR__ . '/../Views/header.php';
require_once __DIR__ . '/../Models/db.php';

$userId = $_SESSION['user_id'];
$success = false;
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    if (!$fullName || !$email) {
        $error = 'Full name and email are required.';
    } else {
        $stmt = mysqli_prepare($conn, 'UPDATE users SET full_name=?, email=?, phone=?, address=? WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'ssssi', $fullName, $email, $phone, $address, $userId);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['full_name'] = $fullName;
            $success = true;
        } else {
            $error = 'Database error: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
// Fetch user data
$stmt = mysqli_prepare($conn, 'SELECT username, full_name, email, phone, address FROM users WHERE id=?');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>
<div class="form-container">
    <h2>Edit Profile</h2>
    <form method="post" id="editProfileForm">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <textarea name="address" id="address" rows="2"><?php echo htmlspecialchars($user['address']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Save Changes</button>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($success): ?>
        showToast('Profile updated successfully!', 'success');
    <?php elseif ($error): ?>
        showToast('<?php echo addslashes($error); ?>', 'error');
    <?php endif; ?>
});
</script>
<?php include __DIR__ . '/../Views/footer.php'; ?> 