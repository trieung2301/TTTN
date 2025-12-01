<?php
require_once __DIR__ . "/../../model/Cart.php";
require_once __DIR__ . "/../../model/Order.php";
require_once __DIR__ . "/../../model/Product.php";
require_once __DIR__ . "/../../model/OrderItems.php";
require_once __DIR__ . "/../../model/Coupon.php";
require_once __DIR__ . "/../../model/CouponUsage.php";

// PHPMailer
require_once __DIR__ . '/../../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CheckoutController {
    private $cartModel;
    private $orderModel;
    private $productModel;
    private $orderItems;
    private $couponModel;
    private $couponUsageModel;

    public function __construct($cartModel, $orderModel, $productModel, $orderItems, $couponModel, $couponUsageModel) {
        $this->cartModel = $cartModel;
        $this->orderModel = $orderModel;
        $this->productModel = $productModel;
        $this->orderItems = $orderItems;
        $this->couponModel = $couponModel;
        $this->couponUsageModel = $couponUsageModel;
    }

    public function index() {
        if (!isset($_SESSION['user'])) {
            header('Location: /php-pj/login');
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $cartItems = $this->cartModel->getCartItems($user_id);
        $totalItems = $this->cartModel->getTotalItems($user_id);

        if ($totalItems == 0) {
            header('Location: /php-pj/home');
            exit;
        }

        $total = 0;
        foreach ($cartItems as $item) $total += $item['price'] * $item['quantity'];

        $error = $_GET['error'] ?? '';
        $finalTotal = $total;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname     = trim($_POST['fullname']);
            $email        = trim($_POST['email']);
            $phone        = trim($_POST['phone']);
            $note         = trim($_POST['note'] ?? '');
            $address      = trim($_POST['address']);
            $district     = trim($_POST['district']);
            $city         = trim($_POST['city']);
            $postcode     = trim($_POST['postcode'] ?? '');
            $payment_method = $_POST['payment_method'] ?? 'COD';
            $couponCode     = trim($_POST['coupon'] ?? '');

            $discount = 0;
            if ($couponCode !== '') {
                $coupon = $this->couponModel->checkCoupon($couponCode);
                if ($coupon && !$this->couponUsageModel->checkUsed($user_id, $coupon['id'])) {
                    $discount = $coupon['discount_value'];
                    $finalTotal = $total - $discount;
                }
            }

            // Tạo đơn hàng → status = Chờ xác nhận, payment_status = Chưa thanh toán (cả COD và VNPay)
            $orderId = $this->orderModel->createOrder(
                $user_id, $fullname, $phone, $email,
                $address, $district, $city, $postcode,
                $note, $payment_method, $finalTotal
            );

            // Thêm chi tiết đơn hàng
            foreach ($cartItems as $item) {
                $this->orderItems->addOrderItem($orderId, $item['product_id'], $item['quantity'], $item['price']);
            }

            // Nếu chọn VNPay → chuyển sang trang thanh toán
            if ($payment_method === 'VNPay') {
                if ($discount > 0) {
                    $_SESSION['pending_coupon'] = $couponCode;
                }
                $this->vnpay_payment($orderId, $finalTotal);
                return;
            }

            // COD hoặc thanh toán thủ công
            if ($discount > 0) {
                $this->couponUsageModel->addUsage($user_id, $coupon['id']);
                $this->couponModel->updateUsageCount($couponCode);
            }

            $this->cartModel->clearCart($user_id);
            $this->sendOrderConfirmationEmail($orderId, $email, $cartItems, $total);
            header('Location: /php-pj/success?order=' . $orderId);
            exit;
        }

        require_once __DIR__ . '/../../view/site/checkout.php';
    }

    private function vnpay_payment($orderId, $finalTotal) {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_TmnCode = "5VCY53U1";
        $vnp_HashSecret = "0L0SM070YCOFLIXWU61DD1PDMKQL7YW7";
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost/php-pj/index.php?url=vnpay_return";
        $vnp_TxnRef = $orderId;
        $vnp_OrderInfo = "Thanh toan don hang #$orderId";
        $vnp_Amount = $finalTotal * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $startTime,
            "vnp_CurrCode" => "VND",
            "vnp_ExpireDate" => $expire,
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        ksort($inputData);
        $hashdata = "";
        $query = "";
        foreach ($inputData as $key => $value) {
            $hashdata .= urlencode($key) . "=" . urlencode($value) . '&';
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $hashdata = rtrim($hashdata, '&');

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        header('Location: ' . $vnp_Url);
        exit;
    }

    // XỬ LÝ KẾT QUẢ TRẢ VỀ TỪ VNPAY
    public function vnpay_return() {
        if (!isset($_SESSION['user'])) {
            header('Location: /php-pj/login');
            exit;
        }

        $vnp_HashSecret = "0L0SM070YCOFLIXWU61DD1PDMKQL7YW7";

        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);

        ksort($inputData);
        $hashData = "";
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $hashData = rtrim($hashData, '&');
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $orderId = (int)($_GET['vnp_TxnRef'] ?? 0);
        $responseCode = $_GET['vnp_ResponseCode'] ?? '';

        if ($secureHash === $vnp_SecureHash && $responseCode == '00') {
            // THANH TOÁN THÀNH CÔNG
            $this->orderModel->updatePaymentStatus($orderId, 'Đã thanh toán');
            // status vẫn là "Chờ xác nhận" → admin sẽ đổi sau

            // Xử lý coupon nếu có
            if (isset($_SESSION['pending_coupon'])) {
                $couponCode = $_SESSION['pending_coupon'];
                $coupon = $this->couponModel->checkCoupon($couponCode);
                if ($coupon) {
                    $this->couponUsageModel->addUsage($_SESSION['user']['id'], $coupon['id']);
                    $this->couponModel->updateUsageCount($couponCode);
                }
                unset($_SESSION['pending_coupon']);
            }

            // Trừ kho
            $orderItems = $this->orderItems->getOrderItemsByOrderId($orderId);
            foreach ($orderItems as $item) {
                $this->productModel->decreaseStock($item['product_id'], $item['quantity']);
            }

            // Xóa giỏ hàng
            $this->cartModel->clearCart($_SESSION['user']['id']);

            // Gửi email xác nhận
            $order = $this->orderModel->getOrderById($orderId);
            $this->sendOrderConfirmationEmail($orderId, $order['email'], $orderItems, $order['total']);

            header('Location: /php-pj/success?order=' . $orderId);
            exit;
        } else {
            // THANH TOÁN THẤT BẠI
            $this->orderModel->updatePaymentStatus($orderId, 'Chưa thanh toán');
            $this->orderModel->updateOrderStatus('Đã hủy (VNPay thất bại)', $orderId);

            header('Location: /php-pj/checkout?error=Thanh toán thất bại. Vui lòng thử lại.');
            exit;
        }
    }
    private function sendOrderConfirmationEmail($orderId, $email, $orderItems, $total) {
        $order = $this->orderModel->getOrderById($orderId);
        $fullname = $order['fullname'];
        $phone = $order['phone'];
        $address = $order['address'];
        $district = $order['district'];
        $city = $order['city'];
        $note = $order['note'] ?? 'Không có';
        $payment_method = $order['payment_method'];

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'trieung2301@gmail.com';
            $mail->Password = 'uirldfmozybzfdxd';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('trieung2301@gmail.com', 'WatchShop');
            $mail->addAddress($email, $fullname);
            $mail->isHTML(true);
            $mail->Subject = "Don hang #$orderId - WatchShop";

            $imageCids = [];
            foreach ($orderItems as $index => $item) {
                $imagePath = __DIR__ . '/../../view/image/' . basename($item['image']);
                if (file_exists($imagePath)) {
                    $cid = 'img_' . $index;
                    $mail->addEmbeddedImage($imagePath, $cid);
                    $imageCids[$index] = $cid;
                }
            }

            $itemsTable = "";
            foreach ($orderItems as $i => $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $imgHtml = isset($imageCids[$i]) 
                    ? "<img src='cid:{$imageCids[$i]}' width='70' style='border-radius:6px;' />"
                    : "<div style='width:70px;height:70px;background:#eee;display:flex;align-items:center;justify-content:center;font-size:11px;'>No image</div>";

                $itemsTable .= "<tr style='text-align:center;'>
                    <td style='padding:8px;'>".($i+1)."</td>
                    <td style='padding:8px;text-align:left;'><strong>{$item['name']}</strong></td>
                    <td style='padding:8px;'>$imgHtml</td>
                    <td style='padding:8px;'>".number_format($item['price'],0,',','.')." ₫</td>
                    <td style='padding:8px;'>{$item['quantity']}</td>
                    <td style='padding:8px;'><strong>".number_format($subtotal,0,',','.')." ₫</strong></td>
                </tr>";
            }

            $body = "
            <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;border:1px solid #ddd;padding:20px;border-radius:10px;'>
                <h2 style='color:#a12c8f;text-align:center;'>CẢM ƠN BẠN ĐÃ MUA HÀNG!</h2>
                <p>Xin chào <strong>$fullname</strong>,</p>
                <p>Đơn hàng của bạn đã được ghi nhận thành công!</p>
                <div style='background:#f9f9f9;padding:15px;border-radius:8px;'>
                    <p><strong>Mã đơn hàng:</strong> <span style='color:#e74c3c;font-size:1.3em;'>$orderId</span></p>
                    <p><strong>Tổng tiền:</strong> <strong style='color:#e67e22;font-size:1.2em;'>".number_format($total,0,',','.')." ₫</strong></p>
                    <p><strong>Phương thức:</strong> $payment_method</p>
                </div>
                <h3>Thông tin giao hàng</h3>
                <p>
                    Họ tên: $fullname<br>
                    SĐT: $phone<br>
                    Địa chỉ: $address, $district, $city<br>
                    Ghi chú: $note
                </p>
                <h3>Chi tiết sản phẩm</h3>
                <table style='width:100%;border-collapse:collapse;'>
                    <tr style='background:#f1f1f1;font-weight:bold;'>
                        <th>STT</th><th>Sản phẩm</th><th>Ảnh</th><th>Giá</th><th>SL</th><th>Thành tiền</th>
                    </tr>
                    $itemsTable
                    <tr>
                        <td colspan='5' style='text-align:right;padding:10px;font-weight:bold;'>TỔNG CỘNG:</td>
                        <td style='padding:10px;font-weight:bold;color:#e67e22;'>".number_format($total,0,',','.')." ₫</td>
                    </tr>
                </table>
                <p style='text-align:center;color:#7f8c8d;margin-top:30px;'>
                    WatchShop - Đồng hồ chính hãng giá tốt<br>
                    Hotline: 090xxxxxxx | Email: trieung2301@gmail.com
                </p>
            </div>";

            $mail->Body = $body;
            $mail->send();
        } catch (Exception $e) {
            error_log("Gửi mail thất bại (Đơn $orderId): " . $e->getMessage());
        }
    }
}
?>