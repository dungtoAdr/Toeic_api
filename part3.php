<?php
// Cấu hình tiêu đề cho API
header('Content-Type: application/json');

try {
    require 'db.php';

    // Bước 1: Lấy tất cả các audio_path từ category 'Part3'
    $sql = "SELECT DISTINCT q.audio_path 
            FROM questions q
            JOIN categories c ON q.category_id = c.id
            WHERE c.name = 'Part3' 
            AND q.audio_path IS NOT NULL";

    $stmt = $pdo->query($sql);
    $audioPaths = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Kiểm tra nếu có audio_path
    if ($audioPaths) {
        // Random chọn 3 audio_path từ danh sách đã lấy (tránh trùng lặp)
        shuffle($audioPaths);
        $randomAudios = array_slice($audioPaths, 0, 3);

        $response = [
            'success' => true,
            'message' => 'Dữ liệu lấy thành công.',
            'result' => []
        ];

        // Bước 2: Lấy tất cả câu hỏi cho mỗi audio_path được chọn
        foreach ($randomAudios as $randomAudio) {
            $sql2 = "SELECT q.* 
                     FROM questions q
                     JOIN categories c ON q.category_id = c.id
                     WHERE c.name = 'Part3' 
                     AND q.audio_path = :audio_path";

            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute(['audio_path' => $randomAudio]);

            // Lấy và chuẩn bị dữ liệu câu hỏi
            $questions = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            $audioData = [
                'audio_path' => $randomAudio,
                'questions' => []
            ];

            if ($questions) {
                foreach ($questions as $question) {
                    $audioData['questions'][] = [
                        'id' => $question['id'],
                        'question_text' => $question['question_text'],
                        'option_a' => $question['option_a'],
                        'option_b' => $question['option_b'],
                        'option_c' => $question['option_c'],
                        'option_d' => $question['option_d'],
                        'correct_option' => $question['correct_option'],
                        'category_id' => $question['category_id'],
                        'image_path' => $question['image_path']
                    ];
                }
            }

            $response['result'][] = $audioData;
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Không tìm thấy audio nào cho category "Part3".',
            'result' => []
        ];
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
        'result' => []
    ];
}

// Trả về JSON
echo json_encode($response);
?>
