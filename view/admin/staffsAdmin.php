<?php 
include __DIR__ . "/components/header.php";
include __DIR__ . "/components/navbar.php";
include __DIR__ . "/components/sidebar.php";

$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

$users = $staff ?? [];

?>

<div class="main-content">
    <h1 class="mb-4 text-dark"><i class="fa-solid fa-user-tie"></i> Quản lý Nhân viên</h1> 
    
    <?php if ($success_message): ?><div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div><?php endif; ?>
    <?php if ($error_message): ?><div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form class="d-flex" method="GET" action="/php-pj/admin/staff/">
            <input type="hidden" name="action" value="admin/staff"> 
            <input class="form-control me-2" type="search" placeholder="Tìm kiếm nhân viên..." name="search" value="<?= htmlspecialchars($searchTerm ?? '') ?>">
            <button class="btn btn-outline-primary" type="submit"><i class="fa-solid fa-search"></i></button>
        </form>
        
        <div class="d-flex justify-content-end">
            <a href="/php-pj/admin/staff/add" class="btn btn-success">
                <i class="fa-solid fa-user-plus"></i> Thêm Nhân viên
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Level</th> 
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="9" class="text-center">Không có nhân viên nào.</td></tr>
                <?php else: ?>
                    <?php 
                    $modals = []; 
                    $currentAdminId = $_SESSION['user']['id'] ?? 0; 
                    ?>
                    <?php foreach ($users as $user): 
                        $status = $user['status'] ?? 0;
                        $level = $user['level'] ?? 'Common'; 
                        $isSelf = $user['id'] == $currentAdminId;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['fullname'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                            
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($level) ?></span>
                            </td>
                            
                            <td>
                                <span class="badge bg-danger">ADMIN</span>
                            </td>
                            
                            <td>
                                <span class="badge bg-<?= $status == 0 ? 'success' : 'danger' ?>"><?= $status == 0 ? 'Hoạt động' : 'Đã khóa' ?></span>
                                <?php if ($isSelf): ?>
                                    <span class="badge bg-primary ms-1">Bạn</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center text-nowrap">
                                
                                <a href="/php-pj/admin/staff/edit&id=<?= $user['id'] ?>" 
                                    class="btn btn-sm btn-primary" 
                                    title="Chỉnh sửa thông tin"
                                    <?= $isSelf ? 'disabled' : '' ?>>
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                
                                <button type="button" class="btn btn-sm btn-info" title="Đặt lại Mật khẩu" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#changePasswordModal<?= $user['id'] ?>">
                                    <i class="fa-solid fa-key"></i>
                                </button>

                                <?php 
                                $isLocked = $status == 1; 
                                $confirmAction = $isLocked ? 'mở khóa' : 'khóa';
                                $buttonClass = $isLocked ? 'btn-success' : 'btn-secondary';
                                $buttonIcon = $isLocked ? 'fa-lock-open' : 'fa-lock';
                                ?>
                                <form action="/php-pj/admin/staff/toggleStatus" method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="status" value="<?= $status ?>">
                                    <button type="submit" class="btn btn-sm <?= $buttonClass ?>" 
                                        onclick="return confirm('Bạn có muốn <?= $confirmAction ?> tài khoản này không?')"
                                        title="<?= $isLocked ? 'Mở khóa tài khoản' : 'Khóa tài khoản' ?>"
                                        <?= $isSelf ? 'disabled' : '' ?>>
                                        <i class="fa-solid <?= $buttonIcon ?>"></i>
                                    </button>
                                </form>
                                
                                <a href="/php-pj/admin/staff/delete&id=<?= $user['id'] ?>" 
                                    onclick="return confirm('Bạn có chắc chắn muốn HẠ CẤP nhân viên ID: <?= $user['id'] ?> thành KHÁCH HÀNG (USER) không?')"
                                    class="btn btn-sm btn-warning" 
                                    title="Hạ cấp thành User"
                                    <?= $isSelf ? 'disabled' : '' ?>>
                                   <i class="fa-solid fa-user-slash"></i>
                                </a>
                                
                            </td>
                        </tr>

                        <?php 
                        $targetUser = $user;
                        $actionUrl = 'admin/staff/changePassword'; 
                        
                        ob_start(); 
                        include __DIR__ . '/ChangePassword.php'; 
                        $modals[] = ob_get_clean(); 
                        ?> 
                        
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
</div>

<?php 
if (!empty($modals)) {
    echo implode("\n", $modals);
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form[action*="changePassword"]').forEach(form => {
        form.addEventListener('submit', function(event) {
            
            const id = this.querySelector('input[name="id"]').value;
            const newPassField = document.getElementById('new_password_' + id);
            const confirmPassField = document.getElementById('confirm_password_' + id);
            const feedbackDiv = document.getElementById('password_feedback_' + id);

            const newPassword = newPassField.value;
            const confirmPassword = confirmPassField.value;

            newPassField.classList.remove('is-invalid', 'is-valid');
            confirmPassField.classList.remove('is-invalid', 'is-valid');
            feedbackDiv.style.display = 'none';

            let isValid = true;

            if (newPassword !== confirmPassword) {
                event.preventDefault();
                confirmPassField.classList.add('is-invalid');
                feedbackDiv.innerHTML = 'Mật khẩu xác nhận không khớp.';
                feedbackDiv.style.display = 'block';
                isValid = false;
            }
        });
    });
});
</script>
</body>
</html>