z<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4facfe, #00c6ff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
        }

        h3 {
            font-weight: 700;
            color: #333;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #00c6ff;
            box-shadow: 0 0 6px rgba(0,198,255,0.3);
        }

        .btn-success {
            background: linear-gradient(to right, #4facfe, #00c6ff);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,198,255,0.4);
        }

        a {
            color: #00c6ff;
            text-decoration: none;
            font-weight: 500;
        }
        a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            font-size: 0.9rem;
        }

    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card" style="width: 420px;">
        <h3 class="text-center mb-3">Đăng ký</h3>

        <!-- Thông báo lỗi -->
        <?php if (isset($_SESSION['regis-error'])): ?>
            <div class="alert alert-danger text-center py-2">
                <?= htmlspecialchars($_SESSION['regis-error']) ?>
            </div>
            <?php unset($_SESSION['regis-error']); ?>
        <?php endif; ?>

        <!-- Form Đăng ký -->
        <form method="post" action="/php-pj/register/">
            <div class="mb-3">
                <label for="fullname" class="form-label">Họ và tên</label>
                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Nhập họ và tên" required>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Tài khoản</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tài khoản" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Nhập số điện thoại" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Đăng ký</button>
        </form>

        <p class="text-center mt-3 mb-0">
            Đã có tài khoản? <a href="/php-pj/login">Đăng nhập</a>
        </p>
    </div>
</div>

</body>
</html>
