<?php
$host = 'localhost';
$db   = 'lawyer_db';
$user = 'root'; 
$pass = '';
$charset = 'utf8mb4';

// Create mysqli connection (procedural)
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

// Set charset
if (!mysqli_set_charset($conn, $charset)) {
    http_response_code(500);
    echo json_encode(['error' => 'Error loading character set ' . $charset . ': ' . mysqli_error($conn)]);
    exit;
}
?>
