<?php
include('include/config.php');
$stmt = $conn->query('SELECT status, count(*) FROM orders GROUP BY status');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
