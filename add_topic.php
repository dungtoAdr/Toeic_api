<?php
require 'db.php';
header('Content-Type: application/json');

// Kiểm tra phương thức
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Chỉ chấp nhận phương thức POST.'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->name) || empty(trim($data->name))) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu hoặc trống tên chủ đề.'
    ]);
    exit;
}

$name = trim($data->name);

try {
    $stmt = $pdo->prepare("INSERT INTO topics (name) VALUES (:name)");
    $stmt->execute(['name' => $name]);

    echo json_encode([
        'success' => true,
        'message' => 'Thêm chủ đề thành công.',
        'id' => $pdo->lastInsertId()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi thêm chủ đề: ' . $e->getMessage()
    ]);
}
?>
