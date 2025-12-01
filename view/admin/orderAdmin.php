<?php 
include __DIR__ . "/components/header.php";
include __DIR__ . "/components/navbar.php";
include __DIR__ . "/components/sidebar.php";

$orders = $orders ?? []; 
$statusMap = OrderAdminController::STATUS_MAP;
$statusKeys = array_keys($statusMap); 

$statusColors = [];
foreach ($statusMap as $key => $value) {
    $statusColors[$key] = $value['class']; 
}

// Hiển thị trạng thái thanh toán (Chỉ dùng cho trường hợp Đã thanh toán)
function getPaymentBadge($payment_status) {
    return match(trim($payment_status)) {
        'Đã thanh toán' => '<span class="badge bg-success fs-6 px-3 py-2">Đã thanh toán</span>',
        'Chưa thanh toán' => '<span class="badge bg-danger fs-6 px-3 py-2">Chưa thanh toán</span>',
        default => '<span class="badge bg-warning text-dark fs-6 px-3 py-2">Chờ xác nhận</span>',
    };
}

$currentStatusKey = $currentStatusKey ?? 'Tất cả'; 
$allStatusesKeys = $allStatusesKeys ?? ['Tất cả', 'Chờ xác nhận', 'Đang giao', 'Giao thành công', 'Đã hủy'];
?>

<div class="main-content">
    <h1 class="mb-4 text-dark fw-bold">Quản lý đơn hàng</h1> 

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-4">
        <?php foreach ($allStatusesKeys as $statusName): ?>
            <?php 
            $url = $statusName === 'Tất cả' 
                ? '/php-pj/admin/orders' 
                : '/php-pj/admin/orders?status=' . urlencode($statusName);
            $isActive = $currentStatusKey === $statusName ? 'active' : '';
            ?>
            <li class="nav-item">
                <a class="nav-link <?= $isActive ?>" href="<?= $url ?>">
                    <?= htmlspecialchars($statusName) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Địa chỉ</th>
                    <th>Ngày tạo</th>
                    <th>Phương thức</th>
                    <th>Trạng thái</th>
                    <th class="text-center">Thanh toán</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="9" class="text-center py-5 text-muted fs-5">Chưa có đơn hàng nào.</td></tr>
                <?php else: foreach ($orders as $order): 
                    $statusText = trim($order['status'] ?? 'Không rõ');
                    $badgeColor = $statusColors[$statusText] ?? 'secondary';
                    $paymentStatus = $order['payment_status'] ?? 'Chờ xác nhận';
                ?>
                    <tr>
                        <td><strong>#<?= $order['id'] ?></strong></td>
                        <td><?= htmlspecialchars($order['user_name'] ?? 'Khách vãng lai') ?></td>
                        <td class="fw-bold text-danger"><?= number_format($order['total'], 0, ',', '.') ?> VNĐ</td>
                        <td><?= htmlspecialchars($order['address'] . ', ' . $order['district'] . ', ' . $order['city']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        <td><?= htmlspecialchars($order['payment_method'] ?? 'COD') ?></td>
                        
                        <td><span class="badge bg-<?= $badgeColor ?> px-3 py-2"><?= htmlspecialchars($statusText) ?></span></td>

                        <td class="text-center">
                            <?php if (trim($paymentStatus) === 'Đã thanh toán'): ?>
                                <?= getPaymentBadge($paymentStatus) ?>
                            <?php else: 
                                // 2. Chưa thanh toán/Chờ xác nhận: Hiển thị nút (đóng vai trò là badge và trigger)
                                $buttonText = (trim($paymentStatus) === 'Chưa thanh toán') ? 'Chưa thanh toán' : 'Chờ xác nhận';
                                $buttonClass = (trim($paymentStatus) === 'Chưa thanh toán') ? 'btn-danger' : 'btn-warning text-dark';
                            ?>
                                <form action="/php-pj/admin/orders/confirm-manual-payment" method="POST" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit" class="btn <?= $buttonClass ?> btn-sm px-3 py-2 fw-bold"
                                            onclick="return confirm('Xác nhận khách đã thanh toán đơn #<?= $order['id'] ?>? Hành động này sẽ cập nhật trạng thái thanh toán thành Đã thanh toán.')"
                                            style="white-space: nowrap;">
                                        <?= $buttonText ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">
                            <a href="/php-pj/admin/orders/detail?id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <form action="/php-pj/admin/orders/updateStatus" method="POST" class="d-inline-block mt-1">
                                <input type="hidden" name="id" value="<?= $order['id'] ?>">
                                <select name="status" class="form-select form-select-sm d-inline-block w-auto">
                                    <?php foreach ($statusKeys as $name): ?>
                                        <option value="<?= $name ?>" <?= $statusText === $name ? 'selected' : '' ?>><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-info ms-1">Cập nhật</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>