<?php
include __DIR__ . "/components/header.php";
include __DIR__ . "/components/navbar.php";
include __DIR__ . "/components/sidebar.php";
?>
<body class="bg-light">

<div class="container mt-5" style="max-width: 500px;">
    <h3 class="text-center mb-4">Cập nhật thông tin cá nhân</h3>

    <?php if (!empty($_SESSION['profile-error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['profile-error']; unset($_SESSION['profile-error']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['profile-success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['profile-success']; unset($_SESSION['profile-success']); ?>
        </div>
    <?php endif; ?>

    <!-- ✅ Chỉ gọi editProfileAdmin -->
    <form method="post" action="/php-pj/editProfileAdmin/">
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" name="fullname" class="form-control" 
                   value="<?= htmlspecialchars($user['fullname']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" 
                   value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="phone" class="form-control" 
                   value="<?= htmlspecialchars($user['phone']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu mới</label>
            <input type="password" name="password" class="form-control" placeholder="Để trống nếu không đổi">
        </div>

        <div class="mb-3">
            <label class="form-label">Xác nhận mật khẩu</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu mới">
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            <a href="/php-pj/homeAdmin" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>

</body>
</html>
