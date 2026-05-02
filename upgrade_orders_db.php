<?php
include('include/config.php');
try {
    $conn->exec("ALTER TABLE orders ADD COLUMN status TINYINT(1) DEFAULT 1");
    echo "Thêm cột status cho orders thành công!";
} catch (PDOException $e) {
    echo "Lỗi hoặc cột đã tồn tại: " . $e->getMessage();
}
?>
