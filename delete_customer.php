<?php
include('include/config.php');
session_start();

// Kiểm tra ID khách hàng
if (!isset($_GET['id'])) {
    $_SESSION['toast_message'] = "Thiếu ID khách hàng";
    http_response_code(400);
    echo "error";
    exit;
}

$customer_id = (int)$_GET['id'];

try {
    // Nếu cần xóa dữ liệu liên quan, thêm vào đây
    // VD: $conn->prepare("DELETE FROM some_table WHERE customer_id=:id")->execute([':id'=>$customer_id]);

    // Xóa khách hàng
    $stmt = $conn->prepare("DELETE FROM customer WHERE customer_id = :id");
    $stmt->execute([':id' => $customer_id]);

    $_SESSION['toast_message'] = "Xóa khách hàng #$customer_id thành công!";
    echo "success";
    exit;

} catch (PDOException $e) {
    $_SESSION['toast_message'] = "Lỗi DB: " . $e->getMessage();
    http_response_code(500);
    echo "error";
    exit;
}
