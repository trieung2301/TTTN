<?php
// BỎ session_start() → ĐÃ CÓ TRONG index.php
// session_start();  ← XÓA DÒNG NÀY

// Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: /php-pj/login");
    exit;
}


// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editProfileController->updateProfile($_POST, $_SESSION['user']['id']);
}

// Include layout
include __DIR__ . '/components/header.php';
?>

<div class="container mt-5" style="max-width: 500px;">
    <h3 class="text-center mb-4">Cập nhật thông tin cá nhân</h3>

    <!-- Thông báo lỗi -->
    <?php if (!empty($_SESSION['profile-error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['profile-error']; unset($_SESSION['profile-error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Thông báo thành công -->
    <?php if (!empty($_SESSION['profile-success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['profile-success']; unset($_SESSION['profile-success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="post" action="/php-pj/editProfile">
        <div class="mb-3">
            <label class="form-label fw-semibold">Tên đăng nhập</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
            <input type="text" name="fullname" class="form-control" 
                   value="<?= htmlspecialchars($user['fullname']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" 
                   value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Số điện thoại</label>
            <input type="text" name="phone" class="form-control" 
                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
        </div>

        <hr class="my-4">

        <div class="mb-3">
            <label class="form-label fw-semibold">Mật khẩu mới</label>
            <input type="password" name="password" class="form-control" 
                   placeholder="Để trống nếu không đổi">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
            <input type="password" name="confirm_password" class="form-control" 
                   placeholder="Nhập lại mật khẩu mới">
        </div>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-lg">Lưu thay đổi</button>
            <a href="/php-pj/home" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/components/footer.php'; ?>