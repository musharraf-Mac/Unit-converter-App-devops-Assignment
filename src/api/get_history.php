<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db_config.php';

try {
    $stmt = $pdo->prepare("
        SELECT input_value, from_unit, to_unit, result, conversion_type, created_at
        FROM conversion_history
        ORDER BY created_at DESC
        LIMIT 50
    ");
    
    $stmt->execute();
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($history);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>