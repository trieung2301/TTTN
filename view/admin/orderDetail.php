<?php
include __DIR__ . "/components/header.php";
include __DIR__ . "/components/navbar.php";
include __DIR__ . "/components/sidebar.php";

function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' VNĐ';
}

function formatDate($datetimeString) {
    if (empty($datetimeString) || $datetimeString === 'N/A') {
        return 'N/A';
    }
    try {
        $date = new DateTime($datetimeString);
        return $date->format('d-m-Y H:i:s'); 
    } catch (Exception $e) {
        return htmlspecialchars($datetimeString);
    }
}

function formatShippingAddress($orderDetail) {
    $addressParts = [];
    if (!empty($orderDetail['address'])) $addressParts[] = $orderDetail['address'];
    if (!empty($orderDetail['district'])) $addressParts[] = $orderDetail['district'];
    if (!empty($orderDetail['city'])) $addressParts[] = $orderDetail['city'];

    return htmlspecialchars(implode(', ', $addressParts) ?: 'N/A');
}

// Hàm hiển thị badge trạng thái thanh toán (cho thẻ thông tin)
function getPaymentStatusBadge($paymentStatus) {
    $paymentStatus = trim($paymentStatus ?? 'Chờ xác nhận');
    return match($paymentStatus) {
        'Đã thanh toán' => '<span class="badge bg-success">Đã thanh toán</span>',
        'Chưa thanh toán' => '<span class="badge bg-danger">Chưa thanh toán</span>',
        default => '<span class="badge bg-warning text-dark">Chờ xác nhận</span>',
    };
}
?>

<div class="main-content">
    <div class="container-fluid">
        <h1 class="mb-4 text-dark"><i class="fa-solid fa-receipt"></i> Chi tiết Đơn hàng #<?= htmlspecialchars($orderId) ?></h1>

        <div class="d-flex justify-content-between mb-4">
            <a href="/php-pj/admin/orders" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Quay lại Danh sách Đơn hàng
            </a>
            
            <div class="d-flex gap-4 align-items-center">
                <span class="fs-4">
                    Trạng thái Đơn hàng: <span class="badge bg-<?= htmlspecialchars($badgeColor) ?> fs-5 px-3 py-2"><?= htmlspecialchars($statusText) ?></span>
                </span>

                <span class="fs-4">
                    Trạng thái Thanh toán:
                    <?php 
                    $paymentStatus = trim($orderDetail['payment_status'] ?? 'Chờ xác nhận');
                    
                    if ($paymentStatus === 'Đã thanh toán'): ?>
                        <span class="badge bg-success fs-5 px-3 py-2"><?= htmlspecialchars($paymentStatus) ?></span>
                    <?php else:
                        // 2. Chưa thanh toán/Chờ xác nhận: Hiển thị nút bấm
                        $buttonText = $paymentStatus; 
                        $buttonClass = ($paymentStatus === 'Chưa thanh toán') ? 'btn-danger' : 'btn-warning text-dark';
                    ?>
                        <form action="/php-pj/admin/orders/confirm-manual-payment" method="POST" class="d-inline">
                            <input type="hidden" name="order_id" value="<?= $orderId ?>">
                            <button type="submit" class="btn <?= $buttonClass ?> btn-sm px-3 py-2 fw-bold fs-5"
                                    onclick="return confirm('Xác nhận khách đã thanh toán đơn #<?= $orderId ?>? Hành động này sẽ cập nhật trạng thái thanh toán thành Đã thanh toán.')"
                                    style="white-space: nowrap;">
                                <?= htmlspecialchars($buttonText) ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </span>
            </div>
            </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                Thông tin Giao nhận & Thanh toán
            </div>
            <div class="card-body row">
                <div class="col-md-6">
                    <h5 class="fw-bold">Thông tin Khách hàng</h5>
                    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($orderDetail['fullname'] ?? 'N/A') ?></p> 
                    <p><strong>Ngày đặt:</strong> <?= formatDate($orderDetail['created_at'] ?? 'N/A') ?></p>
                    <p><strong>Phương thức TT:</strong> <?= htmlspecialchars($orderDetail['payment_method'] ?? 'N/A') ?></p>
                    <p><strong>Trạng thái TT:</strong> <?= getPaymentStatusBadge($orderDetail['payment_status'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-6">
                    <h5 class="fw-bold">Địa chỉ Giao hàng</h5>
                    <p><strong>Người nhận:</strong> <?= htmlspecialchars($orderDetail['fullname'] ?? 'N/A') ?></p> 
                    <p><strong>SĐT:</strong> <?= htmlspecialchars($orderDetail['phone'] ?? 'N/A') ?></p>
                    <p><strong>Địa chỉ:</strong> <?= formatShippingAddress($orderDetail) ?></p>
                </div>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white">
                Sản phẩm đã đặt
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th> 
                            <th>Sản phẩm</th>
                            <th>Ảnh</th> 
                            <th>Giá (Đơn vị)</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $subtotal = 0; ?>
                        <?php foreach ($orderItems as $item): 
                            $itemTotal = ($item['quantity'] ?? 0) * ($item['price'] ?? 0); 
                            $subtotal += $itemTotal;
                            
                            // Đường dẫn ảnh giả định, sử dụng cột 'image'
                            $imagePath = '/php-pj/view/image/' . ($item['image'] ?? 'default.jpg');
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_id'] ?? 'N/A') ?></td> 
                                
                                <td><?= htmlspecialchars($item['name'] ?? 'Sản phẩm không rõ') ?></td>
                                
                                <td class="text-center">
                                    <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($item['name'] ?? 'Sản phẩm') ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                
                                <td><?= formatCurrency($item['price'] ?? 0) ?></td>
                                <td><?= htmlspecialchars($item['quantity'] ?? 0) ?></td>
                                <td><?= formatCurrency($itemTotal) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Tổng tiền hàng:</th>
                            <th><?= formatCurrency($subtotal) ?></th>
                        </tr>
                        <?php 
                        $discountAmount = $orderDetail['discount_amount'] ?? 0;
                        if ($discountAmount > 0): 
                        ?>
                        <tr>
                            <th colspan="5" class="text-end">Giảm giá (<?= htmlspecialchars($orderDetail['discount_code'] ?? '') ?>):</th>
                            <th class="text-danger">- <?= formatCurrency($discountAmount) ?></th>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th colspan="5" class="text-end text-success fs-5">TỔNG THANH TOÁN:</th>
                            <th class="text-success fs-5"><?= formatCurrency($orderDetail['total'] ?? $subtotal) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>