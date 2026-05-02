<?php
include('include/config.php');
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

$customer_name  = trim($_POST['customer_name'] ?? '');
$customer_phone = trim($_POST['customer_phone'] ?? '');

if (!$customer_name) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
    exit;
}

try {
    $stmt = $conn->prepare(
        "INSERT INTO customer (customer_name, customer_phone) 
         VALUES (:name, :phone)"
    );
    $stmt->execute([
        ':name'  => $customer_name,
        ':phone' => $customer_phone
    ]);

    $last_id = $conn->lastInsertId();

    // Nếu muốn toast message, lưu vào session
    $_SESSION['toast_message'] = "Thêm khách hàng thành công!";

    // Trả JSON thành công
    echo json_encode(['success' => true, 'id' => $last_id]);
    exit;

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]);
    exit;
}
