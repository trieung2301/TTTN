<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/php-pj/homeAdmin">Trang chủ Admin</a>
        <a href="/php-pj/home" class="btn btn-outline-light me-3">
            <i class="fa-solid fa-arrow-left"></i> Quay lại Shop
        </a>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="nav-link">
                        <li class="nav-item">
                        <a class="nav-link text-white" href="/php-pj/editProfileAdmin">
                            <i class="fa-solid fa-user-circle"></i>
                            <?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'Admin'); ?>
                        </a>
                    </li>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/php-pj/logout">Đăng xuất</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="wrapper">