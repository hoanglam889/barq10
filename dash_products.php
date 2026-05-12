<?php
include('include/config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
$role = $_SESSION['user_role'] ?? '';

$sql = "SELECT drink_id, drink_name, drink_quantity, drink_cost, drink_price, drink_img, status 
        FROM soft_drink 
        ORDER BY drink_id DESC";
$stmt = $conn->query($sql);

$initial_show = 12;
$count = 0;
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="css/style_product.css">

<div class="order-header fixed-top bg-white shadow-sm px-3 py-2 border-bottom" style="top:64px; z-index:1030;">
  
  <input class="btn-add" type="button" id="addProduct" value="Thêm món mới">
</div>

<div class="order-list" style="padding-top:70px;">
  <table class="tbl-order-header">
    <thead>
      <tr>
        <th>Tên sản phẩm</th>
        <th>Giá vốn</th>
        <th>Giá bán</th>
        <th>Ảnh</th>
        <th>Hành động</th>
      </tr>
    </thead>
  </table>

  <table>
    <tbody id="productBody">
      <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
        $count++;
        $hidden_class = ($count > $initial_show) ? 'hidden-order' : '';
        $inactive_class = ($row['status'] == 0) ? 'inactive-item' : '';
      ?>
      <tr class="order-row <?= $hidden_class; ?> <?= $inactive_class; ?>" data-show="1">
        <td data-label="Tên"><?= htmlspecialchars($row['drink_name']); ?></td>
        <td data-label="Giá vốn"><?= number_format($row['drink_cost'], 0, ',', '.'); ?> đ</td>
        <td data-label="Giá bán"><?= number_format($row['drink_price'], 0, ',', '.'); ?> đ</td>
        <td data-label="Ảnh">
          <img src="<?= htmlspecialchars($row['drink_img']); ?>" 
               alt="<?= htmlspecialchars($row['drink_name']); ?>" 
               style="width:50px;height:50px;border-radius:6px;object-fit:cover;">
        </td>
        <td data-label="Hành động" class="action-icons" style="gap:10px;">
          <i class="fa fa-edit" title="Sửa" onclick="editProduct(<?= $row['drink_id']; ?>)" style="color:#007bff; cursor:pointer;"></i>
          <label class="switch" title="Trạng thái">
            <input type="checkbox" onchange="toggleStatus('product', <?= $row['drink_id']; ?>, this.checked)" <?= $row['status'] == 1 ? 'checked' : ''; ?>>
            <span class="slider round"></span>
          </label>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <div class="load-more-container">
    <button id="loadMoreBtn" class="btn-primary hidden">Xem thêm</button>
  </div>

  <!-- Modal sửa -->
  <div id="editModal" class="hidden">
    <div class="modal-box">
      <h2>Sửa sản phẩm</h2>
      <form id="editForm" action="update_product.php" method="POST"  enctype="multipart/form-data">
        <input type="hidden" id="editDrinkId" name="drink_id">
        <label>Tên:
          <input type="text" id="editDrinkName" name="drink_name">
        </label>
        <label>Giá vốn:
          <input type="number" id="editDrinkCost" name="drink_cost">
        </label>
        <label>Giá bán:
          <input type="number" id="editDrinkPrice" name="drink_price">
        </label>
        <label>Ảnh sản phẩm:
        <input type="file" id="editDrinkImg" name="drink_img" accept="image/*">
      </label>
      <img id="editImgPreview" src="" alt="Xem trước ảnh" style="max-width:100px; display:block; margin-top:5px;">
        <div class="modal-buttons">
          <button type="button" class="btn-cancel" onclick="closeEditModal()">Hủy</button>
          <button type="submit" class="btn-save">Lưu</button>
        </div>
      </form>
    </div>
  </div>
<!-- Modal thêm món -->
<div id="addModal" class="hidden">
  <div class="modal-box">
    <h2>Thêm sản phẩm</h2>
    <form id="addForm" action="add_product.php" method="POST" enctype="multipart/form-data">
      <label>Tên:
        <input type="text" id="addDrinkName" name="drink_name" required>
      </label>

      <label>Giá vốn:
        <input type="number" id="addDrinkCost" name="drink_cost" required>
      </label>

      <label>Giá bán:
        <input type="number" id="addDrinkPrice" name="drink_price" required>
      </label>

      <label>Ảnh sản phẩm:
        <input type="file" id="addDrinkImg" name="drink_img" accept="image/*" required>
      </label>
      <img id="addImgPreview" src="" alt="Xem trước ảnh" style="max-width:100px; display:block; margin-top:5px;">

      <div class="modal-buttons">
        <button type="button" class="btn-cancel" onclick="closeAddModal()">Hủy</button>
        <button type="submit" class="btn-save">Lưu</button>
      </div>
    </form>
  </div>
</div>

  <style>
    #editModal.hidden {display:none;}
    #editModal {
      position:fixed; inset:0;
      background:rgba(0,0,0,0.5);
      display:flex; align-items:center; justify-content:center;
      z-index:9999;
    }
    #editModal .modal-box {
      background:#fff; padding:20px; border-radius:10px;
      width:320px; max-width:95%; box-shadow:0 4px 15px rgba(0,0,0,0.2);
    }
    #editModal label {display:block; margin-bottom:10px; font-weight:500;}
    #editModal input {width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;}
    .modal-buttons {display:flex; justify-content:flex-end; gap:10px; margin-top:15px;}
    .btn-cancel, .btn-save {
      padding:6px 12px; border-radius:6px; border:none; cursor:pointer; color:#fff;
    }
    .btn-cancel {background:#6b7280;}
    .btn-save {background:#2563eb;}
    .btn-cancel:hover {background:#4b5563;}
    .btn-save:hover {background:#1e40af;}

    
  /* thêm */
    #addModal.hidden { display: none; }
#addModal {
  position: fixed; inset:0;
  background: rgba(0,0,0,0.5);
  display: flex; align-items:center; justify-content:center;
  z-index: 9999;
}
#addModal .modal-box {
  background: #fff; padding:20px; border-radius:10px;
  width:320px; max-width:95%; box-shadow:0 4px 15px rgba(0,0,0,0.2);
}
#addModal label { display:block; margin-bottom:10px; font-weight:500; }
#addModal input { width:100%; padding:8px; border:1px solid #ccc; border-radius:6px; }

  </style>
</div>

<script>
  window.initial_show = <?= $initial_show ?>;
</script>