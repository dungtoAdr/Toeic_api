<?php
// Include file kết nối cơ sở dữ liệu
require 'db.php';

// Lấy dữ liệu từ yêu cầu PUT
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['word']) || !isset($data['meaning'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Thiếu thông tin']);
    exit;
}

$id = $data['id'];
$word = $data['word'];
$meaning = $data['meaning'];
$pronunciation = $data['pronunciation'];
$audio_path = $data['audio_path'];

try {
    // Truy vấn cập nhật từ vựng
    $stmt = $pdo->prepare("UPDATE vocabulary SET word = :word, meaning = :meaning, pronunciation =:pronunciation, audio_path=:audio_path  WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':word', $word, PDO::PARAM_STR);
    $stmt->bindParam(':meaning', $meaning, PDO::PARAM_STR);
    $stmt->bindParam(':pronunciation', $pronunciation, PDO::PARAM_STR);
    $stmt->bindParam(':audio_path', $audio_path, PDO::PARAM_STR);
    $stmt->execute();

    // Trả về dữ liệu với thông báo thành công
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Sửa từ vựng thành công.'
    ]);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi sửa từ vựng: ' . $e->getMessage()
    ]);
}
?>
