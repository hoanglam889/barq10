(function() {
    // 1. Các sự kiện trên document (Chỉ gắn 1 lần duy nhất)
    if (!window.hasDashOrderLoaded) {
        document.addEventListener('click', function(e) {
            const trash = e.target.closest('.fa-trash');
            if (trash) {
                const row = trash.closest('.order-row');
                const id = row.dataset.id;
                if (confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')) {
                    fetch('delete_order.php?id=' + id)
                        .then(r => r.text())
                        .then(text => {
                            if (text.trim() === 'success') {
                                if (typeof loadOrder === 'function') loadOrder();
                                else location.reload();
                            } else alert('Lỗi: ' + text);
                        })
                        .catch(err => alert('Lỗi kết nối: ' + err.message));
                }
            }
        });
        window.hasDashOrderLoaded = true;
    }

    // 2. Hàm khởi tạo (Chạy lại mỗi lần load tab)
    window.bindOrderRowClick = function() {
        const orderBody = document.getElementById("orderBody");
        const loadMoreBtn = document.getElementById("loadMoreBtn");
        const searchInput = document.getElementById("searchInput");
        const filterQuick = document.getElementById("filterQuick");
        if (!orderBody || !loadMoreBtn || !searchInput || !filterQuick) return;

        const STEP = 12;

        function resetOrders() {
            const rows = Array.from(document.querySelectorAll(".order-row"));
            const details = Array.from(document.querySelectorAll(".order-details"));
            rows.forEach(r => (r.style.display = "none"));
            details.forEach(d => (d.style.display = "none"));
            let shown = 0;
            for (let i = 0; i < rows.length; i++) {
                if (rows[i].dataset.show === "1") {
                    if (shown < STEP) rows[i].style.display = "table-row";
                    shown++;
                }
            }
            toggleLoadMore(shown);
        }

        function revealNext(n) {
            const rows = Array.from(document.querySelectorAll(".order-row"));
            for (let i = 0; i < rows.length && n > 0; i++) {
                if (rows[i].dataset.show === "1" && rows[i].style.display === "none") {
                    rows[i].style.display = "table-row";
                    n--;
                }
            }
            toggleLoadMore(countVisible());
        }

        function countVisible() {
            return Array.from(document.querySelectorAll(".order-row")).filter(r => r.dataset.show === "1").length;
        }

        function toggleLoadMore(total) {
            const visibleNow = Array.from(document.querySelectorAll(".order-row")).filter(r => r.dataset.show === "1" && r.style.display !== "none").length;
            loadMoreBtn.style.display = total > visibleNow ? "inline-block" : "none";
        }

        orderBody.onclick = function(e) {
            const row = e.target.closest(".order-row");
            if (row && !e.target.closest(".fa-edit") && !e.target.closest(".fa-trash")) {
                const next = row.nextElementSibling;
                if (next && next.classList.contains("order-details")) {
                    next.style.display = next.style.display === "table-row" ? "none" : "table-row";
                }
            }
        };

        loadMoreBtn.onclick = function() { revealNext(STEP); };

        const filterFrom = document.getElementById("filterFrom");
        const filterTo = document.getElementById("filterTo");
        const btnApplyDateFilter = document.getElementById("btnApplyDateFilter");
        const btnResetFilter = document.getElementById("btnResetFilter");

        if (sessionStorage.getItem('order_search') !== null) searchInput.value = sessionStorage.getItem('order_search');
        if (sessionStorage.getItem('order_filter_quick') !== null) filterQuick.value = sessionStorage.getItem('order_filter_quick') || 'today';
        if (sessionStorage.getItem('order_filter_from')) filterFrom.value = sessionStorage.getItem('order_filter_from');
        if (sessionStorage.getItem('order_filter_to')) filterTo.value = sessionStorage.getItem('order_filter_to');

        function applyFilters() {
            const q = searchInput.value.toLowerCase().trim();
            const fQuick = filterQuick.value;
            const fFrom = filterFrom.value;
            const fTo = filterTo.value;

            sessionStorage.setItem('order_search', q);
            sessionStorage.setItem('order_filter_quick', fQuick);
            sessionStorage.setItem('order_filter_from', fFrom);
            sessionStorage.setItem('order_filter_to', fTo);

            const now = new Date();
            const todayStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
            
            const rows = Array.from(document.querySelectorAll(".order-row"));
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const matchesSearch = !q || text.includes(q);
                let matchesDate = true;
                const orderDateFull = row.querySelector(".order-date").innerText.trim();
                const dateStr = orderDateFull.split(' ')[0]; 
                const orderDate = new Date(dateStr);
                orderDate.setHours(0,0,0,0);

                if (fQuick === 'today') {
                    matchesDate = (dateStr === todayStr);
                } else if (fQuick === 'yesterday') {
                    const yest = new Date();
                    yest.setDate(yest.getDate() - 1);
                    const yestStr = yest.getFullYear() + '-' + String(yest.getMonth() + 1).padStart(2, '0') + '-' + String(yest.getDate()).padStart(2, '0');
                    matchesDate = (dateStr === yestStr);
                } else if (fQuick === 'week') {
                    const limit = new Date();
                    limit.setDate(limit.getDate() - 7);
                    limit.setHours(0,0,0,0);
                    matchesDate = (orderDate >= limit);
                } else if (fQuick === 'month') {
                    const now = new Date();
                    matchesDate = (orderDate.getMonth() === now.getMonth() && orderDate.getFullYear() === now.getFullYear());
                } else if (fQuick === 'custom') {
                    if (fFrom && fTo) {
                        const dFrom = new Date(fFrom); dFrom.setHours(0,0,0,0);
                        const dTo = new Date(fTo); dTo.setHours(23,59,59,999);
                        matchesDate = (orderDate >= dFrom && orderDate <= dTo);
                    }
                }
                row.dataset.show = (matchesSearch && matchesDate) ? "1" : "0";
            });
            resetOrders();
        }

        searchInput.oninput = applyFilters;
        filterQuick.onchange = function() {
            if (this.value !== 'custom') {
                filterFrom.value = '';
                filterTo.value = '';
            }
            applyFilters();
        };
        if (btnApplyDateFilter) btnApplyDateFilter.onclick = function() {
            filterQuick.value = 'custom';
            applyFilters();
        };
        if (btnResetFilter) btnResetFilter.onclick = function() {
            searchInput.value = '';
            filterQuick.value = 'today';
            filterFrom.value = '';
            filterTo.value = '';
            applyFilters();
        };

        applyFilters();
    };

    // Chạy luôn lần đầu
    bindOrderRowClick();

    // 3. Các hàm Global cho đơn hàng
    window.editOrder = function(id) {
        fetch('get_order.php?id=' + id)
            .then(r => r.json())
            .then(data => {
                if (!data) return alert('Không lấy được thông tin đơn hàng');
                document.getElementById('editOrderId').value = data.order_id;
                document.getElementById('editOrderCustomer').value = data.customer_id;
                document.getElementById('editOrderPayby').value = data.order_payby;
                document.getElementById('editOrderNote').value = data.Order_note || '';

                let html = '';
                if (data.details && data.details.length > 0) {
                    data.details.forEach(item => {
                        html += `<div style="margin-bottom:10px; border-bottom:1px dashed #eee; padding-bottom:5px;">
                            <strong>${item.drink_name}</strong><br>
                            Số lượng: <input type="number" name="quantity[${item.drink_id}]" value="${item.order_detail_quantity}" style="width:60px;">
                            Giá: <input type="number" name="items_price[${item.drink_id}]" value="${item.order_detail_price}" readonly style="width:100px; border:none; background:transparent;">
                        </div>`;
                    });
                }
                document.getElementById('editOrderContent').innerHTML = html;
                document.getElementById('editOrderModal').classList.remove('hidden');
            })
            .catch(err => alert('Lỗi: ' + err.message));
    };

    window.closeEditOrderModal = function() {
        document.getElementById('editOrderModal').classList.add('hidden');
    };

    window.saveOrderChanges = function() {
        const form = document.getElementById('editOrderForm');
        const formData = new FormData(form);
        formData.append('ajax', '1');
        fetch('update_order.php', { method: 'POST', body: formData })
            .then(r => r.text())
            .then(text => {
                if (text.trim() === 'success') {
                    window.closeEditOrderModal();
                    if (typeof loadOrder === 'function') loadOrder();
                    else location.reload();
                } else alert('Lỗi: ' + text);
            })
            .catch(err => alert('Lỗi kết nối: ' + err.message));
    };

    // 4. Các hàm Global cho Khách nợ (Dùng cho modal_loc)
    window.openCusNo = function (customerId) {
        if (!customerId) return;
        const modalEl = document.getElementById('modal_loc');
        if (!modalEl) return;
        const bodyEl = modalEl.querySelector('.modal-body');
        if (!bodyEl) return;

        // Lưu lại danh sách tổng quan ban đầu trước khi ghi đè chi tiết nợ của 1 người
        if (!modalEl.dataset.hasOriginalHtml) {
            // Lưu nội dung HTML ban đầu
            modalEl.dataset.originalHtml = bodyEl.innerHTML;
            modalEl.dataset.hasOriginalHtml = "true";
            
            // Khi popup đóng lại, tự động phục hồi lại danh sách tổng quan
            modalEl.addEventListener('hidden.bs.modal', function () {
                bodyEl.innerHTML = modalEl.dataset.originalHtml;
            });
        }

        bodyEl.innerHTML = '<div class="d-flex justify-content-center p-5"><div class="spinner-border text-primary" role="status"></div></div>';
        fetch('cus_no.php?id=' + encodeURIComponent(customerId))
          .then(res => res.ok ? res.text() : Promise.reject('Lỗi tải trang'))
          .then(html => {
            bodyEl.innerHTML = html;
            initDebtModalEvents(bodyEl);
          })
          .catch(err => {
            bodyEl.innerHTML = `<div class="alert alert-danger">${err}</div>`;
          });
    };

    function initDebtModalEvents(container) {
        const chkAll = container.querySelector('#chkAll');
        const chkOnes = container.querySelectorAll('.chkOne');
        const totalEl = container.querySelector('#totalDebt');
        const btnPayCash = container.querySelector('#btnPayCash');
        const btnPayBank = container.querySelector('#btnPayBank');

        function updateTotal() {
            let total = 0;
            container.querySelectorAll('.chkOne:checked').forEach(chk => {
                total += parseFloat(chk.dataset.amount);
            });
            if (totalEl) totalEl.innerText = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
        }

        if (chkAll) {
            chkAll.onchange = function() {
                chkOnes.forEach(c => c.checked = chkAll.checked);
                updateTotal();
            };
        }
        chkOnes.forEach(c => {
            c.onchange = updateTotal;
        });

        const handlePay = (method) => {
            const selected = Array.from(container.querySelectorAll('.chkOne:checked')).map(c => c.closest('tr').dataset.orderId);
            if (selected.length === 0) return alert('Vui lòng chọn đơn hàng');
            if (!confirm('Xác nhận thanh toán các đơn đã chọn?')) return;

            const customerId = container.querySelector('[data-customer-id]').dataset.customerId;

            // Chạy từng request cập nhật đơn hàng
            const tasks = selected.map(id => {
                const fd = new FormData();
                fd.append('order_id', id);
                fd.append('payment_method', method);
                fd.append('customer_id', customerId);
                fd.append('ajax', '1');
                return fetch('update_order.php', { method: 'POST', body: fd }).then(r => r.text());
            });

            Promise.all(tasks).then(results => {
                const allOk = results.every(t => t.trim() === 'success');
                if (allOk) {
                    // Đóng modal nợ
                    const modalEl = document.getElementById('modal_loc');
                    if (modalEl && window.bootstrap && bootstrap.Modal) {
                        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modalInstance.hide();
                    }

                    window.openCusNo(customerId); // Load lại danh sách nợ (nếu còn đơn khác)
                    if (typeof loadOrder === 'function') loadOrder();
                } else {
                    alert('Một số đơn cập nhật thất bại, vui lòng kiểm tra lại.');
                }
            }).catch(err => alert('Lỗi kết nối: ' + err.message));
        };

        if (btnPayCash) btnPayCash.onclick = () => handlePay(1);
        if (btnPayBank) btnPayBank.onclick = () => handlePay(2);
    }

    // 5. Khách hàng nhanh
    window.openQuickAddCustomerOrder = function() {
        document.getElementById('quickCusNameOrder').value = '';
        document.getElementById('quickCusPhoneOrder').value = '';
        document.getElementById('quickAddCustomerOrderModal').style.display = 'flex';
    };
    window.closeQuickAddCustomerOrder = function() {
        document.getElementById('quickAddCustomerOrderModal').style.display = 'none';
    };
    window.saveQuickCustomerOrder = function() {
        const name = document.getElementById('quickCusNameOrder').value.trim();
        const phone = document.getElementById('quickCusPhoneOrder').value.trim();
        if (!name) { alert("Vui lòng nhập tên khách hàng"); return; }
        const formData = new FormData();
        formData.append('customer_name', name);
        formData.append('customer_phone', phone);
        formData.append('ajax', '1');
        fetch('add_customers.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                const select = document.getElementById('editOrderCustomer');
                if (select) {
                    const option = document.createElement('option');
                    option.value = res.id; option.text = name;
                    select.appendChild(option); select.value = res.id;
                }
                window.closeQuickAddCustomerOrder();
            } else alert("Lỗi: " + (res.message || "Không thể thêm"));
        })
        .catch(err => alert("Lỗi kết nối"));
    };

})();
