<?php
    if(isset($_SESSION['user'])) {
        header("Location: /php-pj/index.php?action=home");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f8fbff, #e6f0ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 380px;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            animation: fadeInDown 1s ease;
        }

        .login-card h3 {
            font-weight: 700;
            color: #333;
        }

        .form-label {
            color: #555;
            font-size: 0.9rem;
        }

        .form-control {
            background: rgba(255,255,255,0.95);
            border: 1px solid #d0d7e2;
            color: #333;
        }

        .form-control::placeholder {
            color: #999;
        }

        .form-control:focus {
            border-color: #4facfe;
            box-shadow: 0 0 0 0.2rem rgba(79,172,254,0.2);
        }

        .btn-gradient {
            background: linear-gradient(to right, #4facfe, #00c6ff);
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
            color: #fff;
        }

        .btn-gradient:hover {
            transform: scale(1.05);
            background: linear-gradient(to right, #00c6ff, #4facfe);
        }

        .login-card p {
            color: #555;
        }

        .login-card a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        .login-card a:hover {
            text-decoration: underline;
        }

        .alert-custom {
            background: rgba(255, 50, 50, 0.1);
            color: #c00;
            border: 1px solid rgba(255, 0, 0, 0.2);
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.9rem;
        }

        @keyframes fadeInDown {
            from {opacity:0; transform: translateY(-20px);}
            to {opacity:1; transform: translateY(0);}
        }
    </style>
</head>

<body>

    <div class="login-card animate__animated animate__fadeInDown">
        <h3 class="text-center mb-4">Đăng nhập</h3>

        <?php if (isset($_SESSION['login-error'])): ?>
            <div class="alert alert-custom text-center">
                <?= htmlspecialchars($_SESSION['login-error']); ?>
            </div>
            <?php unset($_SESSION['login-error']); ?>
        <?php endif; ?>

        <form method="post" action="/php-pj/login/">
            <div class="mb-3">
                <label for="username" class="form-label">Tài khoản</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tài khoản" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>

            <button type="submit" class="btn btn-gradient w-100 mt-3 py-2">Đăng nhập</button>
        </form>

        <p class="text-center mt-3">
            Chưa có tài khoản?
            <a href="/php-pj/register/">Đăng ký ngay</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
