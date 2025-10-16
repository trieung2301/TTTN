<?php
class Category {
    private $pdo;

    public function __construct($pdo) { 
        // Nhận đối tượng PDO
        $this->pdo = $pdo;
    }

    // Lấy tất cả danh mục
    public function getAllCategories(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM categories");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    // Tạo danh mục mới
    public function create($name, $description, $status): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (name, description, status, created_at, updated_at)
             VALUES (?, ?, ?, NOW(), NOW())"
        );
        return $stmt->execute([$name, $description, $status]);
    }

    // Xoá danh mục
    public function delete($id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Cập nhật danh mục
    public function update($name, $description, $status, $id): bool {
        $stmt = $this->pdo->prepare("UPDATE categories SET name = ?, description = ?, status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$name, $description, $status, $id]);
    }
}