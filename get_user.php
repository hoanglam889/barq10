<?php
include('include/config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    die(json_encode(['error' => 'Unauthorized']));
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT user_id, user_name, user_profile, user_role, status FROM users WHERE user_id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($data);
?>
