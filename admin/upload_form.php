<!DOCTYPE html>
<html>
<head>
    <title>Import Excel vào CSDL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">📥 Nhập câu hỏi từ Excel</h4>
        </div>
        <div class="card-body">
            <form action="import_excel.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="excel_file" class="form-label">Chọn file Excel (.xlsx)</label>
                    <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx" required>
                </div>
                <button type="submit" name="import" class="btn btn-success">
                    <i class="bi bi-upload"></i> Nhập dữ liệu
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Optional Bootstrap Icons (if you want the upload icon) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</body>
</html>
