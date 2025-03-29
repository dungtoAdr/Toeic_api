<?php
// Kết nối cơ sở dữ liệu
require 'db.php';

header('Content-Type: application/json');

try {
    // Kiểm tra nếu có tham số session_id được truyền vào
    if (!isset($_GET['session_id']) || empty($_GET['session_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng cung cấp session_id hợp lệ.'
        ]);
        exit;
    }

    $session_id = $_GET['session_id'];

    // Truy vấn thông tin của phiên luyện tập
    $stmt_session = $pdo->prepare("SELECT * FROM practice_sessions WHERE id = :session_id");
    $stmt_session->execute(['session_id' => $session_id]);
    $session = $stmt_session->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy phiên luyện tập với ID đã cho.'
        ]);
        exit;
    }

    // Truy vấn danh sách câu hỏi của phiên luyện tập đó
    $stmt_questions = $pdo->prepare("
        SELECT q.*, pa.user_answer
        FROM practice_answers pa
        JOIN questions q ON pa.question_id = q.id
        WHERE pa.session_id = :session_id
    ");
    $stmt_questions->execute(['session_id' => $session_id]);
    $questions = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);

    // Thêm danh sách câu hỏi vào session
    $session['questions'] = $questions;

    echo json_encode([
        'success' => true,
        'message' => 'Lấy danh sách câu hỏi thành công.',
        'data' => $session
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi lấy danh sách câu hỏi: ' . $e->getMessage()
    ]);
}
