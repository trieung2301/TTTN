<?php 
include __DIR__ . "/components/header.php";
include __DIR__ . "/components/navbar.php";
include __DIR__ . "/components/sidebar.php";

function formatValue($value) {
    $value = (float)$value;
    if ($value < 1) {
        return htmlspecialchars((string)$value);
    }

    return number_format($value, 0, '', ' ') . ' VNĐ'; // định dạng tiền VNĐ
}

?>

<div class="main-content">
    <h1 class="mb-4 text-dark"><i class="fa-solid fa-percent"></i> Quản lý Giảm giá</h1> 

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCouponModal">
        <i class="fa-solid fa-plus"></i> Thêm mã giảm giá
    </button>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th> <th>Mã</th>
                <th>Giá trị</th>
                <th>Giới hạn SL</th>
                <th>Đã dùng</th>
                <th>Hết hạn</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($coupons)): ?>
                <tr>
                    <td colspan="8">Chưa có mã giảm giá nào.</td> </tr>
            <?php else: ?>
                <?php foreach ($coupons as $coupon): 
                    $currentStat = $coupon['status'] ?? 0; 
                    $statusClass = $coupon['status_class'] ?? 'bg-secondary';
                    $statusText = $coupon['status_text'] ?? 'Không rõ';
                ?>
                <tr>
                    <td><?= htmlspecialchars($coupon['id']) ?></td> 
                    <td><?= htmlspecialchars($coupon['code']) ?></td>
                    
                    <td><?= formatValue($coupon['discount_value']) ?></td>
                    
                    <td><?= htmlspecialchars($coupon['usage_limit']) ?></td>
                    <td><?= htmlspecialchars($coupon['used_count']) ?></td>
                    <td><?= date('d-m-Y', strtotime($coupon['expires_at'])) ?></td>
                    <td>
                        <span class="badge <?= $statusClass ?>">
                            <?= $statusText ?>
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning edit-coupon-btn" 
                                data-bs-toggle="modal" data-bs-target="#editCouponModal"
                                data-code="<?= htmlspecialchars($coupon['code']) ?>"
                                data-value="<?= htmlspecialchars($coupon['discount_value']) ?>"
                                data-expires="<?= htmlspecialchars(date('Y-m-d', strtotime($coupon['expires_at']))) ?>"
                                data-limit="<?= htmlspecialchars($coupon['usage_limit']) ?>"
                                data-status="<?= htmlspecialchars($currentStat) ?>">
                            Sửa
                        </button>

                        <form action="/php-pj/admin/discounts/toggleStatus" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn <?= $currentStat == 1 ? 'vô hiệu hóa' : 'kích hoạt' ?> mã này?')">
                            <input type="hidden" name="code" value="<?= htmlspecialchars($coupon['code']) ?>">
                            <input type="hidden" name="action_type" value="<?= $currentStat == 1 ? 'disable' : 'enable' ?>">
                            <button type="submit" class="btn btn-sm <?= $currentStat == 1 ? 'btn-danger' : 'btn-success' ?>">
                                <?= $currentStat == 1 ? 'Tắt' : 'Bật' ?>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
require_once __DIR__ . "/addCoupon.php"; 
require_once __DIR__ . "/editCoupon.php"; 
?>

<script> // lấy các dữ liệu từ Coupon cần sửa gán vào editCoupon.php
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-coupon-btn');
        const editModal = document.getElementById('editCouponModal');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const code = this.getAttribute('data-code');
                const value = this.getAttribute('data-value');
                const expires = this.getAttribute('data-expires');
                const limit = this.getAttribute('data-limit');
                const status = this.getAttribute('data-status');

                if (editModal) {
                    editModal.querySelector('#edit_original_code').value = code;
                    editModal.querySelector('#edit_code').value = code;
                    editModal.querySelector('#edit_value').value = value;
                    editModal.querySelector('#edit_expires').value = expires;
                    editModal.querySelector('#edit_limit').value = limit;
                    editModal.querySelector('#edit_status').value = status;
                }
            });
        });

        <?php if (isset($_SESSION['error_message']) && !empty($old_data['code'])): ?>
            const addModalElement = document.getElementById('addCouponModal');
            if (typeof bootstrap !== 'undefined' && addModalElement) {
                const addModal = new bootstrap.Modal(addModalElement);
                addModal.show();
            }
        <?php endif; ?>
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>