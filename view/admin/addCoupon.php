<?php 
$old_data = $old_data ?? []; 
?>

<div class="modal fade" id="addCouponModal" tabindex="-1" aria-labelledby="addCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/php-pj/admin/discounts/add" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCouponModalLabel">Thêm Mã Giảm Giá Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label for="code" class="form-label">Mã Giảm Giá (Code):</label>
                        <input type="text" class="form-control" id="code" name="code" value="<?= htmlspecialchars($old_data['code'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="discount_value" class="form-label">Giá Trị Giảm Giá (VNĐ):</label>
                        <input type="number" step="1" min="1" class="form-control" id="discount_value" name="discount_value" value="<?= htmlspecialchars($old_data['discount_value'] ?? 0) ?>" required>
                        <small class="form-text text-muted">Giá trị được xem là tiền tệ cố định.</small>
                    </div>

                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Ngày Hết Hạn:</label>
                        <input type="date" class="form-control" id="expires_at" name="expires_at" value="<?= htmlspecialchars($old_data['expires_at'] ?? date('Y-m-d', strtotime('+1 month'))) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="usage_limit" class="form-label">Giới Hạn Lượt Dùng:</label>
                        <input type="number" min="1" class="form-control" id="usage_limit" name="usage_limit" value="<?= htmlspecialchars($old_data['usage_limit'] ?? 100) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng Thái:</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="1" <?= ($old_data['status'] ?? 1) == 1 ? 'selected' : '' ?>>Kích Hoạt</option>
                            <option value="0" <?= ($old_data['status'] ?? 1) == 0 ? 'selected' : '' ?>>Vô Hiệu Hóa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm Mã Giảm Giá</button>
                </div>
            </form>
        </div>
    </div>
</div>