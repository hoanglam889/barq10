function formatCurrency(value) {
  return value.toLocaleString('vi-VN') + ' đ';
}

// Cập nhật giá từng dòng
function updateLinePrice(item) {
  const price = parseInt(item.dataset.price);
  const input = item.querySelector('.quantity-input');
  let qty = parseInt(input.value) || 1;
  if (qty < 1) qty = 1;
  input.value = qty; // Đảm bảo value luôn đúng
  const lineTotal = price * qty;
  item.querySelector('.item-price').textContent = formatCurrency(lineTotal);
  updateCartTotal();
}

// Cập nhật tổng tiền
function updateCartTotal() {
  let total = 0;
  document.querySelectorAll('.cart-item').forEach(item => {
    const price = parseInt(item.dataset.price);
    const input = item.querySelector('.quantity-input');
    const qty = parseInt(input.value) || 1;
    total += price * qty;
  });
  const totalPrice = document.getElementById('total-price');
  if (totalPrice) totalPrice.textContent = formatCurrency(total);
}

// Gắn sự kiện tăng/giảm & nhập số lượng
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.cart-item').forEach(item => {
    const input = item.querySelector('.quantity-input');
    const btnDec = item.querySelector('.pay-btn-decrease');
    const btnInc = item.querySelector('.pay-btn-increase');
    const price = parseInt(item.dataset.price);

    if (btnDec) {
      btnDec.addEventListener('click', () => {
        let val = parseInt(input.value) || 1;
        if (val > 1) val--;
        input.value = val;
        updateLinePrice(item);
        console.log('Giảm:', input.value);
      });
    }

    if (btnInc) {
      btnInc.addEventListener('click', () => {
        let val = parseInt(input.value) || 1;
        val++;
        input.value = val;
        updateLinePrice(item);
        console.log('Tăng:', input.value);
      });
    }

    if (input) {
      input.addEventListener('input', () => {
        let val = parseInt(input.value) || 1;
        if (val < 1) val = 1;
        input.value = val;
        updateLinePrice(item);
        console.log('Nhập:', input.value);
      });
    }
  });
  updateCartTotal();
});

/* ====== POPUP ====== */
function showPopup() {
  document.getElementById('confirmPopup').style.display = 'flex';
}
function closePopup() {
  document.getElementById('confirmPopup').style.display = 'none';
}
function showCashPopup() {
  document.getElementById('CashPopup').style.display = 'flex';
}
function closeCashPopup() {
  document.getElementById('CashPopup').style.display = 'none';
}
function showTransferPopup() {
  document.getElementById('transferPopup').style.display = 'flex';
}
function closeTransferPopup() {
  document.getElementById('transferPopup').style.display = 'none';
}
function showDebtPopup() {
  document.getElementById('debtPopup').style.display = 'flex';
}
function closeDebtPopup() {
  document.getElementById('debtPopup').style.display = 'none';
}
function showtExportPopup() {
  document.getElementById('exportPopup').style.display = 'flex';
}
function closeExportPopup() {
  document.getElementById('exportPopup').style.display = 'none';
} 