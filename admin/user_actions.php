<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
    $stmt = $pdo->prepare('SELECT id, username, full_name, email, user_type, is_active FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
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

try {
    if ($action === 'activate') {
        $stmt = $pdo->prepare('UPDATE users SET is_active = 1 WHERE id = ?');
        $stmt->execute([$userId]);
        echo json_encode(['success' => true, 'message' => 'User activated']);
    } elseif ($action === 'deactivate') {
        $stmt = $pdo->prepare('UPDATE users SET is_active = 0 WHERE id = ?');
        $stmt->execute([$userId]);
        echo json_encode(['success' => true, 'message' => 'User deactivated']);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$userId]);
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
        $stmt = $pdo->prepare('UPDATE users SET full_name=?, email=?, user_type=?, is_active=? WHERE id=?');
        $stmt->execute([$fullName, $email, $userType, $isActive, $userId]);
        echo json_encode(['success' => true, 'message' => 'User updated']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 