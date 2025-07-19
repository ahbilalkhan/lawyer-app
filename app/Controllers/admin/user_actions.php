<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
require_once __DIR__ . '/../../Models/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
    $stmt = mysqli_prepare($conn, 'SELECT id, username, full_name, email, user_type, is_active FROM users WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    if ($user) {
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
    exit;
}

$action = $_POST['action'] ?? '';
$userId = intval($_POST['user_id'] ?? 0);
if (!$userId || !in_array($action, ['activate', 'deactivate', 'delete', 'edit'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

if ($action === 'activate') {
    $stmt = mysqli_prepare($conn, 'UPDATE users SET is_active = 1 WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true, 'message' => 'User activated']);
} elseif ($action === 'deactivate') {
    $stmt = mysqli_prepare($conn, 'UPDATE users SET is_active = 0 WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true, 'message' => 'User deactivated']);
} elseif ($action === 'delete') {
    $stmt = mysqli_prepare($conn, 'DELETE FROM users WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true, 'message' => 'User deleted']);
} elseif ($action === 'edit') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $userType = $_POST['user_type'] ?? '';
    $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
    if (!$fullName || !$email || !in_array($userType, ['customer','lawyer','admin'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }
    $stmt = mysqli_prepare($conn, 'UPDATE users SET full_name=?, email=?, user_type=?, is_active=? WHERE id=?');
    mysqli_stmt_bind_param($stmt, 'sssii', $fullName, $email, $userType, $isActive, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true, 'message' => 'User updated']);
}
?> 