<?php
include('include/config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

$sql = "SELECT customer_id, customer_name, customer_phone, status FROM customer ORDER BY customer_id DESC";
$stmt = $conn->query($sql);

$initial_show = 12;
$count = 0;
?>
<link rel="stylesheet" href="css/style_customer.css">

<div class="customer-header fixed-top bg-white shadow-sm px-3 py-2 border-bottom" style="top:64px; z-index:1030;">
  <div style="display:flex; gap:10px; align-items:center;">
    <button type="button" class="btn-add" id="addCustomer">Thêm khách hàng</button>
    <input type="text" id="searchInput" placeholder="Tìm kiếm khách hàng..." class="search-box">
  </div>
</div>

<div class="customer-list" style="padding-top:70px;">
  <div id="customerContainer">
    <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)):
      $count++;
      $hidden_class = ($count > $initial_show) ? 'cus-hidden' : '';
      $inactive_class = ($row['status'] == 0) ? 'inactive-item' : '';
    ?>
      <div class="customer-card <?= $hidden_class; ?> <?= $inactive_class; ?>">
        <div class="customer-info">
          <span class="customer-name"><?= htmlspecialchars($row['customer_name']); ?></span>
          <span class="customer-phone"><?= htmlspecialchars($row['customer_phone']); ?></span>
        </div>
        <div class="customer-actions" style="gap:10px;">
          <i class="fa fa-edit" title="Sửa" onclick="editCustomer(<?= $row['customer_id']; ?>)" style="color:#007bff; cursor:pointer;"></i>
          <label class="switch" title="Trạng thái">
            <input type="checkbox" onchange="toggleStatus('customer', <?= $row['customer_id']; ?>, this.checked)" <?= $row['status'] == 1 ? 'checked' : ''; ?>>
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

<!-- Modal Thêm -->
<div id="addModal" class="cus-hidden">
  <div class="modal-box">
    <h2>Thêm khách hàng</h2>
    <form id="addForm" method="POST" action="add_customer.php">
      <label>Tên: <input type="text" name="customer_name" required></label>
      <label>Số điện thoại: <input type="text" name="customer_phone"></label>
      <div class="modal-buttons">
        <button type="button" class="btn-cancel">Hủy</button>
        <button type="submit" class="btn-save">Lưu</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Sửa -->
<div id="editModal" class="cus-hidden">
  <div class="modal-box">
    <h2>Sửa khách hàng</h2>
    <form id="editForm" method="POST" action="update_customer.php">
      <input type="hidden" id="editCustomerId" name="customer_id">
      <label>Tên: <input type="text" id="editCustomerName" name="customer_name" required></label>
      <label>Số điện thoại: <input type="text" id="editCustomerPhone" name="customer_phone"></label>
      <div class="modal-buttons">
        <button type="button" class="btn-cancel">Hủy</button>
        <button type="submit" class="btn-save">Lưu</button>
      </div>
    </form>
  </div>
</div>

<script> window.initial_show = <?= $initial_show ?>; </script>

