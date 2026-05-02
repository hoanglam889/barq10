<?php 
require 'include/config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$note    = $_POST['order_note'] ?? '';
$customer_id = $_POST['fillerCustomer'] ?? '0';

// Hủy đơn
if (isset($_POST['cancel_order'])) {
    header("Location: cancel_order.php");
    exit();
}

function process_payment($conn, $user_id, $customer_id, $note, $payby, $success_msg) {
    // Tạo order mới
    $stmt = $conn->prepare(
        "INSERT INTO orders (user_id, customer_id, order_payby, total_amount, status, Order_note)
         VALUES (:uid, :cid, :payby, 0, 'paid', :note)"
    );
    $stmt->execute([
        ':uid'   => $user_id,
        ':cid'   => $customer_id,
        ':payby' => $payby,
        ':note'  => $note
    ]);
    $order_id = $conn->lastInsertId();

    // Lấy cart_id
    $stmt = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = :uid");
    $stmt->execute([':uid' => $user_id]);
    $cart_id = $stmt->fetchColumn();

    // Lấy item trong cart
    $stmt = $conn->prepare(
        "SELECT drink_id, cart_detail_price 
         FROM cart_details 
         WHERE cart_id = :cid"
    );
    $stmt->execute([':cid' => $cart_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Chèn order_details và tính tổng
    $total = 0;
    foreach ($cart_items as $item) {
        $drink_id = $item['drink_id'];
        $price    = $item['cart_detail_price'];
        $qty      = isset($_POST['drink_quantity'][$drink_id]) 
                    ? max(1, intval($_POST['drink_quantity'][$drink_id])) 
                    : 1;

        $stmt = $conn->prepare(
            "INSERT INTO order_details (order_id, drink_id, order_detail_quantity, order_detail_price)
             VALUES (:oid, :did, :qty, :price)"
        );
        $stmt->execute([
            ':oid'   => $order_id,
            ':did'   => $drink_id,
            ':qty'   => $qty,
            ':price' => $price
        ]);

        $total += $qty * $price;
    }

    // Cập nhật tổng tiền vào orders
    $stmt = $conn->prepare(
        "UPDATE orders SET total_amount = :total WHERE order_id = :oid"
    );
    $stmt->execute([':total' => $total, ':oid' => $order_id]);

    // Xóa cart sau khi thanh toán
    $stmt = $conn->prepare("DELETE FROM cart_details WHERE cart_id = :cid");
    $stmt->execute([':cid' => $cart_id]);

    $stmt = $conn->prepare("DELETE FROM carts WHERE cart_id = :cid");
    $stmt->execute([':cid' => $cart_id]);

    $_SESSION['toast_message'] = $success_msg;
    header("Location: index.php");
    exit();
}

// Thanh toán các loại
if (isset($_POST['confirm_cash'])) {
    process_payment($conn, $user_id, $customer_id, $note, '1', "Thanh toán thành công!");
}
if (isset($_POST['confirm_transfer'])) {
    process_payment($conn, $user_id, $customer_id,$note, '2', "Thanh toán thành công!");
}
if (isset($_POST['confirm_debt'])) {
    process_payment($conn, $user_id, $customer_id,$note, '3', "Ghi nợ thành công!");
}
if (isset($_POST['confirm_export'])) {
    process_payment($conn, $user_id, $customer_id,$note, '4', "Xuất bếp thành công!");
}
?>
