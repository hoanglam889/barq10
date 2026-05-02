<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Đăng nhập - POSEIDON</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style_login.css">
  <link rel="icon" href="img/icon.png" type="image/png">
  <link rel="apple-touch-icon" href="icons/icon-192.png">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Bar Poseidon">
  <link rel="manifest" href="manifest.json">
</head>
<body>

<div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
  <div class="login-container">
    <!-- Logo nếu có -->
    <img src="img/logo_poseidon.png" alt="Logo" class="logo">

    <h3 class="login-title">ĐĂNG NHẬP</h3>

    <form action="login.php" method="POST">
      <div class="mb-3">
        <label for="user_name" class="form-label">Tên đăng nhập</label>
        <input type="text" class="form-control" id="username" name="user_name" placeholder="Nhập tên đăng nhập">
      </div>
      <div class="mb-3">
        <label for="user_password" class="form-label">Mật khẩu</label>
        <input type="password" class="form-control" id="password" name="user_password"e placeholder="Nhập mật khẩu">
      </div>

    <?php 
session_start();
require_once ("include/config.php");

if (isset($_POST["submit"])) { 
    // Lọc và làm sạch dữ liệu đầu vào
    $username = trim($_POST['user_name']);
    $password = trim($_POST['user_password']);
    $error_message = "";

    // Validate dữ liệu đầu vào
    if (empty($username)) {
        $error_message = "Tên đăng nhập không được để trống!";
    } elseif (empty($password)) {
        $error_message = "Mật khẩu không được để trống!";
    }

    // Hiển thị lỗi nếu có
    if (!empty($error_message)) {
        echo "<div style='color: red; margin: 10px 0;'>$error_message</div>";
    } else {
        // Truy vấn dữ liệu từ database
        try {
            $sql = "SELECT * FROM users WHERE user_name = :username LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':username' => $username]);

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Kiểm tra trạng thái khóa (mặc định nếu null là vẫn hoạt động)
                if (isset($user['status']) && $user['status'] == 0) {
                    echo "<div style='color: red;'>Tài khoản này đã bị khóa. Vui lòng liên hệ quản lý!</div>";
                } else {
                    // Kiểm tra mật khẩu
                    if ($password === $user['user_password']) {
                            // Lưu thông tin vào session và chuyển hướng
                            $_SESSION['user_id'] = $user['user_id'];
                            $_SESSION['user_name'] = $user['user_name'];
                            $_SESSION['user_profile'] = $user['user_profile'];
                            $_SESSION['user_role'] = $user['user_role'];
                            header("Location: index.php");
                            exit();
                    } else {
                        echo "<div style='color: red;'>Mật khẩu không đúng!</div>";
                    }
                }
            } else {
                echo "<div style='color: red;'>Tên đăng nhập không tồn tại!</div>";
            }
        } catch (PDOException $e) {
            echo "<div style='color: red;'>Lỗi hệ thống: " . $e->getMessage() . "</div>";
        }
    }
}
?>
      <input type="submit" name ="submit" class="btn btn-login mt-2" value="Đăng nhập">
    </form>

    <div class="text-small">
      Chưa có tài khoản? <a href="#" style="color: maroon; text-decoration: underline;">Đăng ký</a>
    </div>
  </div>
</div>

</body>
</html>
