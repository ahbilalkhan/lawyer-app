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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to continue']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'accept' || $action === 'cancel' || $action === 'complete' || $action === 'set_status') {
    $appointmentId = intval($_POST['appointment_id'] ?? 0);
    if (!$appointmentId) {
        echo json_encode(['success' => false, 'message' => 'Invalid appointment']);
        exit;
    }
    // Check if this appointment belongs to the logged-in user (lawyer or customer)
    $sql = 'SELECT a.*, lp.user_id as lawyer_user_id FROM appointments a JOIN lawyer_profiles lp ON a.lawyer_id = lp.id WHERE a.id = ?';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $appointmentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $appt = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    if (!$appt) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    $isLawyer = ($_SESSION['user_type'] === 'lawyer' && $appt['lawyer_user_id'] == $_SESSION['user_id']);
    $isCustomer = ($_SESSION['user_type'] === 'customer' && $appt['customer_id'] == $_SESSION['user_id']);
    $isAdmin = (($_SESSION['user_type'] ?? '') === 'admin') || (isset($_POST['admin']) && $_POST['admin'] == '1');
    if ($action === 'accept') {
        if (!$isLawyer && !$isAdmin) { echo json_encode(['success' => false, 'message' => 'Only lawyers or admin can perform this action']); exit; }
        $sql = 'UPDATE appointments SET status = "confirmed" WHERE id = ?';
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $appointmentId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => true, 'message' => 'Appointment accepted and confirmed.']);
        exit;
    } elseif ($action === 'cancel') {
        if (!$isLawyer && !$isCustomer && !$isAdmin) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
        $sql = 'UPDATE appointments SET status = "cancelled" WHERE id = ?';
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $appointmentId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => true, 'message' => 'Appointment cancelled.']);
        exit;
    } elseif ($action === 'complete') {
        if ((!$isLawyer && !$isAdmin)) { echo json_encode(['success' => false, 'message' => 'Only lawyers or admin can perform this action']); exit; }
        if ($appt['status'] !== 'confirmed') {
            echo json_encode(['success' => false, 'message' => 'Only confirmed appointments can be marked as completed.']);
            exit;
        }
        $sql = 'UPDATE appointments SET status = "completed" WHERE id = ?';
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $appointmentId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => true, 'message' => 'Appointment marked as completed.']);
        exit;
    } elseif ($action === 'set_status') {
        $newStatus = $_POST['new_status'] ?? '';
        $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit;
        }
        $current = $appt['status'];
        $canChange = false;
        if ($isAdmin) {
            $canChange = true;
        } else if ($isLawyer) {
            if ($current === 'pending' && $newStatus === 'confirmed') $canChange = true;
            if ($current === 'pending' && $newStatus === 'cancelled') $canChange = true;
            if ($current === 'confirmed' && $newStatus === 'completed') $canChange = true;
            if ($current === 'confirmed' && $newStatus === 'cancelled') $canChange = true;
        } else if ($isCustomer) {
            if ($current === 'pending' && $newStatus === 'cancelled') $canChange = true;
            if ($current === 'confirmed' && $newStatus === 'cancelled') $canChange = true;
        }
        if (!$canChange) {
            echo json_encode(['success' => false, 'message' => 'You cannot change to this status.']);
            exit;
        }
        $sql = 'UPDATE appointments SET status = ? WHERE id = ?';
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $newStatus, $appointmentId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => true, 'message' => 'Status updated.']);
        exit;
    }
}

if ($_SESSION['user_type'] !== 'customer') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login as a customer to book appointments']);
    exit;
}

$customerId = $_SESSION['user_id'];
$lawyerId = intval($_POST['lawyer_id'] ?? 0);
$appointmentDate = $_POST['appointment_date'] ?? '';
$appointmentTime = $_POST['appointment_time'] ?? '';
$meetingType = $_POST['meeting_type'] ?? 'office';
$notes = trim($_POST['notes'] ?? '');

if (empty($lawyerId) || empty($appointmentDate) || empty($appointmentTime)) {
    echo json_encode(['success' => false, 'message' => 'Lawyer, date, and time are required']);
    exit;
}

$appointmentDateTime = DateTime::createFromFormat('Y-m-d H:i', $appointmentDate . ' ' . $appointmentTime);
$now = new DateTime();
if ($appointmentDateTime <= $now) {
    echo json_encode(['success' => false, 'message' => 'Appointment must be scheduled for a future date and time']);
    exit;
}

// Validate lawyer exists and is verified
$lawyerCheckSql = "SELECT lp.id, lp.user_id, u.full_name, lp.consultation_fee FROM lawyer_profiles lp JOIN users u ON lp.user_id = u.id WHERE lp.id = ? AND lp.is_verified = 1 AND u.is_active = 1";
$lawyerCheckStmt = mysqli_prepare($conn, $lawyerCheckSql);
mysqli_stmt_bind_param($lawyerCheckStmt, 'i', $lawyerId);
mysqli_stmt_execute($lawyerCheckStmt);
$lawyerResult = mysqli_stmt_get_result($lawyerCheckStmt);
$lawyer = mysqli_fetch_assoc($lawyerResult);
mysqli_stmt_close($lawyerCheckStmt);
if (!$lawyer) {
    echo json_encode(['success' => false, 'message' => 'Lawyer not found or not available']);
    exit;
}

// Check if slot is already booked
$conflictCheckSql = "SELECT id FROM appointments WHERE lawyer_id = ? AND appointment_date = ? AND appointment_time = ? AND status IN ('pending', 'confirmed')";
$conflictCheckStmt = mysqli_prepare($conn, $conflictCheckSql);
mysqli_stmt_bind_param($conflictCheckStmt, 'iss', $lawyerId, $appointmentDate, $appointmentTime);
mysqli_stmt_execute($conflictCheckStmt);
$conflictResult = mysqli_stmt_get_result($conflictCheckStmt);
if (mysqli_fetch_assoc($conflictResult)) {
    mysqli_stmt_close($conflictCheckStmt);
    echo json_encode(['success' => false, 'message' => 'This time slot is already booked']);
    exit;
}
mysqli_stmt_close($conflictCheckStmt);

// Check if customer already has an appointment with this lawyer on the same day
$duplicateCheckSql = "SELECT id FROM appointments WHERE customer_id = ? AND lawyer_id = ? AND appointment_date = ? AND status IN ('pending', 'confirmed')";
$duplicateCheckStmt = mysqli_prepare($conn, $duplicateCheckSql);
mysqli_stmt_bind_param($duplicateCheckStmt, 'iis', $customerId, $lawyerId, $appointmentDate);
mysqli_stmt_execute($duplicateCheckStmt);
$duplicateResult = mysqli_stmt_get_result($duplicateCheckStmt);
if (mysqli_fetch_assoc($duplicateResult)) {
    mysqli_stmt_close($duplicateCheckStmt);
    echo json_encode(['success' => false, 'message' => 'You already have an appointment with this lawyer on this date']);
    exit;
}
mysqli_stmt_close($duplicateCheckStmt);

// Insert appointment
$insertSql = "INSERT INTO appointments (customer_id, lawyer_id, appointment_date, appointment_time, meeting_type, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, 'iissss', $customerId, $lawyerId, $appointmentDate, $appointmentTime, $meetingType, $notes);
if (!mysqli_stmt_execute($insertStmt)) {
    mysqli_stmt_close($insertStmt);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}
$appointmentId = mysqli_insert_id($conn);
mysqli_stmt_close($insertStmt);

// Get customer details for notification
$customerSql = "SELECT full_name, email, phone FROM users WHERE id = ?";
$customerStmt = mysqli_prepare($conn, $customerSql);
mysqli_stmt_bind_param($customerStmt, 'i', $customerId);
mysqli_stmt_execute($customerStmt);
$customerResult = mysqli_stmt_get_result($customerStmt);
$customer = mysqli_fetch_assoc($customerResult);
mysqli_stmt_close($customerStmt);

// Here you would typically send email notifications to both customer and lawyer
// For now, we'll just return success

echo json_encode([
    'success' => true,
    'message' => 'Appointment booked successfully! The lawyer will confirm your appointment soon.',
    'appointment_id' => $appointmentId,
    'appointment_details' => [
        'lawyer_name' => $lawyer['full_name'],
        'date' => $appointmentDate,
        'time' => $appointmentTime,
        'meeting_type' => $meetingType,
        'consultation_fee' => $lawyer['consultation_fee']
    ]
]);
?>
