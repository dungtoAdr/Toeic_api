<?php
// Set the Content-Type header for API response to JSON
header('Content-Type: application/json');

try {
    require 'db.php';

    // Step 1: Fetch 3 unique random paragraph_path for category 'Part'
    $sql = "SELECT DISTINCT q.paragraph_path 
            FROM questions q
            JOIN categories c ON q.category_id = c.id
            WHERE c.name = 'Part7' 
            AND q.paragraph_path IS NOT NULL
            ORDER BY RAND()
            LIMIT 3";

    // Execute the query
    $stmt = $pdo->query($sql);
    $paragraphPaths = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Check if there are any paragraph_paths found
    if ($paragraphPaths) {
        $response = [
            'success' => true,
            'message' => 'Data fetched successfully.',
            'result' => []
        ];

        // Step 2: Fetch all questions for each paragraph_path
        $sql2 = "SELECT q.* 
                 FROM questions q
                 JOIN categories c ON q.category_id = c.id
                 WHERE c.name = 'Part7' 
                 AND q.paragraph_path = :paragraph_path";

        // Prepare the second query
        $stmt2 = $pdo->prepare($sql2);

        foreach ($paragraphPaths as $paragraphPath) {
            // Execute the query for each paragraph_path
            $stmt2->execute(['paragraph_path' => $paragraphPath]);

            // Fetch all questions related to the current paragraph_path
            $questions = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            // Prepare the data for the current paragraph
            $paragraphData = [
                'paragraph_path' => $paragraphPath,
                'questions' => []
            ];

            // Add questions to the paragraph data if found
            if ($questions) {
                foreach ($questions as $question) {
                    $paragraphData['questions'][] = [
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

            // Add the paragraph data to the result
            $response['result'][] = $paragraphData;
        }
    } else {
        // No paragraph paths found for 'Part7'
        $response = [
            'success' => false,
            'message' => 'No paragraphs found for category "Part7".',
            'result' => []
        ];
    }
} catch (Exception $e) {
    // Catch any exceptions and return an error response
    $response = [
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage(),
        'result' => []
    ];
}

// Return the JSON response
echo json_encode($response);
?>
