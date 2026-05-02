(function() {
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.oninput = function() {
      const keyword = searchInput.value.toLowerCase();
      document.querySelectorAll('#productBody tr').forEach(row => {
        const name = row.querySelector('td:first-child').textContent.toLowerCase();
        row.style.display = name.includes(keyword) ? '' : 'none';
      });
    };
  }

  const rows = document.querySelectorAll('.order-row');
  const loadMoreBtn = document.getElementById('loadMoreBtn');
  let visible = window.initial_show || 12;
  if (rows.length > visible && loadMoreBtn) {
    loadMoreBtn.classList.remove('hidden');
    loadMoreBtn.onclick = function() {
      visible += 10;
      rows.forEach((row, idx) => {
        if (idx < visible) row.classList.remove('hidden-order');
      });
      if (visible >= rows.length) loadMoreBtn.classList.add('hidden');
    };
  }

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

  window.editProduct = function(id) {
    fetch('get_product.php?id=' + encodeURIComponent(id))
      .then(r => r.json())
      .then(data => {
        if (!data) return alert('Không tìm thấy sản phẩm');
        document.getElementById('editDrinkId').value   = data.drink_id;
        document.getElementById('editDrinkName').value = data.drink_name;
        document.getElementById('editDrinkCost').value = data.drink_cost ?? '';
        document.getElementById('editDrinkPrice').value= data.drink_price ?? '';
        document.getElementById('editDrinkImg').value = '';
        const preview = document.getElementById('imgPreview');
        if (preview) preview.src = data.drink_img || 'default.jpg';
        document.getElementById('editModal').classList.remove('hidden');
      })
      .catch(err => {
        console.error(err);
        alert('Lỗi tải dữ liệu: ' + err.message);
      });
  };

  window.deleteProduct = function(id) {
    if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
      window.location.href = 'delete_product.php?id=' + encodeURIComponent(id);
    }
  };

  window.closeEditModal = function() {
    document.getElementById('editModal')?.classList.add('hidden');
  };

  const btnAddProduct = document.getElementById('addProduct');
  if (btnAddProduct) {
    btnAddProduct.onclick = function() {
      document.getElementById('addForm').reset();
      document.getElementById('addImgPreview').src = '';
      document.getElementById('addModal').classList.remove('hidden');
    };
  }

  const addDrinkImg = document.getElementById('addDrinkImg');
  if (addDrinkImg) {
    addDrinkImg.onchange = function() {
      const file = this.files[0];
      if (file) document.getElementById('addImgPreview').src = URL.createObjectURL(file);
      else document.getElementById('addImgPreview').src = '';
    };
  }

  const addForm = document.getElementById('addForm');
  if (addForm) {
    addForm.onsubmit = function() {
      document.getElementById('addModal')?.classList.add('hidden');
    };
  }

})();
