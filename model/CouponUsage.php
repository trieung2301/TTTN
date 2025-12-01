<?php
class CouponUsage{
    private $pdo;
    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function checkUsed(int $user_id, int $coupon_id): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM coupon_usages WHERE coupon_id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$coupon_id, $user_id]);
        return $stmt->fetch() !== false;//nếu có trả về thì là true đã dùng, không thì false
        
    }
    public function addUsage(int $user_id, int $coupon_id): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO coupon_usages (user_id, coupon_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $coupon_id]);
    }

}
?>