<?php
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Chỉ chấp nhận phương thức POST.'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || !isset($data->name)) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu ID hoặc tên chủ đề cần sửa.'
    ]);
    exit;
}

$id = $data->id;
$name = trim($data->name);

try {
    $stmt = $pdo->prepare("UPDATE topics SET name = :name WHERE id = :id");
    $stmt->execute(['name' => $name, 'id' => $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật chủ đề thành công.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy chủ đề hoặc không có thay đổi.'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi cập nhật chủ đề: ' . $e->getMessage()
    ]);
}
?>
