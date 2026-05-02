<?php
include('include/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    session_start();
    $_SESSION['toast_message'] = "Phương thức không hợp lệ";
    header("Location: index.php?tab=products");
    exit;
}

$drink_id    = $_POST['drink_id'] ?? '';
$drink_name  = trim($_POST['drink_name'] ?? '');
$drink_cost  = $_POST['drink_cost'] ?? '';
$drink_price = $_POST['drink_price'] ?? '';

if (!$drink_id || !$drink_name || !$drink_cost || !$drink_price) {
    session_start();
    $_SESSION['toast_message'] = "Thiếu dữ liệu";
    header("Location: index.php?tab=products");
    exit;
}

$drink_img = null;
if (isset($_FILES['drink_img']) && $_FILES['drink_img']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/img/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $ext = pathinfo($_FILES['drink_img']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('drink_', true) . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['drink_img']['tmp_name'], $targetPath)) {
        $drink_img = 'img/' . $fileName;
    } else {
        session_start();
        $_SESSION['toast_message'] = "Không thể upload ảnh";
        header("Location: index.php?tab=products");
        exit;
    }
}

if (!$drink_img) {
    $stmtOld = $conn->prepare("SELECT drink_img FROM soft_drink WHERE drink_id = :id LIMIT 1");
    $stmtOld->execute([':id' => $drink_id]);
    $old = $stmtOld->fetch(PDO::FETCH_ASSOC);
    $drink_img = $old ? $old['drink_img'] : '';
}

try {
    $stmt = $conn->prepare(
        "UPDATE soft_drink 
         SET drink_name=:name, drink_cost=:cost, drink_price=:price, drink_img=:img 
         WHERE drink_id=:id"
    );
    $stmt->execute([
        ':name'  => $drink_name,
        ':cost'  => $drink_cost,
        ':price' => $drink_price,
        ':img'   => $drink_img,
        ':id'    => $drink_id
    ]);

    session_start();
    $_SESSION['toast_message'] = "Cập nhật sản phẩm thành công!";
    header("Location: index.php?tab=products");
    exit();

} catch (PDOException $e) {
    session_start();
    $_SESSION['toast_message'] = 'Lỗi DB: ' . $e->getMessage();
    header("Location: index.php?tab=products");
    exit;
}
