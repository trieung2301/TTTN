<?php include __DIR__ . '/components/header.php'; ?>

<style>
body { background: #f5f7fa; }
.nav-pills .nav-link {
    background: #fff; color: #555; font-weight: 500; border-radius: 20px;
    margin: 0 6px; padding: 8px 18px; border: 1px solid #ddd;
}
.nav-pills .nav-link.active {
    background: linear-gradient(90deg,#4facfe,#00c6ff); color: #fff; font-weight: 600;
}
.table { background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
</style>

<div class="container py-5">
    <h1 class="text-center mb-5 fw-bold text-primary">Đơn hàng của tôi</h1>

    <ul class="nav nav-pills justify-content-center mb-4">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pending">Đơn đặt hàng</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#completed">Lịch sử đơn hàng</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#cancelled">Đơn đã hủy</button></li>
    </ul>

    <div class="tab-content">

        <!-- TAB ĐƠN ĐẶT HÀNG -->
        <div class="tab-pane fade show active" id="pending">
            <?php
            $grouped = [];
            foreach ($pendingOrders as $row) {
                $id = $row['order_id'];
                if (!isset($grouped[$id])) {
                    $grouped[$id] = [
                        'order_id'       => $id,
                        'order_date'     => $row['order_date'],
                        'status'         => $row['status'],
                        'total_price'    => $row['total_price'],
                        'payment_status' => $row['payment_status'],
                        'items'          => []
                    ];
                }
                $grouped[$id]['items'][] = $row['product_name'] . ' (x' . $row['quantity'] . ')';
            }
            ?>
            <?php if ($grouped): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th><th>Ngày mua</th><th>Sản phẩm</th><th>Tổng tiền</th>
                                <th>Trạng thái đơn</th><th>Thanh toán</th><th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($grouped as $order): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                <td class="text-start">
                                    <?php foreach ($order['items'] as $it): ?>
                                        <div>• <?= htmlspecialchars($it) ?></div>
                                    <?php endforeach; ?>
                                </td>
                                <td class="fw-bold text-danger"><?= number_format($order['total_price'],0,',','.') ?> ₫</td>
                                <td><span class="badge bg-warning"><?= htmlspecialchars($order['status']) ?></span></td>
                                <td>
                                    <?php
                                    switch ($order['payment_status']) {
                                        case 'Đã thanh toán':
                                            echo '<span class="badge bg-success">Đã thanh toán</span>';
                                            break;
                                        case 'Chưa thanh toán':
                                            echo '<span class="badge bg-danger">Chưa thanh toán</span>';
                                            break;
                                        default:
                                            echo '<span class="badge bg-secondary">Chờ xác nhận</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($order['status'] === 'Chờ xác nhận'): ?>
                                        <form action="/php-pj/order" method="POST" class="d-inline">
                                            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                            <button type="submit" name="cancel_order" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Hủy đơn này?')">Hủy</button>
                                        </form>
                                    <?php else: ?>—<?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted fs-5">Chưa có đơn hàng nào.</p>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="completed">
            <?php
            $grouped = [];
            foreach ($completedOrders as $row) {
                $id = $row['order_id'];
                if (!isset($grouped[$id])) {
                    $grouped[$id] = [
                        'order_date'     => $row['order_date'],
                        'status'         => $row['status'],
                        'total_price'    => $row['total_price'],
                        'payment_status' => $row['payment_status'],
                        'items'          => []
                    ];
                }
                $grouped[$id]['items'][] = $row['product_name'] . ' (x' . $row['quantity'] . ')';
            }
            ?>
            <?php if ($grouped): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-success">
                            <tr><th>#</th><th>Ngày mua</th><th>Sản phẩm</th><th>Tổng tiền</th><th>Trạng thái đơn</th><th>Thanh toán</th></tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($grouped as $order): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                <td class="text-start">
                                    <?php foreach ($order['items'] as $it): ?><div>• <?= htmlspecialchars($it) ?></div><?php endforeach; ?>
                                </td>
                                <td class="fw-bold text-danger"><?= number_format($order['total_price'],0,',','.') ?> ₫</td>
                                <td><span class="badge bg-success"><?= $order['status'] ?></span></td>
                                <td>
                                    <?php
                                    switch ($order['payment_status']) {
                                        case 'Đã thanh toán': echo '<span class="badge bg-success">Đã thanh toán</span>'; break;
                                        case 'Chưa thanh toán': echo '<span class="badge bg-danger">Chưa thanh toán</span>'; break;
                                        default: echo '<span class="badge bg-secondary">Chờ xác nhận</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted fs-5">Chưa có đơn hoàn tất.</p>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="cancelled">
            <?php
            $grouped = [];
            foreach ($cancelledOrders as $row) {
                $id = $row['order_id'];
                if (!isset($grouped[$id])) {
                    $grouped[$id] = [
                        'order_date'     => $row['order_date'],
                        'status'         => $row['status'],
                        'total_price'    => $row['total_price'],
                        'payment_status' => $row['payment_status'],
                        'items'          => []
                    ];
                }
                $grouped[$id]['items'][] = $row['product_name'] . ' (x' . $row['quantity'] . ')';
            }
            ?>
            <?php if ($grouped): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-danger">
                            <tr><th>#</th><th>Ngày mua</th><th>Sản phẩm</th><th>Tổng tiền</th><th>Trạng thái đơn</th><th>Thanh toán</th></tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($grouped as $order): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                <td class="text-start">
                                    <?php foreach ($order['items'] as $it): ?><div>• <?= htmlspecialchars($it) ?></div><?php endforeach; ?>
                                </td>
                                <td class="fw-bold text-danger"><?= number_format($order['total_price'],0,',','.') ?> ₫</td>
                                <td><span class="badge bg-danger"><?= $order['status'] ?></span></td>
                                <td>
                                    <?php
                                    switch ($order['payment_status']) {
                                        case 'Đã thanh toán': echo '<span class="badge bg-success">Đã thanh toán</span>'; break;
                                        case 'Chưa thanh toán': echo '<span class="badge bg-danger">Chưa thanh toán</span>'; break;
                                        default: echo '<span class="badge bg-secondary">Chờ xác nhận</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted fs-5">Chưa có đơn bị hủy.</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include __DIR__ . '/components/footer.php'; ?>