<?php
// Include file kết nối cơ sở dữ liệu
require 'db.php';

header('Content-Type: application/json');

try {
    // Thực hiện truy vấn: lấy dữ liệu và sắp xếp theo id tăng dần
    $stmt = $pdo->query("SELECT * FROM topics ORDER BY id ASC");

    // Lấy tất cả các kết quả dưới dạng mảng
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kiểm tra nếu có dữ liệu
    if ($topics) {
        echo json_encode([
            'success' => true,
            'message' => 'Lấy danh sách chủ đề thành công.',
            'data' => $topics
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không có chủ đề nào trong cơ sở dữ liệu.'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi lấy danh sách chủ đề: ' . $e->getMessage()
    ]);
}
?>
