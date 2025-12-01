<?php include __DIR__ . '/components/header.php'; ?>

<style>
    body { background: #f8fafc; font-family: 'Segoe UI', sans-serif; }
    .filter-form { background: white; padding: 2rem; border-radius: 18px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); margin-bottom: 3rem; }
    .product-card { border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,0.15); }
    .product-img-wrapper { height: 250px; overflow: hidden; position: relative; background: #fcfcfc; }
    .product-img { width: 100%; height: 100%; object-fit: contain; transition: transform 0.5s; }
    .product-card:hover .product-img { transform: scale(1.1); }
    .overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.4s; display: flex; align-items: center; justify-content: center; }
    .product-card:hover .overlay { opacity: 1; }
    .text-truncate-2 { overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    .btn-buy-card { background: #4facfe; color: white; border-radius: 8px; font-weight: 600; }
    .btn-buy-card:hover { background: #c82333; }
    .btn-add-cart-icon-card { background: #28a745; color: white; width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    .btn-add-cart-icon-card:hover { background: #1e7e34; }
</style>

<div class="container my-5">
    <h1 class="mb-4 text-center">Danh sách sản phẩm</h1>

    <form method="GET" action="/php-pj/getProducts" class="filter-form">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">Danh mục</label>
                <select name="category" class="form-select">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($_GET['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Sắp xếp</label>
                <select name="sort" class="form-select">
                    <option value="">Mặc định</option>
                    <option value="asc" <?= ($_GET['sort'] ?? '') === 'asc' ? 'selected' : '' ?>>Giá: Thấp → Cao</option>
                    <option value="desc" <?= ($_GET['sort'] ?? '') === 'desc' ? 'selected' : '' ?>>Giá: Cao → Thấp</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Lọc</button>
            </div>
            <div class="col-md-2">
                <a href="/php-pj/getProducts" class="btn btn-secondary w-100">Reset</a>
            </div>
        </div>
    </form>

    <div class="row g-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $pd): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 d-flex align-items-stretch">
                    <div class="product-card d-flex flex-column w-100">
                        <div class="position-relative overflow-hidden product-img-wrapper">
                            <a href="/php-pj/productDetails&id=<?= $pd['id'] ?>">
                                <img src="/php-pj/view/image/<?= htmlspecialchars($pd['image']) ?>" class="product-img" alt="<?= htmlspecialchars($pd['name']) ?>">
                            </a>
                            <div class="overlay">
                                <a href="/php-pj/productDetails&id=<?= $pd['id'] ?>" class="btn btn-outline-light">Xem chi tiết</a>
                            </div>
                        </div>

                        <div class="p-3 text-center flex-grow-1 d-flex flex-column">
                            <h5 class="fw-semibold text-dark text-truncate-2 mb-2"><?= htmlspecialchars($pd['name']) ?></h5>
                            <p class="text-danger fs-5 fw-bold mb-3"><?= number_format($pd['price'], 0, ',', '.') ?> VND</p>

                            <div class="d-flex gap-2 mt-auto">
                                <form method="POST" action="/php-pj/buyNow" class="flex-grow-1">
                                    <input type="hidden" name="product_id" value="<?= $pd['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-buy-card w-100">Mua</button>
                                </form>
                                <form method="POST" action="/php-pj/addCart">
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
            <div class="col-12 text-center">
                <p class="alert alert-warning">Không tìm thấy sản phẩm nào.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/components/footer.php'; ?>