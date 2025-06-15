<?php
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
  echo json_encode(['error' => 'DB connection failed']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $pdo->prepare("INSERT INTO registrations (name, phone, company, email, address) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([
  $data['name'], $data['phone'], $data['company'], $data['email'], $data['address']
]);

echo json_encode(['success' => true]);
?>
