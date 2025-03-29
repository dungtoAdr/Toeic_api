<?php
// Kết nối cơ sở dữ liệu
require 'db.php';

header('Content-Type: application/json');

try {
    // Truy vấn danh sách các phiên luyện tập
    $stmt = $pdo->query("SELECT * FROM practice_sessions");
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kiểm tra nếu có dữ liệu
    if ($sessions) {
        foreach ($sessions as &$session) {
            $session_id = $session['id'];

            // Truy vấn tất cả câu hỏi của từng phiên luyện tập
            $stmt_questions = $pdo->prepare("
                SELECT pa.*
                FROM practice_answers pa
                JOIN questions q ON pa.question_id = q.id
                WHERE pa.session_id = :session_id
            ");
            $stmt_questions->execute(['session_id' => $session_id]);
            $session['questions'] = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Lấy danh sách phiên luyện tập thành công.',
            'data' => $sessions
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không có phiên luyện tập nào trong cơ sở dữ liệu.'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi lấy danh sách phiên luyện tập: ' . $e->getMessage()
    ]);
}
