(function() {
  const searchInput = document.getElementById('searchInput');
  const userContainer = document.getElementById('userContainer');
  const loadMoreBtn = document.getElementById('loadMoreBtn');
  const STEP = 12;

  function filterUsers() {
    if (!searchInput || !userContainer) return;
    const q = searchInput.value.toLowerCase().trim();
    const rows = Array.from(userContainer.getElementsByClassName('user-row'));
    let shown = 0;

    rows.forEach(row => {
      row.classList.add('cus-hidden');
      const text = row.innerText.toLowerCase();
      if (!q || text.includes(q)) {
        if (!q) {
          if (shown < STEP) row.classList.remove('cus-hidden');
        } else {
          row.classList.remove('cus-hidden');
        }
        shown++;
      }
    });

    if (loadMoreBtn) {
      if (!q && shown > STEP) loadMoreBtn.classList.remove('cus-hidden');
      else loadMoreBtn.classList.add('cus-hidden');
    }
  }

  // Khởi tạo (Chạy mỗi lần load tab)
  if (searchInput) searchInput.oninput = filterUsers;
  if (loadMoreBtn) {
    loadMoreBtn.onclick = function() {
      const q = searchInput.value.toLowerCase().trim();
      if (q) return; 
      const rows = Array.from(userContainer.getElementsByClassName('user-row'));
      let currentlyShown = rows.filter(r => !r.classList.contains('cus-hidden')).length;
      let count = 0;
      for (let i = currentlyShown; i < rows.length; i++) {
        rows[i].classList.remove('cus-hidden');
        count++;
        if (count >= STEP) break;
      }
      currentlyShown += count;
      if (currentlyShown >= rows.length) loadMoreBtn.classList.add('cus-hidden');
    };
  }

  const initialRows = userContainer ? userContainer.getElementsByClassName('user-row').length : 0;
  if (initialRows > STEP && loadMoreBtn) loadMoreBtn.classList.remove('cus-hidden');

  // Sự kiện document (Chỉ gắn 1 lần)
  if (!window.hasDashUsersLoaded) {
    document.addEventListener('submit', function(e) {
      if (e.target.id === 'userForm') {
        e.preventDefault();
        const formData = new FormData(e.target);
        fetch('save_user.php', { method: 'POST', body: formData })
          .then(r => r.text())
          .then(text => {
            if (text.trim() === 'success') {
              window.closeUserModal();
              if (typeof loadUsers === 'function') loadUsers();
              else location.reload();
            } else alert('Lỗi: ' + text);
          })
          .catch(err => alert('Lỗi hệ thống: ' + err.message));
      }
    });
    window.hasDashUsersLoaded = true;
  }

  // Global functions
  window.closeUserModal = function() {
    document.getElementById('userModal')?.classList.add('cus-hidden');
  };

  const btnAdd = document.getElementById('addUser');
  if (btnAdd) {
    btnAdd.onclick = function() {
      document.getElementById('userModalTitle').innerText = 'Thêm tài khoản';
      document.getElementById('userId').value = '';
      document.getElementById('userName').value = '';
      document.getElementById('userPassword').value = '';
      document.getElementById('userPassword').required = true;
      document.getElementById('passwordHint').style.display = 'block';
      document.getElementById('userProfile').value = '';
      document.getElementById('userRole').value = 'user';
      document.getElementById('userModal').classList.remove('cus-hidden');
    };
  }

  window.editUser = function(id) {
    fetch('get_user.php?id=' + encodeURIComponent(id))
      .then(r => r.json())
      .then(data => {
        if (!data) return alert('Không tìm thấy tài khoản');
        document.getElementById('userModalTitle').innerText = 'Sửa tài khoản';
        document.getElementById('userId').value = data.user_id;
        document.getElementById('userName').value = data.user_name;
        document.getElementById('userPassword').value = '';
        document.getElementById('userPassword').required = false;
        document.getElementById('passwordHint').style.display = 'none';
        document.getElementById('userProfile').value = data.user_profile;
        document.getElementById('userRole').value = data.user_role;
        document.getElementById('userModal').classList.remove('cus-hidden');
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
            const itemCard = toggleEl.closest('.user-row, .customer-card');
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
})();
