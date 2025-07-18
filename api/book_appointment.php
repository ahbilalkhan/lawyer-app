<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

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

if ($action === 'accept' || $action === 'cancel') {
    // Only lawyers can accept/cancel
    if ($_SESSION['user_type'] !== 'lawyer') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Only lawyers can perform this action']);
        exit;
    }
    $appointmentId = intval($_POST['appointment_id'] ?? 0);
    if (!$appointmentId) {
        echo json_encode(['success' => false, 'message' => 'Invalid appointment']);
        exit;
    }
    // Check if this appointment belongs to the logged-in lawyer
    $stmt = $pdo->prepare('SELECT a.*, lp.user_id as lawyer_user_id FROM appointments a JOIN lawyer_profiles lp ON a.lawyer_id = lp.id WHERE a.id = ?');
    $stmt->execute([$appointmentId]);
    $appt = $stmt->fetch();
    if (!$appt || $appt['lawyer_user_id'] != $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    if ($action === 'accept') {
        $stmt = $pdo->prepare('UPDATE appointments SET status = "confirmed" WHERE id = ?');
        $stmt->execute([$appointmentId]);
        echo json_encode(['success' => true, 'message' => 'Appointment accepted and confirmed.']);
        exit;
    } elseif ($action === 'cancel') {
        $stmt = $pdo->prepare('UPDATE appointments SET status = "cancelled" WHERE id = ?');
        $stmt->execute([$appointmentId]);
        echo json_encode(['success' => true, 'message' => 'Appointment cancelled.']);
        exit;
    }
}

// Only customers can book
if ($_SESSION['user_type'] !== 'customer') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login as a customer to book appointments']);
    exit;
}

try {
    $customerId = $_SESSION['user_id'];
    $lawyerId = intval($_POST['lawyer_id'] ?? 0);
    $appointmentDate = $_POST['appointment_date'] ?? '';
    $appointmentTime = $_POST['appointment_time'] ?? '';
    $meetingType = $_POST['meeting_type'] ?? 'office';
    $notes = trim($_POST['notes'] ?? '');
    
    // Validation
    if (empty($lawyerId) || empty($appointmentDate) || empty($appointmentTime)) {
        echo json_encode(['success' => false, 'message' => 'Lawyer, date, and time are required']);
        exit;
    }
    
    // Validate date (must be in the future)
    $appointmentDateTime = DateTime::createFromFormat('Y-m-d H:i', $appointmentDate . ' ' . $appointmentTime);
    $now = new DateTime();
    
    if ($appointmentDateTime <= $now) {
        echo json_encode(['success' => false, 'message' => 'Appointment must be scheduled for a future date and time']);
        exit;
    }
    
    // Validate lawyer exists and is verified
    $lawyerCheckSql = "SELECT lp.id, lp.user_id, u.full_name, lp.consultation_fee 
                       FROM lawyer_profiles lp 
                       JOIN users u ON lp.user_id = u.id 
                       WHERE lp.id = ? AND lp.is_verified = 1 AND u.is_active = 1";
    $lawyerCheckStmt = $pdo->prepare($lawyerCheckSql);
    $lawyerCheckStmt->execute([$lawyerId]);
    $lawyer = $lawyerCheckStmt->fetch();
    
    if (!$lawyer) {
        echo json_encode(['success' => false, 'message' => 'Lawyer not found or not available']);
        exit;
    }
    
    // Check if slot is already booked
    $conflictCheckSql = "SELECT id FROM appointments 
                        WHERE lawyer_id = ? AND appointment_date = ? AND appointment_time = ? 
                        AND status IN ('pending', 'confirmed')";
    $conflictCheckStmt = $pdo->prepare($conflictCheckSql);
    $conflictCheckStmt->execute([$lawyerId, $appointmentDate, $appointmentTime]);
    
    if ($conflictCheckStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'This time slot is already booked']);
        exit;
    }
    
    // Check if customer already has an appointment with this lawyer on the same day
    $duplicateCheckSql = "SELECT id FROM appointments 
                         WHERE customer_id = ? AND lawyer_id = ? AND appointment_date = ? 
                         AND status IN ('pending', 'confirmed')";
    $duplicateCheckStmt = $pdo->prepare($duplicateCheckSql);
    $duplicateCheckStmt->execute([$customerId, $lawyerId, $appointmentDate]);
    
    if ($duplicateCheckStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You already have an appointment with this lawyer on this date']);
        exit;
    }
    
    // Insert appointment
    $insertSql = "INSERT INTO appointments 
                  (customer_id, lawyer_id, appointment_date, appointment_time, meeting_type, notes, status) 
                  VALUES (?, ?, ?, ?, ?, ?, 'pending')";
    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([$customerId, $lawyerId, $appointmentDate, $appointmentTime, $meetingType, $notes]);
    
    $appointmentId = $pdo->lastInsertId();
    
    // Get customer details for notification
    $customerSql = "SELECT full_name, email, phone FROM users WHERE id = ?";
    $customerStmt = $pdo->prepare($customerSql);
    $customerStmt->execute([$customerId]);
    $customer = $customerStmt->fetch();
    
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
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
