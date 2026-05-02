<?php
include('include/config.php');

if (!isset($_GET['id'])) {
    echo json_encode(null);
    exit;
}

$order_id = (int)$_GET['id'];

// Lấy thông tin order
$stmt = $conn->prepare("SELECT order_id, customer_id, total_amount, order_payby, Order_note 
                        FROM orders WHERE order_id = :id LIMIT 1");
$stmt->execute([':id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(null);
    exit;
}

// Lấy chi tiết món trong order
$stmt2 = $conn->prepare("SELECT od.drink_id, sd.drink_name, od.order_detail_quantity, od.order_detail_price
                         FROM order_details od
                         LEFT JOIN soft_drink sd ON sd.drink_id = od.drink_id
                         WHERE od.order_id = :id");
$stmt2->execute([':id' => $order_id]);
$details = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$order['details'] = $details;

echo json_encode($order);
