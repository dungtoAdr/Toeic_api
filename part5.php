<?php
header('Content-Type: application/json');

try {
    require 'db.php';


    // Prepare the SQL query to fetch 6 questions for Part1
    $sql = "SELECT q.id, q.question_text, q.category_id, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, q.audio_path, q.image_path
            FROM questions q
            JOIN categories c ON q.category_id = c.id
            WHERE c.name = 'Part5'
            ORDER BY RAND()
            LIMIT 10";  // Get only 6 questions for Part1

    // Execute the query
    $stmt = $pdo->query($sql);

    // Fetch the questions
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare the response structure
    $response = [
        'success' => true,
        'message' => 'Data fetched successfully.',
        'questions' => $questions
    ];

} catch (Exception $e) {
    // Handle error and return error response
    $response = [
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage(),
        'questions' => []
    ];
}

// Return JSON response
echo json_encode($response);
?>
