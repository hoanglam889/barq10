<?php
include('include/config.php');
header('Content-Type: application/json');

// Chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

// Lấy dữ liệu từ form
$customer_id    = $_POST['customer_id'] ?? '';
$customer_name  = trim($_POST['customer_name'] ?? '');
$customer_phone = trim($_POST['customer_phone'] ?? '');

if (!$customer_id || !$customer_name) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
    exit;
}

try {
    // Cập nhật khách hàng
    $stmt = $conn->prepare("
        UPDATE customer
        SET customer_name = :name,
            customer_phone = :phone
        WHERE customer_id = :id
    ");
    $stmt->execute([
        ':name'  => $customer_name,
        ':phone' => $customer_phone,
        ':id'    => $customer_id
    ]);

    // Trả về JSON thành công
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]);
}
