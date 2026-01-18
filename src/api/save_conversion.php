<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

$inputValue = $data['input_value'] ?? null;
$fromUnit = $data['from_unit'] ?? null;
$toUnit = $data['to_unit'] ?? null;
$result = $data['result'] ?? null;
$conversionType = $data['conversion_type'] ?? null;

if (!$inputValue || !$fromUnit || !$toUnit || !$result || !$conversionType) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    // Insert new record
    $stmt = $pdo->prepare("
        INSERT INTO conversion_history (input_value, from_unit, to_unit, result, conversion_type, created_at)
        VALUES (:input_value, :from_unit, :to_unit, :result, :conversion_type, NOW())
    ");
    
    $stmt->execute([
        ':input_value' => $inputValue,
        ':from_unit' => $fromUnit,
        ':to_unit' => $toUnit,
        ':result' => $result,
        ':conversion_type' => $conversionType
    ]);

    // Delete older records, keeping only the latest 50
    $deleteStmt = $pdo->prepare("
        DELETE FROM conversion_history 
        WHERE id NOT IN (
            SELECT id FROM (
                SELECT id FROM conversion_history 
                ORDER BY created_at DESC 
                LIMIT 50
            ) AS recent
        )
    ");
    $deleteStmt->execute();

    echo json_encode(['success' => true, 'message' => 'Conversion saved successfully']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
