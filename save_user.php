<?php
include('include/config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    die('Unauthorized');
}

$id = $_POST['user_id'] ?? '';
$username = trim($_POST['user_name'] ?? '');
$password = $_POST['user_password'] ?? '';
$profile = trim($_POST['user_profile'] ?? '');
$role = $_POST['user_role'] ?? 'user';

if (empty($username) || empty($profile)) {
    die('Vui lòng điền đầy đủ Tên đăng nhập và Tên hiển thị');
}

try {
    if (empty($id)) {
        // Thêm mới
        if (empty($password)) {
            die('Mật khẩu bắt buộc khi thêm mới');
        }
        
        // Kiểm tra trùng username
        $check = $conn->prepare("SELECT 1 FROM users WHERE user_name = ?");
        $check->execute([$username]);
        if ($check->rowCount() > 0) {
            die('Tên đăng nhập đã tồn tại');
        }

        $stmt = $conn->prepare("INSERT INTO users (user_name, user_password, user_profile, user_role, status) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$username, $password, $profile, $role]);
    } else {
        // Sửa
        if (!empty($password)) {
            $stmt = $conn->prepare("UPDATE users SET user_name = ?, user_password = ?, user_profile = ?, user_role = ? WHERE user_id = ?");
            $stmt->execute([$username, $password, $profile, $role, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET user_name = ?, user_profile = ?, user_role = ? WHERE user_id = ?");
            $stmt->execute([$username, $profile, $role, $id]);
        }
    }
    echo "success";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
