<?php 
?>
<div class="modal fade" id="editCouponModal" tabindex="-1" aria-labelledby="editCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/php-pj/admin/discounts/update" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCouponModalLabel">Sửa mã giảm giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_original_code" name="original_code"> 
                    
                    <div class="mb-3">
                        <label for="edit_code" class="form-label">Mã giảm giá (Code)</label>
                        <input type="text" class="form-control" id="edit_code" name="code" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_value" class="form-label">Giá trị giảm</label>
                        <input type="number" step="0.01" class="form-control" id="edit_value" name="discount_value" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_expires" class="form-label">Ngày hết hạn</label>
                        <input type="date" class="form-control" id="edit_expires" name="expires_at" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_limit" class="form-label">Giới hạn sử dụng (Tổng)</label>
                        <input type="number" class="form-control" id="edit_limit" name="usage_limit" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="1">Kích hoạt</option>
                            <option value="0">Vô hiệu hóa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning">Cập nhật mã giảm giá</button>
                </div>
            </form>
        </div>
    </div>
</div>