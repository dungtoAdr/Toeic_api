<?php
header('Content-Type: application/json');

try {
    require 'db.php'; // Kết nối CSDL

    // Kiểm tra xem 'id' đã được truyền vào request chưa
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('Missing or invalid question ID.');
    }

    $questionId = $_GET['id']; // Lấy giá trị ID từ request

    // Chuẩn bị truy vấn SQL để lấy thông tin câu hỏi theo ID
    $sql = "SELECT *
            FROM questions q
            WHERE q.id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $questionId, PDO::PARAM_INT);
    $stmt->execute();

    $question = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra nếu không tìm thấy câu hỏi
    if (!$question) {
        throw new Exception('No question found with the given ID.');
    }

    // Trả về phản hồi JSON thành công
    $response = [
        'success' => true,
        'message' => 'Question fetched successfully.',
        'question' => $question
    ];
} catch (Exception $e) {
    // Xử lý lỗi và trả về phản hồi lỗi
    $response = [
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage(),
        'question' => null
    ];
}

// Xuất phản hồi dưới dạng JSON
echo json_encode($response);
?>
