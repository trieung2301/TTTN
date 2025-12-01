<?php include __DIR__ . '/components/header.php'; ?>

<style>
    body {
        background: #f5f8fa;
    }

    h2 {
        font-weight: 700;
        color: #222;
    }

    /* Bảng giỏ hàng */
    table {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0,0,0,0.05);
    }

    thead.table-dark th {
        background: linear-gradient(to right, #4facfe, #00c6ff);
        color: #fff;
        font-weight: 600;
    }

    tbody tr:hover {
        background: #f8faff;
        transition: 0.3s ease;
    }

    td img {
        border-radius: 8px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }

    /* Nút Xóa */
    .btn-danger {
        background: linear-gradient(to right, #ff5858, #f857a6);
        border: none;
        border-radius: 50px;
        font-weight: 500;
        padding: 0.4rem 1rem;
        transition: 0.3s ease;
    }
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(248,87,166,0.3);
    }

    /* Nút Thanh toán */
    .btn-success {
        background: linear-gradient(to right, #4facfe, #00c6ff);
        border: none;
        border-radius: 50px;
        font-weight: 600;
        padding: 0.7rem 2rem;
        transition: 0.3s ease;
    }
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,198,255,0.3);
    }

    /* Tổng cộng */
    .cart-total {
        font-weight: 700;
        font-size: 1.3rem;
        color: #444;
    }

    .cart-total span {
        font-size: 1.5rem;
        color: #00c6ff;
    }

    .alert-info {
        background: #e8f7ff;
        border: 1px solid #bde0ff;
        color: #007acc;
        font-weight: 500;
        border-radius: 12px;
    }
</style>

<div class="container my-5">
    <h2 class="mb-4 text-center">🛒 Giỏ hàng của bạn</h2>

    <?php if (!empty($cartItems)): ?>
        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php 
                $total = 0;
                foreach ($cartItems as $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
            ?>
                <tr>
                    <td>
                        <img src="view/image/<?= htmlspecialchars($item['image']) ?>" width="70" alt="Sản phẩm">
                    </td>
                    <td class="fw-semibold"><?= htmlspecialchars($item['name']) ?></td>
                    <td class="text-primary fw-bold"><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                    <td><?= $item['quantity'] ?></td>
                    <td class="fw-bold"><?= number_format($subtotal, 0, ',', '.') ?>₫</td>
                    <td>
                        <form action="/php-pj/cart" method="POST" onsubmit="return confirm('Xóa sản phẩm này khỏi giỏ?')">
                            <input type="hidden" name="product_id" value="<?= $item['product_id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-end mt-4">
            <div class="cart-total">
                Tổng cộng: <span><?= number_format($total, 0, ',', '.') ?>₫</span>
            </div>
            <a href="/php-pj/checkout" class="btn btn-success btn-lg mt-3">
                Thanh toán
            </a>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            🚫 Giỏ hàng của bạn đang trống!
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
