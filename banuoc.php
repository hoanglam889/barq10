<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include_once('include/config.php');
if (!function_exists('format_vnd')) {
    function format_vnd($value) {
        return number_format($value, 0, ',', '.') . ' đ';
    }
}
$sql = "SELECT * FROM soft_drink WHERE status = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
?>
<style>
/* Giữ nguyên CSS bạn gửi */
* { box-sizing:border-box; }
body { margin:0; font-family:'Segoe UI',sans-serif; background:#f5f9ff; padding-bottom:80px; overflow-x:hidden; }
#content { display:flex; flex-wrap:wrap; gap:20px; padding:20px; justify-content: center;}
.water-item { background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); width:260px; overflow:hidden; transition:0.3s; }
.water-item:hover { transform:translateY(-4px); }
.water-image { width:100%; height:140px; object-fit:cover; background:#d0e7ff; }
.water-content { padding:16px; }
.water-name { font-size:18px; font-weight:600; color:#2c3e50; margin-bottom:8px; }
.item-price { font-size:16px; font-weight:500; margin-bottom:12px; }
.numeric-control { display:flex; align-items:center; gap:10px; }
.numeric-control button { width:32px; height:32px; font-size:18px; background:#4a90e2; border:none; color:#fff; border-radius:6px; cursor:pointer; transition:0.2s; }
.numeric-control button:hover { background:#357abd; }
.numeric-control input { width:50px; text-align:center; font-size:16px; padding:4px; border:1px solid #ccc; border-radius:6px; }
@media (max-width:768px) {
  #content { flex-direction:column; align-items:center; padding:10px; }
  .water-item { width:90vw; max-width:300px; }
}
#content { display: flex; flex-wrap: wrap; gap: 16px; padding: 16px; justify-content: center; }
@media (max-width:600px){
   body { margin: 0; padding-top: 48px; background: #f5f9ff; }
   #content { padding: 0 0 8px 0; gap: 12px; justify-content: center; }
   .water-item {
    display: flex; align-items: center; gap: 12px;
    width: calc(100% - 24px); max-width: 380px;
    padding: 8px 30px; margin: 0 12px;
    border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    background: #fff;
   }
   .water-image { width: 80px; height: 80px; flex-shrink: 0; object-fit: cover; border-radius: 8px; }
   .water-content { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 3px; }
   .water-name, .item-price { margin: 0; font-size: 14px; }
   .numeric-control { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
}
</style>

<form action="add_to_cart.php" method="post">
<div id="content">
<?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
  <div class="water-item" data-price="<?= $row['drink_price'] ?>">
    <img src="<?= $row['drink_img'] ?>" alt="<?= $row['drink_name'] ?>" class="water-image">
    <div class="water-content">
      <div class="water-name"><?= $row['drink_name'] ?></div>
      <div class="item-price"><?= format_vnd($row['drink_price']) ?></div>
      <div class="numeric-control">
        <button type="button" class="btn-decrease">−</button>
        <input type="number" name="drink_quantity[<?= $row['drink_id'] ?>]" value="0" min="0" class="quantity-input">
        <button type="button" class="btn-increase">+</button>
      </div>
    </div>
  </div>
<?php } ?>
</div>
<div>
  <?php include 'checkout_bar.php'; ?>
</div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.numeric-control').forEach(control => {
    const btnMinus = control.querySelector('.btn-decrease');
    const btnPlus = control.querySelector('.btn-increase');
    const input = control.querySelector('.quantity-input');
    const parent = control.closest('.water-item');
    const priceEl = parent.querySelector('.item-price');
    const priceValue = parseInt(parent.dataset.price);

    const updatePrice = () => {
      let qty = parseInt(input.value) || 0;
      if(qty<0) qty=0;
      input.value = qty;
      priceEl.textContent = new Intl.NumberFormat('vi-VN').format(priceValue * qty) + ' đ';
    };

    btnMinus.addEventListener('click', ()=>{ input.value = Math.max(0,(parseInt(input.value)||0)-1); updatePrice(); });
    btnPlus.addEventListener('click', ()=>{ input.value = (parseInt(input.value)||0)+1; updatePrice(); });
    input.addEventListener('input', updatePrice);
  });
});
</script>
</body>
</html>
