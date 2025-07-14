<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

try {
    $sql = "
        SELECT 
            lp.id,
            u.full_name,
            lp.specialization,
            lp.experience_years,
            lp.location,
            lp.consultation_fee,
            lp.rating,
            lp.total_reviews,
            lp.is_verified,
            lp.availability_status,
            GROUP_CONCAT(DISTINCT ls.service_type) as services
        FROM lawyer_profiles lp
        JOIN users u ON lp.user_id = u.id
        LEFT JOIN lawyer_services ls ON lp.id = ls.lawyer_id
        WHERE u.is_active = 1 AND lp.is_verified = 1
        GROUP BY lp.id
        ORDER BY lp.rating DESC, lp.total_reviews DESC
        LIMIT 20
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $lawyers = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $lawyers
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
