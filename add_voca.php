<?php
// Include file kết nối cơ sở dữ liệu
require 'db.php';

// Lấy dữ liệu từ yêu cầu POST
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['topic_id']) || !isset($data['word']) || !isset($data['meaning'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Thiếu thông tin']);
    exit;
}

$topic_id = $data['topic_id'];
$word = $data['word'];
$pronunciation = $data['pronunciation'];
$meaning = $data['meaning'];
$audio_path = $data['audio_path'];

try {
    // Truy vấn thêm từ vựng mới
    $stmt = $pdo->prepare("INSERT INTO vocabulary (topic_id, word, meaning, audio_path, pronunciation) VALUES (:topic_id, :word, :meaning, :audio_path, :pronunciation)");
    $stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
    $stmt->bindParam(':word', $word, PDO::PARAM_STR);
    $stmt->bindParam(':meaning', $meaning, PDO::PARAM_STR);
    $stmt->bindParam(':pronunciation', $pronunciation, PDO::PARAM_STR);
    $stmt->bindParam(':audio_path', $audio_path, PDO::PARAM_STR);
    $stmt->execute();

    // Trả về dữ liệu với thông báo thành công
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Thêm từ vựng thành công.'
    ]);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi thêm từ vựng: ' . $e->getMessage()
    ]);
}
?>
