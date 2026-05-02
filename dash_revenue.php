<?php
// dash_revenue.php
include 'include/config.php';
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

// lấy ngày từ GET (AJAX gọi ?from=YYYY-MM-DD&to=YYYY-MM-DD) hoặc mặc định today
$from = $_GET['from'] ?? ($_POST['from'] ?? date('Y-m-d'));
$to   = $_GET['to'] ?? ($_POST['to'] ?? date('Y-m-d'));

// đảm bảo to >= from
if ($to < $from) $to = $from;

// --- Query doanh thu ---
$sql = "
    SELECT 
        SUM(CASE WHEN order_payby = '1' THEN total_amount ELSE 0 END) AS cash,
        SUM(CASE WHEN order_payby = '2' THEN total_amount ELSE 0 END) AS transfer,
        SUM(CASE WHEN order_payby = '3' THEN total_amount ELSE 0 END) AS debt,
        SUM(CASE WHEN order_payby = '4' THEN total_amount ELSE 0 END) AS kitchen,
        SUM(total_amount) AS total,
        COUNT(*) AS total_orders
    FROM orders
    WHERE DATE(order_time) BETWEEN ? AND ?
";
$stmt = $conn->prepare($sql);
$stmt->execute([$from, $to]);
$revenue = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// --- Query số lượng món bán ---
$sqlItems = "
    SELECT p.drink_name AS name, 
           SUM(od.order_detail_quantity) AS qty,
           SUM(od.order_detail_quantity * COALESCE(od.order_detail_price,0)) AS amount
    FROM order_details od
    INNER JOIN soft_drink p ON od.drink_id = p.drink_id
    INNER JOIN orders o ON od.order_id = o.order_id
    WHERE DATE(o.order_time) BETWEEN ? AND ?
    GROUP BY p.drink_id, p.drink_name
    ORDER BY qty DESC
";
$stmt2 = $conn->prepare($sqlItems);
$stmt2->execute([$from, $to]);
$items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// helper format
function _fmt($n){
    if (function_exists('format_vnd')) return format_vnd($n);
    return number_format($n ?? 0, 0, ',', '.') . 'đ';
}
?>
<style>
@media (max-width: 576px) {
  .card .h5, .card .h4 { font-size: 1rem; }
  .card .small { font-size: 0.75rem; }
  h5 { font-size: 1rem; }
}
@media (max-width: 768px) {
  table.table td, table.table th { padding: 0.4rem 0.5rem; }
}
</style>

<div id="revenue-root" class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <h3 class="m-0">Báo cáo doanh thu</h3>
    <div class="d-flex gap-2 mt-2 mt-md-0">
      <input type="date" id="filter-from" class="form-control form-control-sm" value="<?php echo htmlspecialchars($from); ?>" max="<?php echo date('Y-m-d'); ?>">
      <input type="date" id="filter-to" class="form-control form-control-sm" value="<?php echo htmlspecialchars($to); ?>" max="<?php echo date('Y-m-d'); ?>">
      <button class="btn btn-sm btn-primary" id="filter-apply">Lọc</button>
    </div>
  </div>

  <!-- Doanh thu -->
  <div class="row g-2 g-md-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center p-2 p-md-3">
          <div class="small text-muted">Tiền mặt</div>
          <div class="h5 h-md4 fw-bold"><?php echo _fmt($revenue['cash'] ?? 0); ?></div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center p-2 p-md-3">
          <div class="small text-muted">Chuyển khoản</div>
          <div class="h5 fw-bold text-success"><?php echo _fmt($revenue['transfer'] ?? 0); ?></div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center p-2 p-md-3">
          <div class="small text-muted">Ghi nợ</div>
          <div class="h5 fw-bold text-info"><?php echo _fmt($revenue['debt'] ?? 0); ?></div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center p-2 p-md-3">
          <div class="small text-muted">Xuất bếp</div>
          <div class="h5 fw-bold text-warning"><?php echo _fmt($revenue['kitchen'] ?? 0); ?></div>
        </div>
      </div>
    </div>

    <div class="col-12 mt-2">
      <div class="card shadow-sm">
        <div class="card-body text-center p-2 p-md-3">
          <div class="small text-muted">Tổng doanh thu (<?php echo htmlspecialchars($from).' → '.htmlspecialchars($to); ?>)</div>
          <div class="h4 fw-bold mb-0"><?php echo _fmt($revenue['total'] ?? 0); ?></div>
          <div class="text-muted small">Tổng đơn: <?php echo intval($revenue['total_orders'] ?? 0); ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Số lượng món bán -->
  <h5 class="mb-3">Số lượng món bán được</h5>
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:60px">#</th>
              <th>Món</th>
              <th style="width:120px" class="text-end">Số lượng</th>
              <th style="width:150px" class="text-end">Doanh thu</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($items)): foreach ($items as $i => $it): ?>
              <tr>
                <td><?php echo $i+1; ?></td>
                <td><?php echo htmlspecialchars($it['name']); ?></td>
                <td class="text-end"><?php echo intval($it['qty']); ?></td>
                <td class="text-end"><?php echo _fmt($it['amount'] ?? 0); ?></td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="4" class="text-center text-muted">Không có dữ liệu</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
