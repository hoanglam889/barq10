<?php
include('include/config.php');
session_start();

function is_ajax_request(): bool {
    if (!empty($_POST['ajax']) && $_POST['ajax'] == '1') return true;
    $hdr = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    return strtolower($hdr) === 'xmlhttprequest';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (is_ajax_request()) {
        http_response_code(405);
        echo 'Phương thức không hợp lệ';
        exit;
    }
    $_SESSION['toast_message'] = "Phương thức không hợp lệ";
    header("Location: dash_order.php");
    exit;
}

$order_id       = $_POST['order_id'] ?? '';
$order_note     = $_POST['order_note'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';
$customer_id    = $_POST['customer_id'] ?? '';
$quantities     = $_POST['quantity'] ?? [];

if (!$order_id) {
    if (is_ajax_request()) {
        http_response_code(400);
        echo 'Thiếu ID đơn hàng';
        exit;
    }
    $_SESSION['toast_message'] = "Thiếu ID đơn hàng";
    header("Location: dash_order.php");
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE orders SET Order_note = :note, order_payby = :pay, customer_id = :cid WHERE order_id = :id");
    $stmt->execute([
        ':note' => $order_note,
        ':pay'  => $payment_method,
        ':cid'  => $customer_id,
        ':id'   => $order_id
    ]);

    foreach ($quantities as $drink_id => $qty) {
        $stmt2 = $conn->prepare("UPDATE order_details SET order_detail_quantity = :qty WHERE order_id = :oid AND drink_id = :did");
        $stmt2->execute([
            ':qty' => $qty,
            ':oid' => $order_id,
            ':did' => $drink_id
        ]);
    }

    if (is_ajax_request()) {
        http_response_code(200);
        echo 'success';
        exit;
    }
    $_SESSION['toast_message'] = "Cập nhật đơn hàng thành công!";
    header("Location: index.php");
    exit;

} catch (PDOException $e) {
    if (is_ajax_request()) {
        http_response_code(500);
        echo 'Lỗi DB: ' . $e->getMessage();
        exit;
    }
    $_SESSION['toast_message'] = "Lỗi DB: " . $e->getMessage();
    header("Location: dash_order.php");
    exit;
}
