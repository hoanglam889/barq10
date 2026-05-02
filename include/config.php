<?php 
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Thiết lập múi giờ PHP

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qlbar";

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Thiết lập múi giờ cho phiên MySQL
    $conn->exec("SET time_zone = '+07:00'");

} catch(PDOException $e) {
    echo "Kết nối thất bại: " . $e->getMessage();
}

if (!function_exists('format_vnd')) {
    function format_vnd($number) {
        return number_format($number, 0, ',', '.') . 'đ';
    }
}
