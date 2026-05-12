<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
  <link rel="stylesheet" href="css/style_pay.css">
  <title>Document</title>
</head>
<body>
  
</body>
</html>
<?php 
  include('include/config.php');
  //Check xem login chưa
  session_start();
  $session_id = $_SESSION['user_id'];
   $sql = "select sd.drink_id, sd.drink_name, cd.drink_quantity, sd.drink_price, c.user_id from cart_details cd
inner join soft_drink sd on sd.drink_id = cd.drink_id
inner join carts c on cd.cart_id = c.cart_id
inner join users us on us.user_id = c.user_id
WHERE c.user_id = '$session_id'
";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  
?>


<div class="checkout-wrapper">
  <form method="POST" action="pay_by.php"> <!-- Form đặt bên trong -->

    <!-- Danh sách món đã chọn -->
    <div class="cart-items">
      <h3>🧾 Đơn hàng</h3>
      <?php 
      $total = 0;
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
        $quantity = (int)$row['drink_quantity'];
        $drink_id = $row['drink_id'];
        $price_per_unit = (int)$row['drink_price']; // lấy giá từ DB
        $line_price = $quantity * $price_per_unit;
        $total += $line_price;
        $_SESSION['total_amount'] = $total;
      ?>
      <ul>
        <li class="cart-item" data-id="<?= $drink_id ?>" data-price="<?= $price_per_unit ?>">
          <span class="item-name"><?= htmlspecialchars($row['drink_name']) ?></span>
          <div class="numeric-control">
            <button type="button" class="pay-btn-decrease">−</button>
             <input type="number" class="quantity-input" name="drink_quantity[<?= $drink_id ?>]" value="<?= $quantity ?>" min="1">
            <button type="button" class="pay-btn-increase">+</button>
          </div>
          <span class="item-price"><?= number_format($line_price, 0, ',', '.') ?> đ</span>
        </li>
      </ul>
      <?php } ?>
      <div class="cart-total">
        <strong>Tổng cộng: </strong><span id="total-price"><?= number_format($total, 0, ',', '.');
?> đ</span>
      </div>
    </div>

    <!-- Phần hành động thanh toán -->
        <?php 
          $sql = "SELECT * FROM customer WHERE status = 1";
          $stmt = $conn->prepare($sql);
          $stmt->execute();
        ?>
    <div class="checkout-section">
      <h2>Thanh toán</h2>
      <div style="display:flex; gap:10px; align-items:center;">
        <select name="fillerCustomer" id="fillerCustomer" style="flex:1;">
          <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . $row['customer_id'] . '">' . htmlspecialchars($row['customer_name']) . '</option>';
          }
            ?>
        </select>
        <button type="button" onclick="openQuickAddCustomer()" style="width:40px; height:40px; background:#007bff; color:white; border:none; border-radius:5px; font-size:20px; font-weight:bold; cursor:pointer; flex-shrink:0;" title="Thêm khách hàng nhanh">+</button>
      </div>
      <textarea class="note" name="order_note" placeholder="Ghi chú đơn hàng..."></textarea>

      <div class="checkout-actions">
         <button type="button" class="btn cash" value="cash" onclick="return showCashPopup()">Thanh toán tiền mặt</button>

        <!-- Popup -->
        <div id="CashPopup" class="popup-overlay">
          <div class="popup-box">
            <p>Bạn xác nhận thanh toán đơn hàng bằng <strong>tiền mặt</strong>?</p>
            <div class="popup-actions">
              <button type="submit" name="confirm_cash" value="1" class="btn cash">Có</button>
              <button type="button" class="btn kitchen" onclick="closeCashPopup()">Không</button>
            </div>
          </div>
        </div>


         <button type="button" class="btn transfer" value="transfer" onclick="showTransferPopup()">💳 Chuyển khoản</button>
        <div id="transferPopup" class="popup-overlay">
          <div class="popup-box">
            <p>Bạn xác nhận thanh toán đơn hàng bằng <strong>chuyển khoản</strong>?</p>
            <div class="popup-actions">
              <button type="submit" name="confirm_transfer" value="2" class="btn transfer">Có</button>
              <button type="button" class="btn kitchen" onclick="closeTransferPopup()">Không</button>
            </div>
          </div>
        </div>


        <button type="button" id = "btn_debt" value="debt" onclick="showDebtPopup()" class="btn debt">🧾 Ghi nợ</button>
        <div id="debtPopup" class="popup-overlay">
          <div class="popup-box">
            <p>Bạn xác nhận <strong>ghi nợ</strong> đơn hàng này?</p>
            <div class="popup-actions">
              <button type="submit" name="confirm_debt" value="3" class="btn debt">Có</button>
              <button type="button" class="btn kitchen" onclick="closeDebtPopup()">Không</button>
            </div>
          </div>
        </div>

        
        <button type="button" id = "btn_export" name="payment_method_export" onclick="showtExportPopup()" value="4" class="btn export">🍔 Xuất bếp</button>
        <div id="exportPopup" class="popup-overlay">
          <div class="popup-box">
            <p>Bạn xác nhận <strong>xuất bếp</strong> đơn hàng này?</p>
            <div class="popup-actions">
              <button type="submit" name="confirm_export" value="3" class="btn debt">Có</button>
              <button type="button" class="btn kitchen" onclick="closeExportPopup()">Không</button>
            </div>
          </div>
        </div>
        <!-- Nút huỷ -->
        <button type="button" class="btn cancel" onclick="showPopup()">Huỷ đơn hàng</button>

        <!-- Popup huỷ -->
        <div id="confirmPopup" class="popup-overlay">
          <div class="popup-box">
            <p>Bạn có chắc muốn huỷ đơn hàng không?</p>
            <div class="popup-actions">
              <button type="submit" name="cancel_order" value="1" class="btn cancel">Có</button>
              <button type="button" class="btn kitchen" onclick="closePopup()">Không</button>
            </div>
          </div>
        </div>

      </div>
    </div>

  </form> <!-- Đóng form ở đây -->
</div>

<!-- Quick Add Customer Modal -->
<div id="quickAddCustomerModal" class="popup-overlay" style="display:none; z-index:9999;">
  <div class="popup-box" style="padding:20px; text-align:left;">
    <h3 style="margin-top:0;">Thêm khách hàng nhanh</h3>
    <div style="margin-bottom:15px;">
      <label>Tên khách hàng: <span style="color:red;">*</span><br>
        <input type="text" id="quickCusName" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; margin-top:5px;">
      </label>
    </div>
    <div style="margin-bottom:20px;">
      <label>Số điện thoại:<br>
        <input type="text" id="quickCusPhone" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; margin-top:5px;">
      </label>
    </div>
    <div style="display:flex; gap:10px; justify-content:flex-end;">
      <button type="button" class="btn cancel" onclick="closeQuickAddCustomer()" style="padding:8px 16px; background:#f8f9fa; color:#333; border:1px solid #ccc;">Hủy</button>
      <button type="button" class="btn" onclick="saveQuickCustomer()" style="padding:8px 16px; background:#007bff; color:white; border:none;">Lưu khách</button>
    </div>
  </div>
</div>

<script src="js/pay.js"></script>
<script>
  function openQuickAddCustomer() {
    document.getElementById('quickCusName').value = '';
    document.getElementById('quickCusPhone').value = '';
    document.getElementById('quickAddCustomerModal').style.display = 'flex';
  }

  function closeQuickAddCustomer() {
    document.getElementById('quickAddCustomerModal').style.display = 'none';
  }

  function saveQuickCustomer() {
    const btn = event.currentTarget;
    const name = document.getElementById('quickCusName').value.trim();
    const phone = document.getElementById('quickCusPhone').value.trim();
    if (!name) {
      alert("Vui lòng nhập tên khách hàng");
      return;
    }

    btn.disabled = true;
    btn.innerText = "Đang lưu...";

    const formData = new FormData();
    formData.append('customer_name', name);
    formData.append('customer_phone', phone);
    formData.append('ajax', '1');

    fetch('add_customers.php', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(res => {
      btn.disabled = false;
      btn.innerText = "Lưu khách";
      if (res.success) {
        // Thêm option mới vào combobox và chọn nó
        const select = document.getElementById('fillerCustomer');
        const option = document.createElement('option');
        option.value = res.id;
        option.text = name;
        select.appendChild(option);
        select.value = res.id;
        
        closeQuickAddCustomer();
        if (typeof showToast === 'function') showToast("Thêm khách hàng thành công");
        else alert("Thêm thành công!");
      } else {
        alert("Lỗi: " + (res.message || "Không thể thêm khách hàng"));
      }
    })
    .catch(err => {
      console.error(err);
      alert("Lỗi kết nối");
    });
  }
</script>