<?php
header('Content-Type: application/json');

$host = 'localhost';
$db   = 'scannerex';
$user = 'root';
$pass = 'bujji123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit;
}

// Get data from either POST (JSON) or GET (URL parameters)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_GET;
}

// Validate required fields
$required = ['name', 'phone', 'company', 'email', 'address'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
        exit;
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO scans (name, phone, company, email, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['name'], 
        $data['phone'], 
        $data['company'], 
        $data['email'], 
        $data['address']
    ]);
    
    echo json_encode(['success' => true]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>