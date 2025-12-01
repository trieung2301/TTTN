<?php
if (!isset($targetUser) || !isset($actionUrl)) {
    return; 
}

$id = $targetUser['id'] ?? 0;
$username = $targetUser['username'] ?? 'N/A';
?>

<div class="modal fade" id="changePasswordModal<?= $id ?>" tabindex="-1" aria-labelledby="changePasswordModalLabel<?= $id ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light-subtle border-bottom"> 
                <h5 class="modal-title text-dark" id="changePasswordModalLabel<?= $id ?>">
                    <i class="fa-solid fa-key"></i> Đặt lại Mật Khẩu cho: **<?= htmlspecialchars($username) ?>**
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/php-pj/<?= htmlspecialchars($actionUrl) ?>" method="POST"> 
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    
                    <div class="mb-3">
                        <label for="new_password_<?= $id ?>" class="form-label">Mật khẩu mới (Tối thiểu 6 ký tự):</label>
                        <input type="password" class="form-control" id="new_password_<?= $id ?>" name="new_password" minlength="6" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password_<?= $id ?>" class="form-label">Xác nhận Mật khẩu mới:</label>
                        <input type="password" class="form-control" id="confirm_password_<?= $id ?>" minlength="6" required>
                        <div class="invalid-feedback" id="password_feedback_<?= $id ?>">
                            Mật khẩu xác nhận không khớp.
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-info btn-submit-password-change">Lưu Mật Khẩu Mới</button>
                </div>
            </form>
        </div>
    </div>
</div>