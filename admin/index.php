<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "toeic";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_topic"])) {
        $name = $_POST["topic_name"];
        $stmt = $conn->prepare("INSERT INTO topics (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST["add_word"])) {
        $word = $_POST["word"];
        $pronunciation = $_POST["pronunciation"];
        $meaning = $_POST["meaning"];
        $audio_path = $_POST["audio_path"] ?? NULL;
        $topic_id = $_POST["topic_id"];
        $stmt = $conn->prepare("INSERT INTO vocabulary (word, pronunciation, meaning, audio_path, topic_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $word, $pronunciation, $meaning, $audio_path, $topic_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST["submit"]) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($fileTmpPath);
        $sheet = $spreadsheet->getActiveSheet();
        $stmt = $conn->prepare("INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option, category_id, image_path, audio_path, paragraph_path, transcript) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }
            if (count($data) >= 10) {
                $stmt->bind_param("sssssssssss", $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10]);
                $stmt->execute();
            }
        }
        $stmt->close();
    } elseif (isset($_POST["edit_topic"])) {
        $topic_id = $_POST["topic_id"];
        $topic_name = $_POST["topic_name"];
        $stmt = $conn->prepare("UPDATE topics SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $topic_name, $topic_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST["edit_word"])) {
        $word_id = $_POST["word_id"];
        $word = $_POST["word"];
        $pronunciation = $_POST["pronunciation"];
        $meaning = $_POST["meaning"];
        $audio_path = $_POST["audio_path"] ?? NULL;
        $topic_id = $_POST["topic_id"];
        $stmt = $conn->prepare("UPDATE vocabulary SET word = ?, pronunciation = ?, meaning = ?, audio_path = ?, topic_id = ? WHERE id = ?");
        $stmt->bind_param("ssssii", $word, $pronunciation, $meaning, $audio_path, $topic_id, $word_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST["delete_topic"])) {
        $topic_id = $_POST["topic_id"];
        $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
        $stmt->bind_param("i", $topic_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST["delete_word"])) {
        $word_id = $_POST["word_id"];
        $stmt = $conn->prepare("DELETE FROM vocabulary WHERE id = ?");
        $stmt->bind_param("i", $word_id);
        $stmt->execute();
        $stmt->close();
    }
}

$topics = $conn->query("SELECT id, name FROM topics");
$vocabulary = $conn->query("SELECT v.id, v.word, v.pronunciation, v.meaning, v.audio_path, t.name as topic_name, v.topic_id FROM vocabulary v JOIN topics t ON v.topic_id = t.id");

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý TOEIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="mb-4">Quản lý DUNGTOEIC</h2>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="topic-tab" data-bs-toggle="tab" data-bs-target="#topic" type="button" role="tab" aria-controls="topic" aria-selected="true">Chủ Đề</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="word-tab" data-bs-toggle="tab" data-bs-target="#word" type="button" role="tab" aria-controls="word" aria-selected="false">Từ Vựng</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="question-tab" data-bs-toggle="tab" data-bs-target="#question" type="button" role="tab" aria-controls="question" aria-selected="false">Câu Hỏi</button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="topic" role="tabpanel" aria-labelledby="topic-tab">
            <h3 class="mt-4">Thêm Chủ Đề</h3>
            <form method="post" class="card p-4 shadow">
                <label class="form-label">Chủ đề:</label>
                <input type="text" name="topic_name" class="form-control" required>
                <button type="submit" name="add_topic" class="btn btn-primary mt-2">Thêm chủ đề</button>
            </form>
            <h4 class="mt-4">Danh Sách Chủ Đề</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Chủ Đề</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $topics->data_seek(0); while ($row = $topics->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editTopicModal<?= $row['id'] ?>">Sửa</button>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteTopicModal<?= $row['id'] ?>">Xóa</button>
                                <div class="modal fade" id="editTopicModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editTopicModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editTopicModalLabel">Sửa Chủ Đề</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    <input type="hidden" name="topic_id" value="<?= $row['id'] ?>">
                                                    <label class="form-label">Tên chủ đề:</label>
                                                    <input type="text" name="topic_name" class="form-control" value="<?= $row['name'] ?>" required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit" name="edit_topic" class="btn btn-primary">Lưu thay đổi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="deleteTopicModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="deleteTopicModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteTopicModalLabel">Xóa Chủ Đề</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    <p>Bạn có chắc chắn muốn xóa chủ đề "<?= $row['name'] ?>" không?</p>
                                                    <input type="hidden" name="topic_id" value="<?= $row['id'] ?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit" name="delete_topic" class="btn btn-danger">Xóa</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="word" role="tabpanel" aria-labelledby="word-tab">
            <h3 class="mt-4">Thêm Từ Vựng</h3>
            <form method="post" class="card p-4 shadow">
                <label class="form-label">Từ vựng:</label>
                <input type="text" name="word" class="form-control" required>
                <label class="form-label">Phiên âm:</label>
                <input type="text" name="pronunciation" class="form-control" required>
                <label class="form-label">Ý nghĩa:</label>
                <textarea name="meaning" class="form-control" required></textarea>
                <label class="form-label">Đường dẫn âm thanh:</label>
                <input type="text" name="audio_path" class="form-control">
                <label class="form-label">Chủ đề:</label>
                <select name="topic_id" class="form-select" required>
                    <option value="">Chọn chủ đề</option>
                    <?php $topics->data_seek(0); while ($row = $topics->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="add_word" class="btn btn-primary mt-2">Thêm từ vựng</button>
            </form>
            <h4 class="mt-4">Danh Sách Từ Vựng</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Từ Vựng</th>
                        <th>Phiên Âm</th>
                        <th>Ý Nghĩa</th>
                        <th>Âm Thanh</th>
                        <th>Chủ Đề</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $vocabulary->data_seek(0); while ($row = $vocabulary->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['word'] ?></td>
                            <td><?= $row['pronunciation'] ?></td>
                            <td><?= $row['meaning'] ?></td>
                            <td><?= $row['audio_path'] ?></td>
                            <td><?= $row['topic_name'] ?></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editWordModal<?= $row['id'] ?>">Sửa</button>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteWordModal<?= $row['id'] ?>">Xóa</button>
                                <div class="modal fade" id="editWordModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editWordModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editWordModalLabel">Sửa Từ Vựng</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    <input type="hidden" name="word_id" value="<?= $row['id'] ?>">
                                                    <label class="form-label">Từ vựng:</label>
                                                    <input type="text" name="word" class="form-control" value="<?= $row['word'] ?>" required>
                                                    <label class="form-label">Phiên âm:</label>
                                                    <input type="text" name="pronunciation" class="form-control" value="<?= $row['pronunciation'] ?>" required>
                                                    <label class="form-label">Ý nghĩa:</label>
                                                    <textarea name="meaning" class="form-control" required><?= $row['meaning'] ?></textarea>
                                                    <label class="form-label">Đường dẫn âm thanh:</label>
                                                    <input type="text" name="audio_path" class="form-control" value="<?= $row['audio_path'] ?>">
                                                    <label class="form-label">Chủ đề:</label>
                                                    <select name="topic_id" class="form-select" required>
                                                        <option value="">Chọn chủ đề</option>
                                                        <?php $topics->data_seek(0); while ($topic = $topics->fetch_assoc()): ?>
                                                            <option value="<?= $topic['id'] ?>" <?= $row['topic_id'] == $topic['id'] ? 'selected' : '' ?>><?= $topic['name'] ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit" name="edit_word" class="btn btn-primary">Lưu thay đổi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="deleteWordModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="deleteWordModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteWordModalLabel">Xóa Từ Vựng</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    <p>Bạn có chắc chắn muốn xóa từ vựng "<?= $row['word'] ?>" không?</p>
                                                    <input type="hidden" name="word_id" value="<?= $row['id'] ?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit" name="delete_word" class="btn btn-danger">Xóa</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="question" role="tabpanel" aria-labelledby="question-tab">
            <h3 class="mt-4">Nhập Câu Hỏi (Excel)</h3>
            <form method="post" enctype="multipart/form-data" class="card p-4 shadow">
                <label class="form-label">Tải lên file Excel:</label>
                <input type="file" name="file" class="form-control" required>
                <button type="submit" name="submit" class="btn btn-primary mt-2">Nhập câu hỏi</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>