<?php
// Include file kết nối cơ sở dữ liệu
require 'db.php';

header('Content-Type: application/json');

try {
    // Thực hiện truy vấn lấy dữ liệu từ bảng topics
    $stmt = $pdo->query("SELECT * FROM topics");

    // Lấy tất cả các kết quả dưới dạng mảng
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kiểm tra nếu có dữ liệu
    if ($topics) {
        echo json_encode([
            'success' => true,  // Thành công
            'message' => 'Lấy danh sách chủ đề thành công.',  // Thông điệp thành công
            'data' => $topics  // Dữ liệu chủ đề
        ]);
    } else {
        // Nếu không có dữ liệu
        echo json_encode([
            'success' => false,
            'message' => 'Không có chủ đề nào trong cơ sở dữ liệu.'
        ]);
    }
} catch (PDOException $e) {
    // Xử lý lỗi nếu có
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,  // Thất bại
        'error' => 'Lỗi khi lấy danh sách chủ đề: ' . $e->getMessage()  // Thông báo lỗi
    ]);
}
?>
