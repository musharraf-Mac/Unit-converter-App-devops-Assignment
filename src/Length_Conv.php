<?php
/**
 * Unit Converter Backend
 * Handles conversion requests and stores records in database
 * Maintains maximum 50 records - deletes oldest when limit exceeded
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'unit_converter');
define('MAX_RECORDS', 50);

// Set headers for JSON response and CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die(json_encode([
            'success' => false,
            'error' => 'Database connection failed: ' . $conn->connect_error
        ]));
    }
    
    return $conn;
}

// Create table if not exists
function initializeDatabase($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS conversion_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        conversion_type VARCHAR(50) NOT NULL,
        input_value DECIMAL(20, 6) NOT NULL,
        from_unit VARCHAR(50) NOT NULL,
        to_unit VARCHAR(50) NOT NULL,
        result_value DECIMAL(20, 6) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($sql)) {
        return false;
    }
    return true;
}

// Maintain max 50 records - delete oldest if exceeded
function maintainRecordLimit($conn) {
    // Get current record count
    $countResult = $conn->query("SELECT COUNT(*) as total FROM conversion_history");
    $count = $countResult->fetch_assoc()['total'];
    
    // If we have 50 or more records, delete the oldest ones to make room
    if ($count >= MAX_RECORDS) {
        $deleteCount = $count - MAX_RECORDS + 1; // +1 to make room for new record
        $conn->query("DELETE FROM conversion_history 
                      ORDER BY created_at ASC 
                      LIMIT $deleteCount");
    }
}

// Save conversion record to database
function saveConversion($conn, $type, $inputValue, $fromUnit, $toUnit, $resultValue) {
    // Maintain record limit before inserting
    maintainRecordLimit($conn);
    
    $stmt = $conn->prepare("INSERT INTO conversion_history 
                           (conversion_type, input_value, from_unit, to_unit, result_value) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssd", $type, $inputValue, $fromUnit, $toUnit, $resultValue);
    
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    }
    
    $stmt->close();
    return false;
}

// Get conversion history
function getHistory($conn, $limit = 50) {
    $stmt = $conn->prepare("SELECT * FROM conversion_history 
                           ORDER BY created_at DESC 
                           LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    $stmt->close();
    return $history;
}

// Conversion functions
function convertLength($value, $from, $to) {
    // Base unit: meters
    $toMeters = [
        'mm' => 0.001,
        'cm' => 0.01,
        'm' => 1,
        'km' => 1000,
        'inch' => 0.0254,
        'foot' => 0.3048,
        'yard' => 0.9144,
        'mile' => 1609.344
    ];
    
    if (!isset($toMeters[$from]) || !isset($toMeters[$to])) {
        return null;
    }
    
    $meters = $value * $toMeters[$from];
    return $meters / $toMeters[$to];
}

function convertWeight($value, $from, $to) {
    // Base unit: grams
    $toGrams = [
        'mg' => 0.001,
        'g' => 1,
        'kg' => 1000,
        'oz' => 28.3495,
        'lb' => 453.592,
        'ton' => 1000000
    ];
    
    if (!isset($toGrams[$from]) || !isset($toGrams[$to])) {
        return null;
    }
    
    $grams = $value * $toGrams[$from];
    return $grams / $toGrams[$to];
}

function convertTemperature($value, $from, $to) {
    // Convert to Celsius first
    $celsius = $value;
    
    switch ($from) {
        case 'fahrenheit':
            $celsius = ($value - 32) * 5/9;
            break;
        case 'kelvin':
            $celsius = $value - 273.15;
            break;
        case 'celsius':
            $celsius = $value;
            break;
        default:
            return null;
    }
    
    // Convert from Celsius to target unit
    switch ($to) {
        case 'fahrenheit':
            return ($celsius * 9/5) + 32;
        case 'kelvin':
            return $celsius + 273.15;
        case 'celsius':
            return $celsius;
        default:
            return null;
    }
}

function performConversion($type, $value, $from, $to) {
    switch ($type) {
        case 'length':
            return convertLength($value, $from, $to);
        case 'weight':
            return convertWeight($value, $from, $to);
        case 'temperature':
            return convertTemperature($value, $from, $to);
        default:
            return null;
    }
}

// Main request handler
$conn = getConnection();
initializeDatabase($conn);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Handle conversion request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        // Try form data
        $input = [
            'type' => $_POST['type'] ?? null,
            'value' => $_POST['value'] ?? null,
            'fromUnit' => $_POST['fromUnit'] ?? null,
            'toUnit' => $_POST['toUnit'] ?? null
        ];
    }
    
    $type = $input['type'] ?? null;
    $value = $input['value'] ?? null;
    $fromUnit = $input['fromUnit'] ?? null;
    $toUnit = $input['toUnit'] ?? null;
    
    // Validate input
    if (!$type || $value === null || !$fromUnit || !$toUnit) {
        echo json_encode([
            'success' => false,
            'error' => 'Missing required fields: type, value, fromUnit, toUnit'
        ]);
        exit;
    }
    
    // Perform conversion
    $result = performConversion($type, floatval($value), $fromUnit, $toUnit);
    
    if ($result === null) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid conversion type or units'
        ]);
        exit;
    }
    
    // Save to database
    $saved = saveConversion($conn, $type, $value, $fromUnit, $toUnit, $result);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'type' => $type,
            'inputValue' => floatval($value),
            'fromUnit' => $fromUnit,
            'toUnit' => $toUnit,
            'result' => round($result, 6),
            'saved' => $saved
        ]
    ]);
    
} elseif ($method === 'GET') {
    // Return conversion history
    $history = getHistory($conn);
    
    echo json_encode([
        'success' => true,
        'data' => $history,
        'count' => count($history)
    ]);
    
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}

$conn->close();
?>
