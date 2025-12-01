<?php
session_start();
if (!isset($_SESSION['momo_qr_info'])) {
    header('Location: /php-pj/index.php');
    exit;
}

$orderId = $_SESSION['momo_qr_info']['order_id'];
$total   = $_SESSION['momo_qr_info']['total'];
unset($_SESSION['momo_qr_info']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán MoMo - WatchShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #a12c8f, #ee3d96); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { max-width: 420px; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.4); }
        .header { background: linear-gradient(135deg, #a12c8f, #e91e63); color: white; padding: 30px; text-align: center; }
        .qr { max-width: 280px; border: 10px solid white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <h3 class="mb-0 fw-bold">Thanh toán MoMo</h3>
    </div>
    <div class="card-body text-center p-5 bg-white">
        <h2 class="text-danger fw-bold"><?= number_format($total,0,',','.') ?> ₫</h2>
        <p class="text-muted">Mã đơn hàng: <strong class="text-danger fs-5"><?= $orderId ?></strong></p>

        <img src="https://api.vietqr.io/image/971025-0834691877-hI20S2E.jpg?<?= http_build_query([
            'accountName' => 'NGUYEN QUOC TRIEU',
            'amount'      => $total,
            'addInfo'     => $orderId
        ]) ?>" alt="QR MoMo" class="qr my-4">

        <div class="alert alert-warning">
            <strong>Hướng dẫn:</strong><br>
            • Mở app <b>MoMo</b> → Quét mã QR<br>
            • Chuyển đúng <b><?= number_format($total,0,',','.') ?> ₫</b><br>
            • Nội dung: <span class="text-danger fw-bold"><?= $orderId ?></span><br><br>
            → Chúng tôi sẽ xác nhận ngay khi nhận được tiền!
        </div>

        <a href="/php-pj/index.php" class="btn btn-secondary w-100 py-3">Về trang chủ</a>
    </div>
</div>
</body>
</html>