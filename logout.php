<?php
session_start();
session_unset(); // XÃ³a táº¥t cáº£ cÃ¡c session
session_destroy(); // Há»§y session

// Chuyá»n hÆ°á»ng vá» trang ÄÄng nháº­p
header("Location: login.php");
exit();
?>
