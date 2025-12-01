<?php include __DIR__ . '/components/header.php'; ?>

<header class="hero-banner position-relative">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="/php-pj/view/image/dong-ho-nam.jpg" class="d-block w-100 banner-img" alt="Đồng hồ nam">
            </div>
            <div class="carousel-item">
                <img src="/php-pj/view/image/dong-ho-nu.jpg" class="d-block w-100 banner-img" alt="Đồng hồ nữ">
            </div>
            <div class="carousel-item">
                <img src="/php-pj/view/image/dong-ho-thong-minh.webp" class="d-block w-100 banner-img" alt="Smart watch">
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>

        <div class="hero-overlay"></div>
        <div class="hero-content text-center text-white position-absolute top-50 start-50 translate-middle z-3">
            <h1 class="display-3 fw-bold animate__animated animate__fadeInDown">WATCHSHOP</h1>
            <p class="lead animate__animated animate__fadeInUp">Khám phá thời trang đỉnh cao của thế giới đồng hồ</p>
            <a href="#product-section" class="btn btn-gradient btn-lg shadow animate__animated animate__fadeInUp">Khám phá ngay</a>
        </div>
    </div>
</header>

<section id="product-section" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5 display-5 text-uppercase text-secondary">Sản phẩm nổi bật</h2>
        <div class="row g-4">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $pd): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 d-flex align-items-stretch">
                        <div class="product-card d-flex flex-column w-100">
                            <div class="position-relative overflow-hidden product-img-wrapper">
                                <a href="/php-pj/productDetails&id=<?= $pd['id'] ?>">
                                    <img src="/php-pj/view/image/<?= htmlspecialchars($pd['image']) ?>" class="product-img" alt="<?= htmlspecialchars($pd['name']) ?>">
                                </a>
                                <div class="overlay d-flex justify-content-center align-items-center">
                                    <a href="/php-pj/productDetails&id=<?= $pd['id'] ?>" class="btn btn-outline-light">Xem chi tiết</a>
                                </div>
                            </div>

                            <div class="p-3 text-center d-flex flex-column flex-grow-1">
                                <h5 class="fw-semibold text-dark text-truncate-2 mb-2"><?= htmlspecialchars($pd['name']) ?></h5>
                                <p class="text-danger fs-5 fw-bold mb-3"><?= number_format($pd['price'], 0, ',', '.') ?> VND</p>

                                <div class="d-flex gap-2 mt-auto">
                                    <form method="POST" action="/php-pj/buyNow" class="flex-grow-1">
                                        <input type="hidden" name="product_id" value="<?= $pd['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-buy-card w-100">Mua</button>
                                    </form>
                                    <form method="POST" action="/php-pj/addCart" class="flex-grow-0">
                                        <input type="hidden" name="product_id" value="<?= $pd['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-add-cart-icon-card">
                                            <i class="fa-solid fa-cart-shopping"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center"><p class="text-muted">Không tìm thấy sản phẩm.</p></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
    .hero-banner { height: 85vh; position: relative; overflow: hidden; }
    .banner-img { height: 85vh; width: 100%; object-fit: cover; filter: brightness(70%); transition: transform 8s ease; }
    .carousel-item.active .banner-img { transform: scale(1.1); }
    .hero-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.85) 100%); }
    .btn-gradient { background: linear-gradient(90deg, #00b4db, #0083b0); border: none; color: #fff; padding: 0.8rem 2.5rem; border-radius: 50px; font-weight: 600; }
    .btn-gradient:hover { background: linear-gradient(90deg, #0083b0, #00b4db); transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,180,219,0.5); }
    .product-card { border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s; }
    .product-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.2); }
    .product-img-wrapper { height: 250px; overflow: hidden; position: relative; }
    .product-img { width: 100%; height: 100%; object-fit: contain; transition: transform 0.5s; }
    .product-card:hover .product-img { transform: scale(1.1); }
    .overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.4s; display: flex; align-items: center; justify-content: center; }
    .product-card:hover .overlay { opacity: 1; }
    .text-truncate-2 { overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    .btn-buy-card { background: #4facfe; color: white; border-radius: 8px; font-weight: 600; }
    .btn-buy-card:hover { background: #c82333; }
    .btn-add-cart-icon-card { background: #28a745; color: white; width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    .btn-add-cart-icon-card:hover { background: #1e7e34; }
</style>

<?php include __DIR__ . '/components/footer.php'; ?>