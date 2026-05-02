<?php
include('include/config.php');
session_start();

if (!isset($_GET['id'])) {
    $_SESSION['toast_message'] = "Thiếu ID sản phẩm";
    header("Location: index.php?tab=products");
    exit;
}

$id = (int)$_GET['id'];

// Lấy ảnh cũ để xóa file
$stmt = $conn->prepare("SELECT drink_img FROM soft_drink WHERE drink_id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['toast_message'] = "Sản phẩm không tồn tại";
    header("Location: index.php?tab=products");
    exit;
}

try {
    // Xóa sản phẩm trong database trước
    $stmt = $conn->prepare("DELETE FROM soft_drink WHERE drink_id = :id");
    $stmt->execute([':id' => $id]);

    // Nếu xóa DB thành công thì mới xóa file ảnh
    if (!empty($product['drink_img']) && file_exists(__DIR__ . '/' . $product['drink_img'])) {
        unlink(__DIR__ . '/' . $product['drink_img']);
    }

    $_SESSION['toast_message'] = "Xóa sản phẩm thành công!";
    header("Location: index.php?tab=products");
    exit;

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        $_SESSION['toast_message'] = "Không thể xóa: Món này đang nằm trong hóa đơn cũ!";
    } else {
        $_SESSION['toast_message'] = "Lỗi khi xóa sản phẩm: " . $e->getMessage();
    }
    header("Location: index.php?tab=products");
    exit;
}
