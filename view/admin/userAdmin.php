<?php 
include __DIR__ . "/components/header.php";
include __DIR__ . "/components/navbar.php";
include __DIR__ . "/components/sidebar.php";

$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<div class="main-content">
    <h1 class="mb-4 text-dark"><i class="fa-solid fa-users"></i> Quản lý Khách hàng</h1> 
    
    <?php if ($success_message): ?><div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div><?php endif; ?>
    <?php if ($error_message): ?><div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form class="d-flex" method="GET" action="/php-pj/admin/users">
            <input type="hidden" name="action" value="admin/users"> 
            <input class="form-control me-2" type="search" placeholder="Tìm kiếm khách hàng..." name="search" value="<?= htmlspecialchars($searchTerm ?? '') ?>">
            <button class="btn btn-outline-primary" type="submit"><i class="fa-solid fa-search"></i></button>
        </form>
        
        <div class="d-flex justify-content-end">
            <a href="/php-pj/admin/users/add" class="btn btn-primary me-2"><i class="fa-solid fa-plus"></i> Thêm Khách hàng</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th><th>Tên đầy đủ</th><th>Username</th><th>Email</th>
                    <th>Tổng Chi Tiêu</th><th>Cấp Độ</th><th>Trạng thái</th><th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php $modals = []; if (!empty($users)): ?>
                    <?php foreach ($users as $user): 
                        $id = $user['id'];
                        $status = $user['status'] ?? 0;
                        $level = strtolower($user['level'] ?? 'common');
                        $isLocked = $status == 1;
                        
                        $levelBadge = match ($level) {
                            'diamond' => 'bg-info', 'gold' => 'bg-warning text-dark',
                            'silver' => 'bg-secondary', default => 'bg-light text-dark',
                        };
                        $confirmAction = $isLocked ? 'mở khóa' : 'khóa';
                        $buttonClass = $isLocked ? 'btn-success' : 'btn-secondary';
                        $buttonIcon = $isLocked ? 'fa-lock-open' : 'fa-lock';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($id) ?></td>
                            <td><?= htmlspecialchars($user['fullname'] ?? '') ?></td>
                            <td><?= htmlspecialchars($user['username'] ?? '') ?></td>
                            <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                            
                            <td><strong><?= number_format($user['total_spent'] ?? 0, 0, ',', '.') ?> VNĐ</strong></td>
                            
                            <td><span class="badge <?= $levelBadge ?> text-uppercase"><?= htmlspecialchars($level) ?></span></td>

                            <td><span class="badge bg-<?= $status == 0 ? 'success' : 'danger' ?>"><?= $status == 0 ? 'Hoạt động' : 'Đã khóa' ?></span></td>
                            
                            <td class="text-nowrap text-center">
                                <a href="/php-pj/admin/users/edit&id=<?= $id ?>" class="btn btn-sm btn-info" title="Sửa thông tin"><i class="fa-solid fa-edit"></i></a>
                                
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal<?= $id ?>" title="Đổi mật khẩu"><i class="fa-solid fa-key"></i></button>
                                
                                <form action="/php-pj/admin/users/toggleStatus" method="POST" class="d-inline" onsubmit="return confirm('Bạn có muốn <?= $confirmAction ?> người dùng này không?');">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <input type="hidden" name="status" value="<?= $status ?>"> 
                                    <button type="submit" class="btn btn-sm <?= $buttonClass ?>" title="<?= $isLocked ? 'Mở khóa tài khoản' : 'Khóa tài khoản' ?>"><i class="fa-solid <?= $buttonIcon ?>"></i></button>
                                </form>
                                
                                <a href="/php-pj/admin/users/delete&id=<?= $id ?>" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn người dùng ID: <?= $id ?> không?')"
                                   class="btn btn-sm btn-danger" title="Xóa vĩnh viễn"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>

                        <?php 
                        $targetUser = $user; 
                        $actionUrl = 'admin/users/changePassword';
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
</body>
</html>