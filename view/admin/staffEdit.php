<?php 
include __DIR__ . "/components/header.php";
include __DIR__ . "/components/navbar.php";
include __DIR__ . "/components/sidebar.php";

$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

?>

    <div class="main-content">
        <h1 class="mb-4 text-primary">
            <i class="fa-solid fa-edit"></i> Chỉnh sửa Nhân viên ID: <?= htmlspecialchars($user['id']) ?> (Role: <?= htmlspecialchars($user['role'] ?? 'N/A') ?>)
        </h1>
        
        <?php if ($success_message): ?><div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div><?php endif; ?>
        <?php if ($error_message): ?><div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                Thông tin cơ bản
            </div>
            <div class="card-body">
                <form action="/php-pj/admin/staff/update" method="POST">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fullname" class="form-label">Họ tên</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" 
                                value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Vai trò</label>
                        <select class="form-select" id="role" name="role" required>
                            <?php $currentRole = $user['role'] ?? 'user';?>
                            <option value="admin" <?= $currentRole === 'admin' ? 'selected' : '' ?>>
                                Admin
                            </option>
                            <option value="user" <?= $currentRole === 'user' ? 'selected' : '' ?>>
                                User
                            </option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="/php-pj/admin/staff" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i> Cập nhật Thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>