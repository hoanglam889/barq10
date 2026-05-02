<?php
include('include/config.php');
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(null);
    exit;
}

$id = (int)$_GET['id'];

try {
    $stmt = $conn->prepare(
        "SELECT drink_id, drink_name, drink_cost, drink_price, drink_img 
         FROM soft_drink 
         WHERE drink_id = :id 
         LIMIT 1"
    );
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($product ?: null);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]);
}
