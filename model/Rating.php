<?php
class Rating
{
    private $pdo;

    public function __construct($pdo) { //tạo đối tượng dpo để querry trực tiếp và mở kết nối db
        $this->pdo = $pdo;
    }
    public function getRating($product_id, $user_id, $rating) {
        $stmt = $this->pdo->prepare("SELECT * FROM rating WHERE product_id = ?,user_id = ?");
        $stmt->execute([$product_id, $user_id, $rating]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function addRating($product_id, $user_id, $rating) {
        $stmt = $this->pdo->prepare( "INSERT INTO rating (product_id, user_id, rating, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$product_id, $user_id, $rating]);
    }
    public function getAverageRating($product_id) {
        $stmt = $this->pdo->prepare("SELECT AVG(rating) as avg_rating FROM rating WHERE product_id = ?");
        $stmt->execute([$product_id]);
        return $stmt->fetchColumn();
    }
    public function checkRating($product_id,$user_id):bool {
        $stmt = $this->pdo->prepare("SELECT 1 FROM rating WHERE product_id = ? AND user_id = ?");
        $stmt->execute([$product_id, $user_id]);
        return (bool) $stmt->fetch();
    }

}

?>