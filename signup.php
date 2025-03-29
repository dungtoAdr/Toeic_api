<?php
// Kết nối cơ sở dữ liệu
require 'db.php';

header('Content-Type: application/json');

try {
    // Kiểm tra dữ liệu đầu vào
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id']) || !isset($data['name']) || !isset($data['uid'])) {
        exit; // Không thông báo gì nếu thiếu dữ liệu
    }

    $id = $data['id'];
    $name = $data['name'];
    $uid = $data['uid'];

    // Kiểm tra xem UID đã tồn tại chưa
    $stmt_check_uid = $pdo->prepare("SELECT uid FROM user WHERE uid = :uid");
    $stmt_check_uid->execute(['uid' => $uid]);

    if ($stmt_check_uid->rowCount() > 0) {
        exit; // Nếu UID đã tồn tại, dừng mà không thông báo gì
    }

    // Thêm bản ghi mới nếu UID chưa tồn tại
    $stmt = $pdo->prepare("INSERT INTO user (id, name, uid) VALUES (:id, :name, :uid)");
    $stmt->execute([
        'id' => $id,
        'name' => $name,
        'uid' => $uid
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Đăng ký thành công!',
        'data' => [
            'id' => $id,
            'name' => $name,
            'uid' => $uid
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
}
?>
