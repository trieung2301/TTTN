<?php
class Comments {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    // Lấy comment theo sản phẩm
    public function getCommentByProduct(int $product_id): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM comments WHERE product_id = :product_id ORDER BY created_at DESC"
        );
        $stmt->execute([':product_id' => $product_id]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Lấy tất cả comment
    public function getAllComments(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM comments ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getComments(int $id):array{
        $stmt = $this->pdo->prepare("SELECT c.*, u.username  FROM comments c JOIN users u ON c.user_id = u.id WHERE product_id = :id ORDER BY created_at DESC");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addComments(int $product_id, int $user_id,string $comment_text){
        $stmt= $this->pdo->prepare("INSERT INTO comments (product_id, user_id, comment_text, created_at) VALUES (:product_id, :user_id, :comment_text, NOW())");
        return $stmt->execute([':product_id'=> $product_id,':user_id'=> $user_id,':comment_text'=> $comment_text]);
    }
}
?>
