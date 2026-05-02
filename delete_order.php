<?php
include('include/config.php');
session_start();

// Kiểm tra ID đơn hàng
if (!isset($_GET['id'])) {
    $_SESSION['toast_message'] = "Thiếu ID đơn hàng";
    header("Location: dash_order.php");
    exit;
}

$order_id = (int)$_GET['id'];

try {
    // Xóa chi tiết order trước
    $stmt = $conn->prepare("DELETE FROM order_details WHERE order_id = :id");
    $stmt->execute([':id' => $order_id]);

    // Xóa order
    $stmt2 = $conn->prepare("DELETE FROM orders WHERE order_id = :id");
    $stmt2->execute([':id' => $order_id]);

    // Lưu thông báo vào session
    $_SESSION['toast_message'] = "Xóa đơn hàng #$order_id thành công!";
    echo "success";
    exit;

} catch (PDOException $e) {
    // Lưu thông báo lỗi vào session
    $_SESSION['toast_message'] = "Lỗi DB: " . $e->getMessage();

    // Trả về error để JS có thể reload
    http_response_code(500);
    echo "error";
    exit;
}
