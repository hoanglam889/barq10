<style>
html, body {
  overflow-x: hidden; /* Chặn tràn ngang toàn trang */
}

.checkout-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  max-width: 100vw; /* CHẶN TRÀN */
  overflow-x: hidden; /* CHẶN TRÀN */
  background-color: #ffffff;
  padding: 12px 16px;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: flex-end;
  flex-wrap: wrap; /* ✅ Co lại khi thiếu chỗ */
  gap: 8px; /* ✅ Thay vì margin-left từng nút */
  z-index: 999;
}

.checkout-bar button {
  padding: 10px 16px;
  font-size: 16px;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  flex-shrink: 1; /* ✅ Cho phép co lại trên mobile */
  min-width: 100px; /* ✅ Không quá nhỏ */
}

.btn-cash {
  background-color: #4a90e2;
}

.btn-cancel {
  background-color: grey;
}
</style>
<div class="checkout-bar">
  <button class="btn-cash">Thanh toán</button>
</div>


