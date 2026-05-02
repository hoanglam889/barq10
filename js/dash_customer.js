(function() {
  // 1. Các sự kiện trên document (Chỉ gắn 1 lần duy nhất)
  if (!window.hasDashCustomerLoaded) {
    document.addEventListener('click', function (e) {
      // ===== Mở modal thêm khách hàng =====
      if (e.target.id === 'addCustomer') {
        const addModal = document.getElementById('addModal');
        if (addModal) {
          const addForm = document.getElementById('addForm');
          if (addForm) addForm.reset();
          addModal.classList.remove('cus-hidden');
        }
      }
      // ===== Đóng modal (cả thêm và sửa) =====
      if (e.target.classList.contains('btn-cancel')) {
        const modal = e.target.closest('.modal-box')?.parentElement;
        if (modal) modal.classList.add('cus-hidden');
      }
    });

    document.addEventListener('input', function (e) {
      if (e.target.id === 'searchInput') {
        const keyword = e.target.value.toLowerCase();
        document.querySelectorAll('#customerContainer .customer-card').forEach(card => {
          const name = card.querySelector('.customer-name').textContent.toLowerCase();
          const phone = card.querySelector('.customer-phone').textContent.toLowerCase();
          card.style.display = (name.includes(keyword) || phone.includes(keyword)) ? '' : 'none';
        });
      }
    });

    document.addEventListener('submit', function (e) {
      if (e.target.id === 'addForm') {
        e.preventDefault();
        const formData = new FormData(e.target);
        fetch('add_customers.php', { method: 'POST', body: formData })
          .then(r => r.json())
          .then(res => {
            if (res.success) {
              document.getElementById('addModal').classList.add('cus-hidden');
              if (typeof loadCustomer === 'function') loadCustomer();
              else window.location.href = 'index.php?tab=customers';
            } else alert(res.message || 'Thêm khách hàng thất bại!');
          })
          .catch(err => alert('Lỗi: ' + err));
      }

      if (e.target.id === 'editForm') {
        e.preventDefault();
        const data = new FormData(e.target);
        fetch('update_customers.php', { method: 'POST', body: data })
          .then(r => r.json())
          .then(res => {
            if (res.success) {
              document.getElementById('editModal').classList.add('cus-hidden');
              if (typeof loadCustomer === 'function') loadCustomer();
              else window.location.href = 'index.php?tab=customers';
            } else alert(res.message || 'Cập nhật thất bại!');
          })
          .catch(err => alert('Lỗi: ' + err));
      }
    });

    window.hasDashCustomerLoaded = true;
  }

  // 2. Các hàm và khởi tạo (Chạy lại mỗi lần load tab)
  window.initCustomerPage = function() {
    const cards = document.querySelectorAll('#customerContainer .customer-card');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    let visible = window.initial_show || 12;

    if (cards.length > visible && loadMoreBtn) {
      loadMoreBtn.classList.remove('cus-hidden');
      loadMoreBtn.onclick = function() {
        visible += 10;
        cards.forEach((card, idx) => {
          if (idx < visible) card.classList.remove('cus-hidden');
        });
        if (visible >= cards.length) loadMoreBtn.classList.add('cus-hidden');
      };
    }
  }
  initCustomerPage();

  window.editCustomer = function(id) {
    fetch('get_customer.php?id=' + encodeURIComponent(id))
      .then(r => r.json())
      .then(data => {
        if (!data) return alert('Không tìm thấy khách hàng');
        document.getElementById('editCustomerId').value = data.customer_id;
        document.getElementById('editCustomerName').value = data.customer_name;
        document.getElementById('editCustomerPhone').value = data.customer_phone;
        document.getElementById('editModal').classList.remove('cus-hidden');
      })
      .catch(err => alert('Lỗi tải dữ liệu: ' + err.message));
  };

  window.toggleStatus = function(type, id, isChecked) {
    const status = isChecked ? 1 : 0;
    const formData = new FormData();
    formData.append('type', type);
    formData.append('id', id);
    formData.append('status', status);

    fetch('toggle_status.php', { method: 'POST', body: formData })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          const toggleEl = document.querySelector(`input[onchange="toggleStatus('${type}', ${id}, this.checked)"]`);
          if (toggleEl) {
            const itemCard = toggleEl.closest('.customer-card, .order-row');
            if (itemCard) {
              if (status === 0) itemCard.classList.add('inactive-item');
              else itemCard.classList.remove('inactive-item');
            }
          }
        } else {
          alert(res.message || 'Lỗi cập nhật trạng thái');
          event.target.checked = !isChecked;
        }
      })
      .catch(err => {
        alert('Lỗi: ' + err);
        event.target.checked = !isChecked;
      });
  };

  window.deleteCustomer = function(id){
    if(confirm('Bạn có chắc muốn xóa khách hàng này?')){
      fetch('delete_customer.php?id=' + encodeURIComponent(id))
        .then(r => r.text())
        .then(res => {
          if(res.trim() === 'success') {
            if (typeof loadCustomer === 'function') loadCustomer();
            else window.location.href = 'index.php?tab=customers';
          } else {
            alert('Xóa thất bại!');
          }
        })
        .catch(err => alert('Lỗi: '+err));
    }
  }

})();
