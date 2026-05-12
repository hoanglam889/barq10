<?php 
  include 'include/config.php';
  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
  <title>Bar Poseidon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="css/style.css" rel="stylesheet">
  <link rel="icon" href="img/icon.png" type="image/png">
  <link rel="apple-touch-icon" href="icons/icon-192.png">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Bar Poseidon">
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#007bff">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  
</head>
<!-- test toast message -->

<body>
<?php 
  session_start();
  if (!isset($_SESSION['user_id'])){
  header("Location: login.php");
  }
  $role = $_SESSION['user_role'];
  $toast_message = $_SESSION['toast_message'] ?? '';
  $toast_type = $_SESSION['toast_type'] ?? 'success';
  unset($_SESSION['toast_message'], $_SESSION['toast_type']);
?>

<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <!-- Nút Hamburger -->
    <button class="btn btn-outline-light me-2 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
      ☰
    </button>

    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center ms-2" href="#" onclick="reloadPage(); return false;">
    <img src="img/logo_poseidon.png" alt="Logo" class="logo-img">
</a>


    <!-- Desktop Menu -->
    <div class="d-none d-lg-block">
      
      <ul class="navbar-nav d-flex flex-row align-items-center">
        <li class="nav-item mx-2"><a id="link_revenue" class="nav-link ajax-link" href="#">DOANH THU</a></li>
        <li class="nav-item mx-2"> <a id="link_order" class="nav-link" href="#">ĐƠN HÀNG</a></li>
        <!--  <li class="nav-item mx-2"><a class="nav-link ajax-link" href="#">TỒN KHO</a></li>-->
        <?php if($role == 'admin') {
          echo '<li class="nav-item mx-2"><a id="link_products" class="nav-link ajax-link" href="#">SẢN PHẨM</a></li>';
          echo '<li class="nav-item mx-2"><a id="link_users" class="nav-link ajax-link" href="#">TÀI KHOẢN</a></li>';
        } ?>
        <li class="nav-item mx-2"> <a id="link_customers" class="nav-link" href="#">KHÁCH HÀNG</a></li>
        <li class="nav-item mx-2">
          <a href="index.php?tab=profile" class="btn-book"><?php echo $_SESSION['user_profile']; ?></a>
        </li>
        <li class="nav-item mx-2">
          <img src="https://upload.wikimedia.org/wikipedia/commons/2/21/Flag_of_Vietnam.svg" alt="VN" class="flag">
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar">
  <div class="offcanvas-header">
    <a href="index.php?tab=profile" class="btn-book"><?php echo $_SESSION['user_profile']; ?></a>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link ajax-link" href="index.php"> TRANG CHỦ</a></li>
      <li class="nav-item"><a id="link_revenue_offcanvas" class="nav-link ajax-link" href="#"> DOANH THU</a></li>
      <li class="nav-item"><a id="link_order_offcanvas" class="nav-link ajax-link" href="#"> ĐƠN HÀNG</a></li>
      <!-- <li class="nav-item"><a class="nav-link ajax-link" href="#">TỒN KHO</a></li> -->
      <?php if($role == 'admin') {
          echo '<li class="nav-item"><a id="link_products_offcanvas" class="nav-link ajax-link" href="#">SẢN PHẨM</a></li>';
          echo '<li class="nav-item"><a id="link_users_offcanvas" class="nav-link ajax-link" href="#">TÀI KHOẢN</a></li>';
        } ?>
      <li class="nav-item"><a id="link_customers_offcanvas" class="nav-link ajax-link" href="#"> KHÁCH HÀNG</a></li>
    </ul>
  </div>
</div>

<div id="content" style="padding: 20px;">
    <?php 
      $tab = $_GET['tab'] ?? '';
      if ($tab === 'products' && $role == 'admin') {
          include 'dash_products.php';
      } elseif ($tab === 'customers') {
          include 'dash_customer.php';
      } elseif ($tab === 'order') {
          include 'dash_order.php';
      } elseif ($tab === 'profile') {
          include 'profile.php';
      } elseif ($tab === 'users' && $role == 'admin') {
          include 'dash_users.php';
      } else {
          include 'banuoc.php';
      }
    ?>
    </div>
  </div>

<div id="toast" data-msg="<?= htmlspecialchars($toast_message) ?>" class="toast"></div>



<script>
  if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(reg => console.log('Service Worker đăng ký:', reg.scope))
      .catch(err => console.log('SW lỗi:', err));
  });
}

// ====== HIỂN THỊ TOAST ======
function showToast() {
    const toast = document.getElementById("toast");
    if (!toast) return;

    // Lấy nội dung từ data-msg
    const message = toast.dataset.msg ? toast.dataset.msg.trim() : '';
    if (message !== '') {
        toast.textContent = message; // Đảm bảo text không rỗng
        toast.classList.add("show");
        setTimeout(() => {
        toast.classList.remove("show");
        toast.remove(); // xoá hẳn khỏi DOM
    }, 3000);
    }
}

// Hiển thị toast ngay khi trang vừa load
window.addEventListener('DOMContentLoaded', showToast);

// ====== LOAD DASHBOARD PAGES ======
function loadOrder() {
    fetch("dash_order.php")
        .then(res => res.text())
        .then(html => {
            document.getElementById("content").innerHTML = html;
            showToast(); // Hiện toast ngay sau khi nội dung thay đổi
            const oldScript = document.getElementById('dash_order_script');
            if (oldScript) oldScript.remove();
            const script = document.createElement('script');
            script.src = 'js/dash_order.js';
            script.id = 'dash_order_script';
            script.onload = function() {
                if (typeof bindOrderRowClick === 'function') bindOrderRowClick();
            };
            document.body.appendChild(script);
        });
}

// Global: mở modal còn nợ theo customerId (dùng cho onclick khi HTML được chèn bằng fetch)
window.openCusNo = function (customerId) {
    if (!customerId) return;
    const modalEl = document.getElementById('modal_loc');
    if (!modalEl) return;
    const bodyEl = modalEl.querySelector('.modal-body');
    if (!bodyEl) return;
    bodyEl.innerHTML = `
      <div class="d-flex justify-content-center p-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>`;
    fetch('cus_no.php?id=' + encodeURIComponent(customerId))
      .then(res => res.ok ? res.text() : Promise.reject('Lỗi tải trang'))
      .then(html => {
        bodyEl.innerHTML = html;
        if (window.bootstrap && bootstrap.Modal) {
          bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
      })
      .catch(err => {
        bodyEl.innerHTML = `<div class="alert alert-danger">${err}</div>`;
      });
};

function loadRevenue() {
    fetch("dash_revenue.php")
        .then(res => res.text())
        .then(html => {
            document.getElementById("content").innerHTML = html;
            showToast();
            const oldScript = document.getElementById('dash_revenue_script');
            if (oldScript) oldScript.remove();
            const script = document.createElement('script');
            script.src = 'js/dash_revenue.js';
            script.id = 'dash_revenue_script';
            script.onload = function() {
                if (typeof initRevenuePage === 'function') initRevenuePage();
            };
            document.body.appendChild(script);
        });
}

function loadProducts() {
    fetch("dash_products.php")
        .then(res => res.text())
        .then(html => {
            document.getElementById("content").innerHTML = html;
            showToast();
            const oldScript = document.getElementById("dash_products_script");
            if (oldScript) oldScript.remove();
            const script = document.createElement("script");
            script.src = "js/dash_products.js";
            script.id = "dash_products_script";
            document.body.appendChild(script);
        });
}
 function loadCustomer() {
      fetch("dash_customer.php")
          .then(res => res.text())
          .then(html => {
              document.getElementById("content").innerHTML = html;
              showToast();
              const oldScript = document.getElementById('dash_customer_script');
              if (oldScript) oldScript.remove();
              const script = document.createElement('script');
              script.src = 'js/dash_customer.js';
              script.id = 'dash_customer_script';
              script.onload = function() {
                  if (typeof initCustomerPage === 'function') initCustomerPage();
              };
              document.body.appendChild(script);
          });
  }

function loadUsers() {
    fetch("dash_users.php")
        .then(res => res.text())
        .then(html => {
            document.getElementById("content").innerHTML = html;
            showToast();
            const oldScript = document.getElementById("dash_users_script");
            if (oldScript) oldScript.remove();
            const script = document.createElement("script");
            script.src = "js/dash_users.js";
            script.id = "dash_users_script";
            document.body.appendChild(script);
        });
}

// ====== EVENT BINDING ======
document.addEventListener("DOMContentLoaded", function() {
    // Desktop menu
    const linkOrder = document.getElementById("link_order");
    if (linkOrder) linkOrder.addEventListener("click", function(e) {
        e.preventDefault();
        loadOrder();
    });

    const linkRevenue = document.getElementById("link_revenue");
    if (linkRevenue) linkRevenue.addEventListener("click", function(e) {
        e.preventDefault();
        loadRevenue();
    });

    const linkProducts = document.getElementById("link_products");
    if (linkProducts) linkProducts.addEventListener("click", function(e) {
        e.preventDefault();
        loadProducts();
    });
    const linkCustomers = document.getElementById("link_customers");
    if (linkCustomers) linkCustomers.addEventListener("click", function(e) {
        e.preventDefault();
        loadCustomer();
    });
    const linkUsers = document.getElementById("link_users");
    if (linkUsers) linkUsers.addEventListener("click", function(e) {
        e.preventDefault();
        loadUsers();
    });

    // Offcanvas menu
    const linkOrderOffcanvas = document.getElementById("link_order_offcanvas");
    if (linkOrderOffcanvas) linkOrderOffcanvas.addEventListener("click", function(e) {
        e.preventDefault();
        loadOrder();
        const offcanvas = document.getElementById('offcanvasNavbar');
        if (offcanvas && typeof bootstrap !== 'undefined') {
            bootstrap.Offcanvas.getOrCreateInstance(offcanvas).hide();
        }
    });

    const linkRevenueOffcanvas = document.getElementById("link_revenue_offcanvas");
    if (linkRevenueOffcanvas) linkRevenueOffcanvas.addEventListener("click", function(e) {
        e.preventDefault();
        loadRevenue();
        const offcanvas = document.getElementById('offcanvasNavbar');
        if (offcanvas && typeof bootstrap !== 'undefined') {
            bootstrap.Offcanvas.getOrCreateInstance(offcanvas).hide();
        }
    });

    const linkProductsOffcanvas = document.getElementById("link_products_offcanvas");
    if (linkProductsOffcanvas) linkProductsOffcanvas.addEventListener("click", function(e) {
        e.preventDefault();
        loadProducts();
        const offcanvas = document.getElementById('offcanvasNavbar');
        if (offcanvas && typeof bootstrap !== 'undefined') {
            bootstrap.Offcanvas.getOrCreateInstance(offcanvas).hide();
        }
    });
    const linkCustomersOffcanvas = document.getElementById("link_customers_offcanvas");
    if (linkCustomersOffcanvas) linkCustomersOffcanvas.addEventListener("click", function(e) {
        e.preventDefault();
        loadCustomer();
        const offcanvas = document.getElementById('offcanvasNavbar');
        if (offcanvas && typeof bootstrap !== 'undefined') {
            bootstrap.Offcanvas.getOrCreateInstance(offcanvas).hide();
        }
    });
    const linkUsersOffcanvas = document.getElementById("link_users_offcanvas");
    if (linkUsersOffcanvas) linkUsersOffcanvas.addEventListener("click", function(e) {
        e.preventDefault();
        loadUsers();
        const offcanvas = document.getElementById('offcanvasNavbar');
        if (offcanvas && typeof bootstrap !== 'undefined') {
            bootstrap.Offcanvas.getOrCreateInstance(offcanvas).hide();
        }
    });
    // Tự động load JS tương ứng nếu có tham số ?tab= trên URL (dùng cho trường hợp F5 hoặc redirect)
    const urlParams = new URLSearchParams(window.location.search);
    const initialTab = urlParams.get('tab');
    if (initialTab === 'products') loadProducts();
    else if (initialTab === 'customers') loadCustomer();
    else if (initialTab === 'order') loadOrder();
    else if (initialTab === 'users') loadUsers();
    else if (initialTab === 'revenue') loadRevenue();
});
function reloadPage() {
        // Tải lại trang không sử dụng cache
        window.location.href = 'index.php?' + new Date().getTime();
    }


</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
