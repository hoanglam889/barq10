<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user_name'];
$profile = $_SESSION['user_profile'] ?? 'Khách hàng'; 
$role = $_SESSION['user_role'] ?? 'user';
$initial = mb_strtoupper(mb_substr($profile, 0, 1));
?>
<style>
    :root {
        --primary: #f59e0b;
        --primary-dark: #d97706;
        --border-color: #e9ecef;
    }
    
    /* Since we are inside index.php, we just need the local component styles */
    .profile-container-tab {
        max-width: 1000px;
        margin: 60px auto 40px; /* Thêm margin-top để không bị navbar che */
        padding: 0 20px;
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 24px;
        font-family: 'Inter', sans-serif;
    }

    .profile-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    .profile-sidebar {
        padding: 32px 24px;
        text-align: center;
    }

    .avatar-lg {
        width: 120px;
        height: 120px;
        background: #fff3cd;
        color: var(--primary-dark);
        font-size: 48px;
        font-weight: 700;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
    }

    .sidebar-name {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 4px;
        color: #212529;
    }

    .sidebar-role {
        font-size: 13px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    .sidebar-divider {
        height: 1px;
        background: var(--border-color);
        margin: 24px 0;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }

    .sidebar-menu li {
        margin-bottom: 8px;
    }

    .sidebar-menu a, .sidebar-menu button {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        padding: 12px 16px;
        border-radius: 8px;
        color: #212529;
        text-decoration: none;
        font-weight: 500;
        background: transparent;
        border: none;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }

    .sidebar-menu a.active {
        background: #fff3cd;
        color: var(--primary-dark);
    }

    .sidebar-menu a:hover, .sidebar-menu button:hover {
        background: #f8f9fa;
    }

    .sidebar-menu .btn-logout {
        color: #dc3545;
    }
    .sidebar-menu .btn-logout:hover {
        background: #fdf3f4;
    }

    .profile-details {
        padding: 0;
    }

    .card-header-custom {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
    }

    .card-header-custom h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #212529;
    }

    .card-body-custom {
        padding: 24px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .info-box {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    .info-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 500;
        color: #212529;
    }

    @media (max-width: 768px) {
        .profile-container-tab {
            grid-template-columns: 1fr;
            margin-top: 20px;
        }
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-container-tab">
    <!-- Sidebar -->
    <div class="profile-card profile-sidebar">
        <div class="avatar-lg">
            <?= htmlspecialchars($initial); ?>
        </div>
        <h1 class="sidebar-name"><?= htmlspecialchars($profile); ?></h1>
        <div class="sidebar-role">Tài khoản <?= htmlspecialchars($role); ?></div>

        <div class="sidebar-divider"></div>

        <ul class="sidebar-menu">
            <li>
                <a href="#" class="active">
                    <i class="fa-regular fa-id-badge"></i> Thông tin chung
                </a>
            </li>
            <li>
                <form action="history_order.php" method="post" style="margin:0;">
                    <button type="submit">
                        <i class="fa-solid fa-clock-rotate-left"></i> Lịch sử đơn hàng
                    </button>
                </form>
            </li>
            <li>
                <form action="logout.php" method="post" style="margin:0;">
                    <button type="submit" class="btn-logout">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Details -->
    <div class="profile-card profile-details">
        <div class="card-header-custom">
            <h2>Chi tiết tài khoản</h2>
        </div>
        <div class="card-body-custom">
            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Tên hiển thị</div>
                    <div class="info-value"><?= htmlspecialchars($profile); ?></div>
                </div>
                
                <div class="info-box">
                    <div class="info-label">Tên đăng nhập (Username)</div>
                    <div class="info-value"><?= htmlspecialchars($user); ?></div>
                </div>

                <div class="info-box">
                    <div class="info-label">Quyền hạn (Role)</div>
                    <div class="info-value" style="color: var(--primary-dark); font-weight: 600;">
                        <?= htmlspecialchars(strtoupper($role)); ?>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-label">Trạng thái</div>
                    <div class="info-value" style="color: #198754; font-weight: 600;">
                        <i class="fa-solid fa-circle-check" style="font-size: 14px;"></i> Đang hoạt động
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
