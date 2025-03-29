<?php
// Include file kết nối cơ sở dữ liệu
require 'db.php';

// Lấy topic_id từ yêu cầu GET
$topic_id = isset($_GET['topic_id']) ? intval($_GET['topic_id']) : null;

if ($topic_id === null) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Thiếu topic_id']);
    exit;
}

try {
    // Truy vấn danh sách từ vựng theo topic_id
    $stmt = $pdo->prepare("SELECT * FROM vocabulary WHERE topic_id = :topic_id");
    $stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
    $stmt->execute();
    $vocabularies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trả về dữ liệu với thông báo thành công
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Lấy danh sách từ vựng thành công.',
        'data' => $vocabularies
    ]);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi lấy danh sách từ vựng: ' . $e->getMessage()
    ]);
}
?>
