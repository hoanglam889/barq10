<?php 
    include('include/config.php');
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;
    if ($user_id) {
    try {
        $conn->beginTransaction();
        // 1. Lấy tất cả cart_id của user này
        $stmt = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $cart_ids = $stmt->fetchAll(PDO::FETCH_COLUMN); // Lấy mảng các cart_id

        if (!empty($cart_ids)) {
            // 2. Xóa cart_details trước
            $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
            $stmtDelDetails = $conn->prepare("DELETE FROM cart_details WHERE cart_id IN ($placeholders)");
            $stmtDelDetails->execute($cart_ids);

            // 3. Xóa carts
            $stmtDelCarts = $conn->prepare("DELETE FROM carts WHERE cart_id IN ($placeholders)");
            $stmtDelCarts->execute($cart_ids);
        }

        $conn->commit();
        header("Location: index.php");
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Lỗi khi xóa giỏ hàng: " . $e->getMessage();
    }
} else {
    echo "Không tìm thấy user_id trong session!";
}
?>