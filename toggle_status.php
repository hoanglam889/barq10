<?php
include('include/config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$type = $_POST['type'] ?? '';
$id = (int)($_POST['id'] ?? 0);
$status = (int)($_POST['status'] ?? 0);

if (!$id || !in_array($type, ['product', 'customer', 'user'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

try {
    if ($type === 'product') {
        $stmt = $conn->prepare("UPDATE soft_drink SET status = :status WHERE drink_id = :id");
    } elseif ($type === 'customer') {
        $stmt = $conn->prepare("UPDATE customer SET status = :status WHERE customer_id = :id");
    } else {
        $stmt = $conn->prepare("UPDATE users SET status = :status WHERE user_id = :id");
    }
    
    $stmt->execute([':status' => $status, ':id' => $id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]);
}
