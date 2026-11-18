<?php 
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Thiết lập múi giờ PHP

$servername = getenv('DB_HOST');
$db_port = getenv('DB_PORT') ?: 3308;
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

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
