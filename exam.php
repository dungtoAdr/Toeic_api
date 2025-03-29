<?php
// Set the Content-Type header for API response to JSON
header('Content-Type: application/json');

try {
    // Connect to the MySQL database
    $pdo = new PDO('mysql:host=localhost;dbname=toeic', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Define the parts of the TOEIC test
    $toeic_parts = [
        'Part1' => 6,
        'Part2' => 25,
        'Part3' => 39,
        'Part4' => 30,
        'Part5' => 30,
        'Part6' => 16,
        'Part7' => 54
    ];
    
    // Prepare the response structure
    $response = [
        'success' => true,
        'message' => 'Data fetched successfully.',
        'result' => []
    ];

    // Loop through each part and fetch the questions for the part
    foreach ($toeic_parts as $part => $expected_count) {
        // Prepare the SQL query to fetch questions for the current part
        $sql = "SELECT q.id, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, q.audio_path, q.paragraph_path, q.image_path
                FROM questions q
                JOIN categories c ON q.category_id = c.id
                WHERE c.name = :part";

        // Prepare and execute the query
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['part' => $part]);

        // Fetch the result
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare data for the current part
        $partData = [
            'part' => $part,
            'expected_count' => $expected_count, // Expected number of questions for the part
            'actual_count' => count($questions), // Actual number of questions retrieved
            'questions' => $questions // The list of questions
        ];

        // Add the part data to the response result
        $response['result'][] = $partData;
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
