<?php
session_start();
$toast_message = $_SESSION['toast_message'] ?? '';
$toast_type = $_SESSION['toast_type'] ?? 'success';
unset($_SESSION['toast_message'], $_SESSION['toast_type']);

echo json_encode([
  'toast' => $toast_message,
  'type'  => $toast_type
]);
?>
