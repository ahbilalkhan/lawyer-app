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

try {
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
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$username, $email]);
    
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Insert user
    $userSql = "INSERT INTO users (username, email, password, user_type, full_name, phone, address) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
    $userStmt = $pdo->prepare($userSql);
    $userStmt->execute([$username, $email, $hashedPassword, $userType, $fullName, $phone, $address]);
    
    $userId = $pdo->lastInsertId();
    
    // If lawyer, insert lawyer profile
    if ($userType === 'lawyer') {
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
            $pdo->rollback();
            echo json_encode(['success' => false, 'message' => 'Specialization, license number, and location are required for lawyers']);
            exit;
        }
        
        // Check if license number already exists
        $licenseCheckSql = "SELECT id FROM lawyer_profiles WHERE license_number = ?";
        $licenseCheckStmt = $pdo->prepare($licenseCheckSql);
        $licenseCheckStmt->execute([$licenseNumber]);
        
        if ($licenseCheckStmt->fetch()) {
            $pdo->rollback();
            echo json_encode(['success' => false, 'message' => 'License number already exists']);
            exit;
        }
        
        // Insert lawyer profile
        $lawyerSql = "INSERT INTO lawyer_profiles 
                      (user_id, specialization, experience_years, license_number, education, bio, 
                       consultation_fee, location, office_address, is_verified) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, FALSE)";
        $lawyerStmt = $pdo->prepare($lawyerSql);
        $lawyerStmt->execute([
            $userId, $specialization, $experienceYears, $licenseNumber, $education, 
            $bio, $consultationFee, $location, $officeAddress
        ]);
        
        $lawyerId = $pdo->lastInsertId();
        
        // Insert lawyer services
        if (!empty($services)) {
            $serviceSql = "INSERT INTO lawyer_services (lawyer_id, service_type) VALUES (?, ?)";
            $serviceStmt = $pdo->prepare($serviceSql);
            
            foreach ($services as $service) {
                $serviceStmt->execute([$lawyerId, $service]);
            }
        }
        
        // Insert default time slots (9 AM to 5 PM, Monday to Friday)
        $timeSlotSql = "INSERT INTO time_slots (lawyer_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)";
        $timeSlotStmt = $pdo->prepare($timeSlotSql);
        
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        foreach ($days as $day) {
            $timeSlotStmt->execute([$lawyerId, $day, '09:00:00', '17:00:00']);
        }
    }
    
    // Commit transaction
    $pdo->commit();
    
    // Set session variables
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['user_type'] = $userType;
    $_SESSION['full_name'] = $fullName;
    
    $message = $userType === 'lawyer' ? 
        'Registration successful! Your lawyer profile is pending verification.' : 
        'Registration successful!';
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'user_type' => $userType,
        'redirect' => $userType === 'admin' ? 'admin/dashboard.php' : 'dashboard.php'
    ]);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
