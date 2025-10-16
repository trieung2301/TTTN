<?php
class Coupon {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Lấy thông tin coupon theo mã
    public function getCoupon(string $couponCode) {
        $stmt = $this->pdo->prepare("SELECT * FROM coupons WHERE code = ?");
        $stmt->execute([$couponCode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function checkCoupon(string $couponCode) {
        $stmt = $this->pdo->prepare("SELECT * FROM coupons WHERE code = ? AND status= 1 AND used_count < usage_limit AND NOW() < expires_at");
        $stmt->execute([$couponCode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateUsageCount(string $couponCode) {
        $stmt = $this->pdo->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE code = ? AND used_count < usage_limit");
        $stmt->execute([$couponCode]);
    }
    public function increaseCouponCount(string $couponCode) {
        $stmt = $this->pdo->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE code = ?");
        $stmt->execute([$couponCode]);
    }

    // Thêm coupon mới
    // public function addCoupon(string $couponCode, string $discountType, float $discountValue, string $expiresAt, int $usageLimit, int $status): bool {
    //     $stmt = $this->pdo->prepare( "INSERT INTO coupons (code, discount_type, discount_value, expires_at, usage_limit, status) VALUES (?, ?, ?, ?, ?, ?)");
    //     return $stmt->execute([$couponCode, $discountType, $discountValue, $expiresAt, $usageLimit, $status]);
    // }
    public function addCoupon(string $couponCode, float $discountValue, string $expiresAt, int $usageLimit, int $status): bool {
        $stmt = $this->pdo->prepare( "INSERT INTO coupons (code, discount_value, expires_at, usage_limit, status) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$couponCode, $discountValue, $expiresAt, $usageLimit, $status]);
    }



    // Cập nhật coupon
    // public function updateCoupon(string $couponCode, string $discountType, float $discountValue, string $expiresAt, int $usageLimit, int $status): bool {
    //     $stmt = $this->pdo->prepare( "UPDATE coupons SET code = ?, discount_type = ?, discount_value = ?, expires_at = ?, usage_limit = ?, status = ?  WHERE code = ?");
    //     return $stmt->execute([$couponCode, $discountType, $discountValue, $expiresAt, $usageLimit, $status, $couponCode]);
    // }
    public function updateCoupon(string $couponCode, float $discountValue, string $expiresAt, int $usageLimit, int $status): bool {
        $stmt = $this->pdo->prepare( "UPDATE coupons SET code = ?, discount_value = ?, expires_at = ?, usage_limit = ?, status = ? WHERE code = ?");
        return $stmt->execute([$couponCode, $discountValue, $expiresAt, $usageLimit, $status, $couponCode]);
    }
    
    // Tắt coupon
    public function disableCoupon(string $couponCode): bool {
        $stmt = $this->pdo->prepare("UPDATE coupons SET status = 0 WHERE code = ?");
        return $stmt->execute([$couponCode]);
    }

    // Bật coupon
    public function enableCoupon(string $couponCode): bool {
        $stmt = $this->pdo->prepare("UPDATE coupons SET status = 1 WHERE code = ?");
        return $stmt->execute([$couponCode]);
    }

    // Lấy tất cả coupon
    // public function getAll(): array {
    //     $stmt = $this->pdo->prepare("SELECT * FROM coupons ORDER BY created_at DESC");
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }
    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM coupons");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
