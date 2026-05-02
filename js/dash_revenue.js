(function() {
    function initRevenuePage() {
        const root = document.getElementById('revenue-root');
        if (!root) return;

        const fromInput = root.querySelector('#filter-from');
        const toInput = root.querySelector('#filter-to');
        const applyBtn = root.querySelector('#filter-apply');

        if (!fromInput || !toInput || !applyBtn) return;

        applyBtn.onclick = function() {
            const from = fromInput.value;
            const to = toInput.value;
            const today = new Date().toISOString().split('T')[0];

            if (from > to) {
                alert('Ngày kết thúc không được nhỏ hơn ngày bắt đầu.');
                return;
            }
            // Không chặn ngày tương lai nếu khách muốn lọc trước? 
            // Nhưng code cũ chặn nên mình giữ nguyên hoặc nới lỏng nếu cần.
            
            loadRange(from, to);
        };
    }

    function loadRange(from, to) {
        const contentEl = document.getElementById('content');
        if (!contentEl) return;

        fetch('dash_revenue.php?from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to))
            .then(r => r.text())
            .then(html => {
                contentEl.innerHTML = html;
                // Sau khi thay đổi nội dung, cần gắn lại sự kiện
                initRevenuePage();
            })
            .catch(err => console.error('Load revenue failed', err));
    }

    // Expose ra global để index.php gọi
    window.initRevenuePage = initRevenuePage;

    // Chạy luôn lần đầu
    initRevenuePage();
})();
