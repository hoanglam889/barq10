<?php
include 'include/config.php';

$customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$customerName = '';
$orders = [];

if ($customerId > 0) {
    $stc = $conn->prepare("SELECT customer_name FROM customer WHERE customer_id = ?");
    $stc->execute([$customerId]);
    $customerName = (string)($stc->fetchColumn() ?: '');

    $sql = "SELECT o.order_id, o.order_time, o.total_amount
            FROM orders o
            WHERE o.customer_id = ? AND o.order_payby = '3'
            ORDER BY o.order_time DESC";
    $st = $conn->prepare($sql);
    $st->execute([$customerId]);
    while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
        $orders[] = [
            'order_id'    => (int)$r['order_id'],
            'order_time'  => (string)$r['order_time'],
            'amount'      => (float)$r['total_amount'],
        ];
    }
}

$total = 0;
foreach ($orders as $o) { $total += (float)$o['amount']; }

function format_vnd_simple($n) {
    return number_format((float)$n, 0, ',', '.') . ' ₫';
}
?>

<style>
  #debtList table {
    border-radius: 8px;
    overflow: hidden;
  }
  #debtList thead th {
    background-color: #f8f9fa;
    font-weight: 600;
  }
  #debtList tbody tr:hover {
    background-color: #f1f1f1;
  }
  #totalDebt {
    font-weight: 600;
    color: #d63384;
  }
</style>

<div class="p-3" data-customer-id="<?php echo (int)$customerId; ?>">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <h5 class="mb-2 mb-sm-0">💳 Đơn còn nợ</h5>
    <div id="customerName" class="text-muted small">
      <?php echo htmlspecialchars($customerName ?: 'Không xác định'); ?>
    </div>
  </div>

  <div id="debtList">
    <?php if (empty($orders)) : ?>
      <div class="alert alert-info mb-0">Khách hàng không có đơn nợ nào.</div>
    <?php else: ?>
      <div class="table-responsive shadow-sm">
        <table class="table table-sm align-middle mb-0">
          <thead>
            <tr>
              <th style="width:40px;"><input type="checkbox" id="chkAll"></th></th>
              <th>Đơn hàng</th>
              <th>Ngày tạo</th>
              <th class="text-end">Số tiền</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
              <tr data-order-id="<?php echo (int)$o['order_id']; ?>">
                <td><input type="checkbox" class="chkOne" data-amount="<?php echo (float)$o['amount']; ?>"></td>
                <td class="fw-semibold">#<?php echo (int)$o['order_id']; ?></td>
                <td><?php echo htmlspecialchars($o['order_time']); ?></td>
                <td class="text-end"><?php echo format_vnd_simple($o['amount']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($orders)) : ?>
  <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3 flex-wrap gap-2">
    <div><strong>Tổng đã chọn:</strong> <span id="totalDebt">0 ₫</span></div>
    <div class="d-flex gap-2">
      <button id="btnPayCash" class="btn btn-success btn-sm px-3">
        <i class="bi bi-cash-coin me-1"></i> Tiền mặt
      </button>
      <button id="btnPayBank" class="btn btn-primary btn-sm px-3">
        <i class="bi bi-bank me-1"></i> Chuyển khoản
      </button>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- Logic moved to dash_order.js -->
