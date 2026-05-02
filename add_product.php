<?php
include('include/config.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

// Lấy dữ liệu
$drink_name  = trim($_POST['drink_name'] ?? '');
$drink_cost  = $_POST['drink_cost'] ?? '';
$drink_price = $_POST['drink_price'] ?? '';

// Kiểm tra dữ liệu
if (!$drink_name || !$drink_cost || !$drink_price) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
    exit;
}

// Xử lý upload ảnh
$drink_img = '';
if (isset($_FILES['drink_img']) && $_FILES['drink_img']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/img/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $ext = pathinfo($_FILES['drink_img']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('drink_', true) . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['drink_img']['tmp_name'], $targetPath)) {
        $drink_img = 'img/' . $fileName; // Lưu đường dẫn tương đối
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể upload ảnh']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Vui lòng chọn ảnh sản phẩm']);
    exit;
}

try {
    $stmt = $conn->prepare(
        "INSERT INTO soft_drink (drink_name, drink_cost, drink_price, drink_img) 
         VALUES (:name, :cost, :price, :img)"
    );
    $stmt->execute([
        ':name'  => $drink_name,
        ':cost'  => $drink_cost,
        ':price' => $drink_price,
        ':img'   => $drink_img
    ]);

    session_start();
    $_SESSION['toast_message'] = "Thêm sản phẩm thành công!";
    header("Location: index.php?tab=products");
    exit();

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]);
    exit;
}
