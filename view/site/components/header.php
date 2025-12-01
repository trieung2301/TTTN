<?php
$current_url = $_SERVER['REQUEST_URI'];

$is_home     = str_ends_with($current_url, '/home') || 
               $current_url === '/php-pj/' || 
               $current_url === '/php-pj/index.php' || 
               str_contains($current_url, '/home?');

$is_products = str_contains($current_url, '/getProducts');
$is_cart     = str_contains($current_url, '/cart') && !str_contains($current_url, '/checkout');
$is_about    = str_contains($current_url, '/about');

require_once __DIR__ . "/../../../model/Cart.php";
$pdo = Database::getConnection();
$cart = new Cart($pdo);
$userId = $_SESSION['user']['id'] ?? null;
$totalItems = $userId ? $cart->getTotalItems($userId) : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WatchShop - Đồng hồ chính hãng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>

<header class="main-header py-3 border-bottom shadow-sm">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <a href="/php-pj/home" class="text-decoration-none">
                <h1 class="h3 mb-0 fw-bold text-dark">WATCHSHOP</h1>
            </a>

            <form class="d-flex flex-grow-1 mx-4" role="search" method="GET" action="/php-pj/getProducts">
                <div class="input-group">
                    <input class="form-control" type="search" placeholder="Tìm kiếm sản phẩm..." name="search" 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button class="btn btn-outline-dark" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            <div class="d-flex align-items-center gap-4">
                
                <div class="dropdown">
                    <a class="text-dark text-decoration-none d-flex align-items-center fw-medium" 
                    href="#" 
                    role="button" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                        <i class="fa-solid fa-user me-2"></i> 
                        Tài khoản 
                        <i class="fas fa-caret-down ms-2"></i> 
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <?php if (isset($_SESSION['user'])): ?>
                            <li><h6 class="dropdown-header">Xin chào, <?= htmlspecialchars($_SESSION['user']['username']) ?></h6></li>
                            <li><a class="dropdown-item" href="/php-pj/editProfile"><i class="fa-solid fa-user-gear me-2"></i> Hồ sơ của tôi</a></li>
                            <li><a class="dropdown-item" href="/php-pj/order"><i class="fa-solid fa-receipt me-2"></i> Đơn hàng đã đặt</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/php-pj/logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="/php-pj/login"><i class="fa-solid fa-right-to-bracket me-2"></i> Đăng nhập</a></li>
                            <li><a class="dropdown-item" href="/php-pj/register"><i class="fa-solid fa-user-plus me-2"></i> Đăng ký</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="cart-icon">
                    <a href="/php-pj/cart" class="text-dark position-relative">
                        <i class="fa-solid fa-lg">🛒</i>
                        <?php if ($totalItems > 0): ?>
                            <span class="badge bg-danger badge-cart position-absolute top-0 start-100 translate-middle">
                                <?= $totalItems ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto fw-medium">
                <li class="nav-item">
                    <a href="/php-pj/home" class="nav-link <?= $is_home ? 'active' : '' ?>">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a href="/php-pj/getProducts" class="nav-link <?= $is_products ? 'active' : '' ?>">Sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a href="/php-pj/cart" class="nav-link <?= $is_cart ? 'active' : '' ?>">Giỏ hàng</a>
                </li>
                <li class="nav-item">
                    <a href="/php-pj/about" class="nav-link <?= $is_about ? 'active' : '' ?>">Liên hệ</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.hero-banner {
  height: 85vh;
  position: relative;
  overflow: hidden;
}
.banner-img {
  height: 85vh;
  width: 100%;
  object-fit: cover;
  filter: brightness(70%);
  transition: transform 3s ease;
}
.carousel-item.active .banner-img {
  transform: scale(1.08);
}
.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0,0,0,0.6) 10%, rgba(0,0,0,0.85) 100%);
}
.fade-in {
  opacity: 0;
  transform: translateY(25px);
  animation: fadeInUp 1.2s ease forwards;
}
.fade-in.delay-1 { animation-delay: .3s; }
.fade-in.delay-2 { animation-delay: .6s; }
.fade-in.delay-3 { animation-delay: .9s; }
@keyframes fadeInUp {
  to { opacity: 1; transform: translateY(0); }
}

.btn-gradient {
  background: linear-gradient(90deg, #00b4db 0%, #0083b0 100%);
  border: none;
  color: #fff;
  font-weight: 600;
  letter-spacing: 1px;
  border-radius: 50px;
  padding: 0.8rem 2rem;
  transition: all .35s ease;
}
.btn-gradient:hover {
  background: linear-gradient(90deg, #0083b0 0%, #00b4db 100%);
  transform: scale(1.08);
  box-shadow: 0 0 20px rgba(0,180,219,0.5);
}

.glass-select {
  background: rgba(255,255,255,0.7);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  padding: .6rem 1rem;
  border: 1px solid rgba(255,255,255,0.3);
  transition: all 0.3s ease;
}
.glass-select:hover {
  background: rgba(255,255,255,0.9);
}

.product-card {
  border-radius: 16px;
  background: #fff;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: all 0.4s ease;
}
.product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.product-img-wrapper {
  width: 100%;
  height: 260px;
  overflow: hidden;
}
.product-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: all 0.5s ease;
}
.product-card:hover .product-img {
  transform: scale(1.08);
  filter: brightness(90%);
}

.text-truncate-2 {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  min-height: 48px;
}

.overlay {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  opacity: 0;
  transition: all 0.4s ease;
}
.product-card:hover .overlay {
  opacity: 1;
}
.overlay .btn {
  border-radius: 30px;
  padding: 0.6rem 1.5rem;
}

.footer-modern {
  background: #000;
  border-top: 1px solid rgba(255,255,255,0.15);
}

.bg-gradient-light {
  background: linear-gradient(to right, #f8f9fa, #e9ecef);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>