<?php
// Include file kết nối cơ sở dữ liệu
require 'db.php';

// Lấy topic_id và id từ yêu cầu DELETE
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Thiếu thông tin']);
    exit;
}

$id = $data['id'];

try {
    // Truy vấn xóa từ vựng
    $stmt = $pdo->prepare("DELETE FROM vocabulary WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Trả về dữ liệu với thông báo thành công
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Xóa từ vựng thành công.'
    ]);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi xóa từ vựng: ' . $e->getMessage()
    ]);
}
?>
