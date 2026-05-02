<?php
require 'include/config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "Bạn chưa đăng nhập.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['drink_quantity'])) {
    $quantities = $_POST['drink_quantity'];

    // Lọc các loại nước có số lượng > 0
    $valid_items = array_filter($quantities, function($qty) {
        return intval($qty) > 0;
    });

    if (count($valid_items) === 0) {
        session_start();
        $_SESSION['toast_message'] = "Vui lòng chọn ít nhất 1 loại nước!";
        header("Location: index.php");
        exit();
    }

    try {
        $conn->beginTransaction();

        // Kiểm tra giỏ hàng pending đã tồn tại chưa
        $stmt = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = :user_id AND status = 'pending' LIMIT 1");
        $stmt->execute(['user_id' => $user_id]);
        $existing_cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_cart) {
            $cart_id = $existing_cart['cart_id'];
        } else {
            // Nếu chưa có, tạo giỏ hàng mới
            $stmt = $conn->prepare("INSERT INTO carts (user_id, status) VALUES (:user_id, 'pending')");
            $stmt->execute(['user_id' => $user_id]);
            $cart_id = $conn->lastInsertId();
        }

        // Lấy giá nước từ bảng soft_drink
        $ids = array_keys($valid_items);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $conn->prepare("SELECT drink_id, drink_price FROM soft_drink WHERE drink_id IN ($placeholders)");
        $stmt->execute($ids);

        $prices = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $prices[$row['drink_id']] = $row['drink_price'];
        }

        // Thêm từng sản phẩm vào cart_details
        foreach ($valid_items as $drink_id => $qty) {
            $price = $prices[$drink_id] ?? 0;

            // Kiểm tra xem drink đã có trong giỏ hàng chưa
            $stmt = $conn->prepare("
                SELECT drink_quantity FROM cart_details
                WHERE cart_id = :cart_id AND drink_id = :drink_id
            ");
            $stmt->execute([
                'cart_id' => $cart_id,
                'drink_id' => $drink_id
            ]);

            if ($stmt->rowCount() > 0) {
                // Nếu có rồi thì cập nhật số lượng
                $updateStmt = $conn->prepare("
                    UPDATE cart_details 
                    SET drink_quantity = drink_quantity + :qty 
                    WHERE cart_id = :cart_id AND drink_id = :drink_id
                ");
                $updateStmt->execute([
                    'qty' => $qty,
                    'cart_id' => $cart_id,
                    'drink_id' => $drink_id
                ]);
            } else {
                // Nếu chưa có thì thêm mới
                $insertStmt = $conn->prepare("
                    INSERT INTO cart_details (cart_id, drink_id, drink_quantity, cart_detail_price)
                    VALUES (:cart_id, :drink_id, :drink_quantity, :cart_detail_price)
                ");
                $insertStmt->execute([
                    'cart_id' => $cart_id,
                    'drink_id' => $drink_id,
                    'drink_quantity' => $qty,
                    'cart_detail_price' => $price
                ]);
            }
        }

        $conn->commit();
        header("Location: pay.php?cart_id=$cart_id");
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Lỗi khi xử lý giỏ hàng: " . $e->getMessage();
    }
} else {
    echo "Không có dữ liệu gửi lên!";
}
?>
