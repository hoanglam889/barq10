<?php
include('include/config.php');
$stmt = $conn->query('SELECT order_id, status FROM orders ORDER BY order_time DESC LIMIT 10');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
