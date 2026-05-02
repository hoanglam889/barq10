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

$sql = "SELECT o.order_id, c.customer_name, o.order_time, o.total_amount, o.order_payby, o.Order_note
        FROM orders o
        LEFT JOIN customer c ON o.customer_id = c.customer_id
        ORDER BY o.order_time DESC";
$stmt = $conn->query($sql);
$initial_show = 12;
$count = 0;
?>

<link rel="stylesheet" href="css/dash_order.css">

<div class="order-header fixed-top bg-white shadow-sm px-3 py-2 border-bottom" style="top:64px; z-index:1030;">
  <!-- Hàng 1: tìm kiếm + bộ lọc nhanh -->
  <div class="row-1 d-flex align-items-center flex-wrap gap-2 mb-2">
    <input type="text" id="searchInput" placeholder="Tìm kiếm đơn hàng..." class="form-control" style="max-width:180px;">
    <select id="filterQuick" class="form-select" style="width:150px;">
      <option value="today" selected>Hôm nay</option>
      <option value="week">7 ngày qua</option>
      <option value="month">Trong tháng</option>
       <option value="all">Tất cả đơn</option>
    </select>
  </div>

  <!-- Hàng 2: nút lọc + bộ lọc ngày -->
  <div class="row-2 d-flex align-items-center flex-wrap gap-2">
    <!-- Nút lọc theo tiền khách còn nợ -->
    <button class="btn btn-warning" id="btnFilterDebt" data-bs-toggle="modal" data-bs-target="#modal_loc">
      <i class="bi bi-cash-stack"></i> Lọc còn nợ
    </button>
    
    <!-- Nút Reset Lọc -->
    <button class="btn btn-secondary" id="btnResetFilter" title="Xóa tất cả bộ lọc">
      <i class="fa-solid fa-rotate-right"></i> Bỏ lọc
    </button>

    <!-- Trên mobile: nút “Bộ lọc” thu gọn -->
    <button class="btn btn-outline-secondary d-lg-none ms-auto" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
      <i class="bi bi-sliders"></i>
    </button>

    <!-- Collapse chứa các input ngày trên mobile -->
    <div class="collapse w-100 mt-2" id="filterCollapse">
      <div class="d-flex flex-wrap gap-2">
        <input type="date" id="filterFrom" class="form-control flex-fill">
        <input type="date" id="filterTo" class="form-control flex-fill">
        <button class="btn btn-primary flex-fill" id="btnApplyDateFilter">Lọc</button>
      </div>
    </div>
  </div>
</div>

<div class="order-list">
  <table>
    <tbody id="orderBody">
      <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
        $count++;
        $hidden_class = ($count > $initial_show) ? 'hidden-order' : '';
        $pay = (string)$row['order_payby'];
        $pay_text = '';
        $pay_class = '';
        if ($pay === '1') { $pay_text = 'Tiền mặt'; $pay_class = 'pay-cash'; }
        elseif ($pay === '2') { $pay_text = 'Chuyển khoản'; $pay_class = 'pay-bank'; }
        elseif ($pay === '3') { $pay_text = 'Ghi nợ'; $pay_class = 'pay-debt'; }
        elseif ($pay === '4') { $pay_text = 'Xuất bếp'; $pay_class = 'pay-export'; }
      ?>
      <!-- Thêm data-id ở đây -->
      <tr class="order-row <?php echo $hidden_class; ?>" data-show="1" data-id="<?php echo htmlspecialchars($row['order_id']); ?>">
        <td><?php echo htmlspecialchars($row['order_id']); ?></td>
        <td class="customer-name"><?php echo htmlspecialchars($row['customer_name']); ?></td>
        <td class="order-date"><?php echo htmlspecialchars($row['order_time']); ?></td>
        <td class="other-info"><?php echo format_vnd($row['total_amount']); ?></td>
        <td class="payment-method"><span class="status-pill <?php echo $pay_class; ?>"><?php echo $pay_text; ?></span></td>
        <td class="action-icons">
             <i class="fa fa-edit" title="Sửa" onclick="editOrder(<?= $row['order_id']; ?>)"></i>   
          <?php if ($role === 'admin'): ?>
            <i class="fa fa-trash" title="Xóa"></i>
          <?php endif; ?>
        </td>
      </tr>

      <tr class="order-details <?php echo $hidden_class; ?>" style="display:none;">
        <td colspan="6">
          <strong>Chi tiết đơn hàng:</strong><br>
          <?php
            $ord = (int)$row['order_id'];
            $sql_details = "SELECT sd.drink_name, od.order_detail_quantity, od.order_detail_price
                            FROM order_details od
                            LEFT JOIN soft_drink sd ON sd.drink_id = od.drink_id
                            WHERE od.order_id = ?";
            $st2 = $conn->prepare($sql_details);
            $st2->execute([$ord]);
            while ($d = $st2->fetch(PDO::FETCH_ASSOC)) {
              $dongia = (float)$d['order_detail_price'] * (int)$d['order_detail_quantity'];
              echo htmlspecialchars($d['drink_name'])." x ".(int)$d['order_detail_quantity']." = ".format_vnd($dongia)."<br>";
            }
            if (!empty($row['Order_note'])) {
              echo "<em>Ghi chú: ".htmlspecialchars($row['Order_note'])."</em>";
            }
          ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Modal sửa đơn hàng -->
<div id="editOrderModal" class="hidden"> 
  <div class="modal-box">
    <h2>Sửa đơn hàng</h2> 
    <form id="editOrderForm" action="update_order.php" method="POST"> 
      <input type="hidden" id="editOrderId" name="order_id"> 

      <!-- Chọn khách hàng -->
      <label>Khách hàng:
      <div style="display:flex; gap:10px; align-items:center;">
        <select id="editOrderCustomer" name="customer_id" required style="flex:1;">
          <option value="">-- Chọn khách hàng --</option>
          <?php
          $custStmt = $conn->query("SELECT customer_id, customer_name FROM customer WHERE status = 1 ORDER BY customer_name ASC");
          while($c = $custStmt->fetch(PDO::FETCH_ASSOC)){
              echo '<option value="'.htmlspecialchars($c['customer_id']).'">'.htmlspecialchars($c['customer_name']).'</option>';
          }
          ?>
        </select>
        <button type="button" onclick="openQuickAddCustomerOrder()" style="width:40px; height:40px; background:#007bff; color:white; border:none; border-radius:5px; font-size:20px; font-weight:bold; cursor:pointer; flex-shrink:0;" title="Thêm khách hàng nhanh">+</button>
      </div>
      </label>

      <!-- Chi tiết món hàng -->
      <div id="editOrderContent">
        <!-- JS sẽ fill chi tiết các món: tên, số lượng, giá -->
      </div>

      <!-- Phương thức thanh toán -->
      <label>Thanh toán bằng:
        <select name="payment_method" id="editOrderPayby">
          <option value="1">Tiền mặt</option>
          <option value="2">Chuyển khoản</option>
          <option value="3">Ghi nợ</option>
          <option value="4">Xuất bếp</option>
        </select>
      </label>
      

      <!-- Ghi chú -->
      <label>Ghi chú:
        <textarea name="order_note" id="editOrderNote"></textarea>
      </label>

      <div class="modal-buttons">
        <button type="button" class="btn-cancel" onclick="closeEditOrderModal()">Hủy</button>
        <button type="button" class="btn-save" onclick="saveOrderChanges()">Lưu</button>
      </div>
    </form>
  </div>
</div>


<!-- Modal còn nợ -->
<div class="modal" id="modal_loc">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Danh sách người còn nợ</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body: danh sách khách đang ghi nợ -->
      <div class="modal-body">
        <?php
          $debtorStmt = $conn->query(
            "SELECT DISTINCT c.customer_id, c.customer_name\n"
            . "FROM orders o\n"
            . "INNER JOIN customer c ON c.customer_id = o.customer_id\n"
            . "WHERE o.order_payby = '3'\n"
            . "ORDER BY c.customer_name ASC"
          );
          $hasDebtor = false;
          while ($cust = $debtorStmt->fetch(PDO::FETCH_ASSOC)):
            $hasDebtor = true;
        ?>
        <div class="customer-card border-bottom p2" data-id="<?php echo htmlspecialchars($cust['customer_id']); ?>" onclick="openCusNo(this.getAttribute('data-id'))" style="cursor:pointer;">
          <div class="customer-info">
            <span class="customer-name"><?php echo htmlspecialchars($cust['customer_name']); ?></span>
          </div>
          <div class="customer-actions">
            <i class="fa fa-chevron-right" title="Xem đơn nợ"></i>
          </div>
        </div>
        <?php endwhile; ?>
        <?php if (!$hasDebtor): ?>
          <div class="alert alert-info mb-0">Hiện không có khách nào đang ghi nợ.</div>
        <?php endif; ?>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- Scripts moved to dash_order.js -->

<style> 
#editOrderModal.hidden { display: none; } 
#editOrderModal { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999; } 
#editOrderModal .modal-box { background: #fff; padding: 20px; border-radius: 10px; width: 400px; max-width: 95%; box-shadow: 0 4px 15px rgba(0,0,0,0.2); } 
#editOrderModal h2 { margin-top: 0; } .modal-buttons { display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px; } 
.btn-cancel, .btn-save { padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; color: #fff; } 
.btn-cancel { background: #6b7280; } .btn-save { background: #2563eb; } 
.btn-cancel:hover { background: #4b5563; } 
.btn-save:hover { background: #1e40af; } 
</style>
  <div class="load-more-container">
    <button id="loadMoreBtn" class="btn-primary hidden">Xem thêm</button>
  </div>
</div>

<!-- Quick Add Customer Modal (for dash_order.php) -->
<div id="quickAddCustomerOrderModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 10000;">
  <div class="modal-box" style="background: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 400px;">
    <h3 style="margin-top:0;">Thêm khách hàng nhanh</h3>
    <div style="margin-bottom:15px;">
      <label>Tên khách hàng: <span style="color:red;">*</span><br>
        <input type="text" id="quickCusNameOrder" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; margin-top:5px;">
      </label>
    </div>
    <div style="margin-bottom:20px;">
      <label>Số điện thoại:<br>
        <input type="text" id="quickCusPhoneOrder" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; margin-top:5px;">
      </label>
    </div>
    <div style="display:flex; gap:10px; justify-content:flex-end;">
      <button type="button" onclick="closeQuickAddCustomerOrder()" style="padding:8px 16px; background:#f8f9fa; color:#333; border:1px solid #ccc; border-radius:4px;">Hủy</button>
      <button type="button" onclick="saveQuickCustomerOrder()" style="padding:8px 16px; background:#007bff; color:white; border:none; border-radius:4px;">Lưu khách</button>
    </div>
  </div>
</div>




