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

// Get form data
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$userType = $_POST['user_type'] ?? 'customer';
$fullName = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

// Validation
if (empty($username) || empty($email) || empty($password) || empty($fullName) || empty($phone) || empty($address)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Check if username or email already exists
$checkSql = "SELECT id FROM users WHERE username = ? OR email = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, 'ss', $username, $email);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
if (mysqli_fetch_assoc($checkResult)) {
    echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
    mysqli_stmt_close($checkStmt);
    exit;
}
mysqli_stmt_close($checkStmt);

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Start transaction
mysqli_begin_transaction($conn);
$commit = true;

// Insert user
$userSql = "INSERT INTO users (username, email, password, user_type, full_name, phone, address) VALUES (?, ?, ?, ?, ?, ?, ?)";
$userStmt = mysqli_prepare($conn, $userSql);
mysqli_stmt_bind_param($userStmt, 'sssssss', $username, $email, $hashedPassword, $userType, $fullName, $phone, $address);
if (!mysqli_stmt_execute($userStmt)) {
    $commit = false;
}
$userId = mysqli_insert_id($conn);
mysqli_stmt_close($userStmt);

// If lawyer, insert lawyer profile
if ($commit && $userType === 'lawyer') {
    $specialization = trim($_POST['specialization'] ?? '');
    $experienceYears = intval($_POST['experience_years'] ?? 0);
    $licenseNumber = trim($_POST['license_number'] ?? '');
    $education = trim($_POST['education'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $consultationFee = floatval($_POST['consultation_fee'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $officeAddress = trim($_POST['office_address'] ?? '');
    $services = $_POST['services'] ?? [];

    // Validate lawyer-specific fields
    if (empty($specialization) || empty($licenseNumber) || empty($location)) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'Specialization, license number, and location are required for lawyers']);
        exit;
    }

    // Check if license number already exists
    $licenseCheckSql = "SELECT id FROM lawyer_profiles WHERE license_number = ?";
    $licenseCheckStmt = mysqli_prepare($conn, $licenseCheckSql);
    mysqli_stmt_bind_param($licenseCheckStmt, 's', $licenseNumber);
    mysqli_stmt_execute($licenseCheckStmt);
    $licenseCheckResult = mysqli_stmt_get_result($licenseCheckStmt);
    if (mysqli_fetch_assoc($licenseCheckResult)) {
        mysqli_stmt_close($licenseCheckStmt);
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'License number already exists']);
        exit;
    }
    mysqli_stmt_close($licenseCheckStmt);

    // Insert lawyer profile
    $lawyerSql = "INSERT INTO lawyer_profiles (user_id, specialization, experience_years, license_number, education, bio, consultation_fee, location, office_address, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, FALSE)";
    $lawyerStmt = mysqli_prepare($conn, $lawyerSql);
    mysqli_stmt_bind_param($lawyerStmt, 'isisssdss', $userId, $specialization, $experienceYears, $licenseNumber, $education, $bio, $consultationFee, $location, $officeAddress);
    if (!mysqli_stmt_execute($lawyerStmt)) {
        $commit = false;
    }
    $lawyerId = mysqli_insert_id($conn);
    mysqli_stmt_close($lawyerStmt);

    // Insert lawyer services
    if ($commit && !empty($services)) {
        $serviceSql = "INSERT INTO lawyer_services (lawyer_id, service_type) VALUES (?, ?)";
        $serviceStmt = mysqli_prepare($conn, $serviceSql);
        foreach ($services as $service) {
            mysqli_stmt_bind_param($serviceStmt, 'is', $lawyerId, $service);
            if (!mysqli_stmt_execute($serviceStmt)) {
                $commit = false;
                break;
            }
        }
        mysqli_stmt_close($serviceStmt);
    }

    // Insert default time slots (9 AM to 5 PM, Monday to Friday)
    if ($commit) {
        $timeSlotSql = "INSERT INTO time_slots (lawyer_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)";
        $timeSlotStmt = mysqli_prepare($conn, $timeSlotSql);
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        foreach ($days as $day) {
            mysqli_stmt_bind_param($timeSlotStmt, 'isss', $lawyerId, $day, $start = '09:00:00', $end = '17:00:00');
            if (!mysqli_stmt_execute($timeSlotStmt)) {
                $commit = false;
                break;
            }
        }
        mysqli_stmt_close($timeSlotStmt);
    }
}

if ($commit) {
    mysqli_commit($conn);
    // Set session variables
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['user_type'] = $userType;
    $_SESSION['full_name'] = $fullName;
    $message = $userType === 'lawyer' ? 'Registration successful! Your lawyer profile is pending verification.' : 'Registration successful!';
    echo json_encode([
        'success' => true,
        'message' => $message,
        'user_type' => $userType,
        'redirect' => $userType === 'admin' ? 'admin/dashboard.php' : 'dashboard.php'
    ]);
} else {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: Registration failed.'
    ]);
}
?>
