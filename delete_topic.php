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

// Nhận dữ liệu từ body (POST)
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu ID chủ đề cần xóa.'
    ]);
    exit;
}

$id = $data->id;

try {
    $stmt = $pdo->prepare("DELETE FROM topics WHERE id = :id");
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Xóa chủ đề thành công.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy chủ đề để xóa.'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi xóa chủ đề: ' . $e->getMessage()
    ]);
}
?>
