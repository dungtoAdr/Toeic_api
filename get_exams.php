<?php
// Kết nối cơ sở dữ liệu
require 'db.php';

header('Content-Type: application/json');

try {
    // Truy vấn danh sách exams
    $stmt = $pdo->query("SELECT * FROM exams");
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kiểm tra nếu có dữ liệu
    if ($exams) {
        // Lặp qua từng exam để lấy danh sách câu hỏi
        foreach ($exams as &$exam) {
            $exam_id = $exam['id'];

            // Truy vấn lấy tất cả câu hỏi của exam
            $stmt_questions = $pdo->prepare("
                SELECT * 
                FROM exam_questions eq
                JOIN questions q ON eq.question_id = q.id
                WHERE eq.exam_id = :exam_id
            ");
            $stmt_questions->execute(['exam_id' => $exam_id]);
            $exam['questions'] = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Lấy danh sách kỳ thi thành công.',
            'data' => $exams
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không có kỳ thi nào trong cơ sở dữ liệu.'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi lấy danh sách kỳ thi: ' . $e->getMessage()
    ]);
}
?>
