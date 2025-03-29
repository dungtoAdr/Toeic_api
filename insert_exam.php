<?php
// Kết nối CSDL
$host = "localhost";
$username = "root";
$password = "";
$database = "toeic";

$conn = new mysqli($host, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý khi người dùng gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $duration = intval($_POST['duration']);
    $selected_questions = $_POST['questions'] ?? [];

    // Thêm bài thi mới vào bảng exams
    $sql_exam = "INSERT INTO exams (title, description, duration, total_questions) 
                 VALUES ('$title', '$description', $duration, " . count($selected_questions) . ")";
    if ($conn->query($sql_exam)) {
        $exam_id = $conn->insert_id;

        // Thêm các câu hỏi vào bảng exam_questions
        if (!empty($selected_questions)) {
            $values = [];
            foreach ($selected_questions as $question_id) {
                $values[] = "($exam_id, " . intval($question_id) . ")";
            }

            $sql_exam_questions = "INSERT INTO exam_questions (exam_id, question_id) VALUES " . implode(',', $values);
            $conn->query($sql_exam_questions);
        }

        echo "Thêm bài thi thành công!";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

// Lấy danh sách câu hỏi từ bảng questions
$sql_questions = "SELECT * FROM questions";
$result = $conn->query($sql_questions);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm bài thi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">
    <h2 class="mb-4">Thêm Bài Thi Mới</h2>

    <form method="POST" action="">
        <!-- Thông tin bài thi -->
        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề bài thi:</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô tả:</label>
            <textarea id="description" name="description" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="duration" class="form-label">Thời gian (phút):</label>
            <input type="number" id="duration" name="duration" class="form-control" required>
        </div>

        <hr>

        <!-- Danh sách câu hỏi -->
        <h4>Chọn câu hỏi:</h4>

        <div class="row">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Câu hỏi: <?php echo htmlspecialchars($row['question_text']); ?></h5>
                            <ul>
                                <li></li>
                                <li>A: <?php echo htmlspecialchars($row['option_a']); ?></li>
                                <li>B: <?php echo htmlspecialchars($row['option_b']); ?></li>
                                <li>C: <?php echo htmlspecialchars($row['option_c']); ?></li>
                                <li>D: <?php echo htmlspecialchars($row['option_d']); ?></li>
                            </ul>
                            <label>
                                <input type="checkbox" name="questions[]" value="<?php echo $row['id']; ?>">
                                Chọn câu hỏi này
                            </label>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <button type="submit" class="btn btn-primary">Thêm bài thi</button>
    </form>

</body>

</html>

<?php
$conn->close();
?>
