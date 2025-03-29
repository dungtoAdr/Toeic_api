<?php
// Kết nối cơ sở dữ liệu
require 'db.php';

header('Content-Type: application/json');

try {
    // Truy vấn danh sách exams
    $stmt = $pdo->query("SELECT * FROM exams");
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trả về JSON
    echo json_encode([
        'success' => true,
        'message' => 'Lấy danh sách kỳ thi thành công.',
        'data' => $exams
    ], JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi lấy danh sách kỳ thi: ' . $e->getMessage()
    ]);
}
?>
