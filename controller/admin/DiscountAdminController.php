<?php
require_once __DIR__ . "/../../model/Coupon.php"; 

class DiscountAdminController {
    private Coupon $couponModel;

    public function __construct(Coupon $couponModel) {
        $this->couponModel = $couponModel;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function adminCheck(): void {
        if (($_SESSION['user']['role'] ?? 'user') !== 'admin') { 
            header("Location: /php-pj/login");
            exit;
        }
    }

    public function getCoupon() {
        $this->adminCheck();
        
        $coupons = $this->couponModel->getAll();

        foreach ($coupons as &$coupon) {
            $isExpired = strtotime($coupon['expires_at']) < time();
            $isUsedUp = $coupon['used_count'] >= $coupon['usage_limit'];
            $currentStat = $coupon['status'] ?? 0; 
            
            $coupon['status_class'] = 'bg-secondary';
            $coupon['status_text'] = 'Đã tắt';
            
            if ($currentStat == 1) {
                if ($isExpired) {
                    $coupon['status_class'] = 'bg-danger';
                    $coupon['status_text'] = 'Hết hạn';
                } elseif ($isUsedUp) {
                    $coupon['status_class'] = 'bg-danger';
                    $coupon['status_text'] = 'Hết lượt dùng';
                } else {
                    $coupon['status_class'] = 'bg-success';
                    $coupon['status_text'] = 'Đang hoạt động';
                }
            }
        }
        unset($coupon);

        $old_data = $_SESSION['old_data'] ?? [];
        unset($_SESSION['old_data']);
        
        include __DIR__ . "/../../view/admin/discountAdmin.php";
    }
    
    public function add() {
        $this->adminCheck();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');
            $value = (float)($_POST['discount_value'] ?? 0);
            $expires = trim($_POST['expires_at'] ?? '');
            $limit = (int)($_POST['usage_limit'] ?? 1);
            $status = (int)($_POST['status'] ?? 0);

            $_SESSION['old_data'] = ['code' => $code, 'discount_value' => $value, 'expires_at' => $expires, 'usage_limit' => $limit, 'status' => $status];

            if (empty($code) || $value <= 0 || empty($expires)) {
                $_SESSION['error_message'] = "Thiếu thông tin hoặc giá trị không hợp lệ.";
            } 
            elseif ($this->couponModel->getCoupon($code)) {
                $_SESSION['error_message'] = "Mã đã tồn tại.";
            } 
            elseif ($this->couponModel->addCoupon($code, $value, $expires, $limit, $status)) {
                $_SESSION['success_message'] = "Thêm mã '{$code}' thành công!";
                unset($_SESSION['old_data']); 
            } else {
                $_SESSION['error_message'] = "Lỗi hệ thống khi thêm mã.";
            }
            
            header("Location: /php-pj/admin/discounts");
            exit;
        }
        $this->getCoupon();
    }

    public function update() {
        $this->adminCheck();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');
            $originalCode = trim($_POST['original_code'] ?? $code); 
            $value = (float)($_POST['discount_value'] ?? 0);
            $expires = trim($_POST['expires_at'] ?? '');
            $limit = (int)($_POST['usage_limit'] ?? 1);
            $status = (int)($_POST['status'] ?? 0);

            if (empty($code) || $value <= 0 || empty($expires)) {
                $_SESSION['error_message'] = "Thiếu thông tin hoặc giá trị không hợp lệ.";
            } else {
                $codeToUpdate = $originalCode; 

                if ($this->couponModel->updateCoupon($codeToUpdate, $value, $expires, $limit, $status)) { 
                    $_SESSION['success_message'] = "Cập nhật mã '{$originalCode}' thành công!";
                } else {
                    $_SESSION['error_message'] = "Lỗi cập nhật mã.";
                }
            }
        }
        header("Location: /php-pj/admin/discounts");
        exit;
    }

    public function toggleStatus() {
        $this->adminCheck();
        
        $code = trim($_POST['code'] ?? '');
        $action = trim($_POST['action_type'] ?? ''); 

        if (empty($code)) {
            $_SESSION['error_message'] = "Mã không hợp lệ.";
        } elseif ($action === 'enable') {
            if ($this->couponModel->enableCoupon($code)) {
                $_SESSION['success_message'] = "Kích hoạt mã '{$code}' thành công!";
            } else {
                $_SESSION['error_message'] = "Lỗi kích hoạt.";
            }
        } elseif ($action === 'disable') {
            if ($this->couponModel->disableCoupon($code)) {
                $_SESSION['success_message'] = "Vô hiệu hóa mã '{$code}' thành công!";
            } else {
                $_SESSION['error_message'] = "Lỗi vô hiệu hóa.";
            }
        }

        header("Location: /php-pj/admin/discounts");
        exit;
    }
}
?>