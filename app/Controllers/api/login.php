<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../Models/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

$sql = "SELECT id, username, email, password, user_type, full_name, is_active FROM users WHERE username = ? OR email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ss', $username, $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

if (!$user['is_active']) {
    echo json_encode(['success' => false, 'message' => 'Account is deactivated']);
    exit;
}

if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid password']);
    exit;
}

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['user_type'] = $user['user_type'];
$_SESSION['full_name'] = $user['full_name'];

// Determine redirect based on user type
$redirect = 'dashboard.php';
if ($user['user_type'] === 'admin') {
    $redirect = 'admin/dashboard.php';
}

echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'redirect' => $redirect,
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'user_type' => $user['user_type'],
        'full_name' => $user['full_name']
    ]
]);
?>
