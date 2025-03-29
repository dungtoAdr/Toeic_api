<?php
require 'db.php';
header('Content-Type: application/json');

try {
    // Nhận dữ liệu JSON từ client
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user_id'], $data['part'], $data['questions'])) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
        exit;
    }

    $user_id = $data['user_id'];
    $part = $data['part'];
    $questions = $data['questions'];
    $total_questions = count($questions);
    $correct_answers = 0;

    // Tính số câu trả lời đúng
    foreach ($questions as $q) {
        if ($q['is_correct']) {
            $correct_answers++;
        }
    }

    // Lưu thông tin phiên luyện tập
    $stmt = $pdo->prepare("INSERT INTO practice_sessions (user_id, part, total_questions, correct_answers, started_at, completed_at) 
                           VALUES (:user_id, :part, :total_questions, :correct_answers, NOW(), NOW())");
    $stmt->execute([
        'user_id' => $user_id,
        'part' => $part,
        'total_questions' => $total_questions,
        'correct_answers' => $correct_answers
    ]);

    $session_id = $pdo->lastInsertId();

    // Lưu câu trả lời
    $stmt = $pdo->prepare("INSERT INTO practice_answers (session_id, question_id, user_answer, is_correct) 
                           VALUES (:session_id, :question_id, :user_answer, :is_correct)");

    foreach ($questions as $q) {
        $stmt->execute([
            'session_id' => $session_id,
            'question_id' => $q['question_id'],
            'user_answer' => $q['user_answer'],
            'is_correct' => $q['is_correct']
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Lưu kết quả thành công.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
