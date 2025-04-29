<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$conn = new mysqli("localhost", "root", "", "toeic");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if (isset($_POST['import'])) {
    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == 0) {
        $fileTmpPath = $_FILES['excel_file']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($fileTmpPath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            for ($i = 1; $i < count($data); $i++) {
                list(
                    $question_text, $option_a, $option_b, $option_c, $option_d,
                    $correct_option, $category_id, $image_path, $audio_path, $paragraph_path
                ) = $data[$i];

                $stmt = $conn->prepare("INSERT INTO questions (
                    question_text, option_a, option_b, option_c, option_d,
                    correct_option, category_id, image_path, audio_path, paragraph_path
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                if (!$stmt) {
                    die("Lỗi prepare: " . $conn->error);
                }

                $stmt->bind_param("ssssssisss",
                    $question_text, $option_a, $option_b, $option_c, $option_d,
                    $correct_option, $category_id, $image_path, $audio_path, $paragraph_path
                );

                $stmt->execute();
            }

            echo "✔️ Nhập dữ liệu thành công!";
        } catch (Exception $e) {
            echo "❌ Lỗi khi xử lý file: " . $e->getMessage();
        }
    } else {
        echo "❌ Vui lòng chọn file hợp lệ!";
    }
}
?>
