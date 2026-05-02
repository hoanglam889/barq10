<?php
include('include/config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

// Only admin can view users
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    echo "<div style='padding:20px;'>Bạn không có quyền truy cập trang này.</div>";
    exit;
}

$sql = "SELECT user_id, user_name, user_profile, user_role, status FROM users ORDER BY user_id DESC";
$stmt = $conn->query($sql);

$initial_show = 12;
$count = 0;
?>
<link rel="stylesheet" href="css/style_customer.css">

<div class="customer-header fixed-top bg-white shadow-sm px-3 py-2 border-bottom" style="top:64px; z-index:1030;">
  <div style="display:flex; gap:10px; align-items:center;">
    <button type="button" class="btn-add" id="addUser">Thêm tài khoản</button>
    <input type="text" id="searchInput" placeholder="Tìm kiếm tài khoản..." class="search-box">
  </div>
</div>

<div class="customer-list" style="padding-top:70px;">
  <div id="userContainer">
    <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)):
      $count++;
      $hidden_class = ($count > $initial_show) ? 'cus-hidden' : '';
      $inactive_class = ($row['status'] == 0) ? 'inactive-item' : '';
    ?>
      <div class="customer-card user-row <?= $hidden_class; ?> <?= $inactive_class; ?>">
        <div class="customer-info">
          <span class="customer-name"><?= htmlspecialchars($row['user_profile']); ?> (<?= htmlspecialchars($row['user_name']); ?>)</span>
          <span class="customer-phone" style="color: <?= $row['user_role'] === 'admin' ? '#d97706' : '#6c757d'; ?>">
            Vai trò: <?= strtoupper(htmlspecialchars($row['user_role'])); ?>
          </span>
        </div>
        <div class="customer-actions" style="gap:10px;">
          <i class="fa fa-edit" title="Sửa" onclick="editUser(<?= $row['user_id']; ?>)" style="color:#007bff; cursor:pointer;"></i>
          <label class="switch" title="Trạng thái">
            <!-- Prevent admin from disabling themselves -->
            <input type="checkbox" onchange="toggleStatus('user', <?= $row['user_id']; ?>, this.checked)" 
                <?= $row['status'] == 1 ? 'checked' : ''; ?>
                <?= $row['user_id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>
            >
            <span class="slider round"></span>
          </label>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
  <div class="load-more-container">
    <button id="loadMoreBtn" class="btn-primary cus-hidden">Xem thêm</button>
  </div>
</div>

<!-- Modal Thêm/Sửa -->
<div id="userModal" class="cus-hidden" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
  <div class="modal-box" style="background: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 400px;">
    <h2 id="userModalTitle">Thêm tài khoản</h2>
    <form id="userForm" method="POST" action="save_user.php">
      <input type="hidden" name="user_id" id="userId">
      
      <div style="margin-bottom: 10px;">
        <label>Tên đăng nhập (Username): <br>
        <input type="text" name="user_name" id="userName" required style="width: 100%; padding: 8px; margin-top:5px; border: 1px solid #ccc; border-radius: 4px;"></label>
      </div>

      <div style="margin-bottom: 10px;">
        <label>Mật khẩu: <br>
        <input type="password" name="user_password" id="userPassword" placeholder="Để trống nếu không đổi" style="width: 100%; padding: 8px; margin-top:5px; border: 1px solid #ccc; border-radius: 4px;"></label>
        <small id="passwordHint" style="color:#6c757d; font-size: 12px; display:none;">Bắt buộc khi thêm mới</small>
      </div>

      <div style="margin-bottom: 10px;">
        <label>Tên hiển thị (Profile): <br>
        <input type="text" name="user_profile" id="userProfile" required style="width: 100%; padding: 8px; margin-top:5px; border: 1px solid #ccc; border-radius: 4px;"></label>
      </div>

      <div style="margin-bottom: 15px;">
        <label>Phân quyền: <br>
        <select name="user_role" id="userRole" style="width: 100%; padding: 8px; margin-top:5px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="user">Nhân viên (User)</option>
            <option value="admin">Quản lý (Admin)</option>
        </select></label>
      </div>

      <div class="modal-buttons" style="display: flex; gap: 10px; justify-content: flex-end;">
        <button type="button" class="btn-cancel" onclick="closeUserModal()" style="background:#f8f9fa; color:#333; border:1px solid #ccc; padding:8px 16px; border-radius:4px;">Hủy</button>
        <button type="submit" class="btn-primary" style="background:#007bff; color:white; border:none; padding:8px 16px; border-radius:4px;">Lưu</button>
      </div>
    </form>
  </div>
</div>

<script src="js/dash_users.js"></script>
