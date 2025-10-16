<?php
class User {
    private PDO $pdo;

    public function __construct(PDO $pdo) { //tạo đối tượng dpo để querry trực tiếp và mở kết nối db
        $this->pdo = $pdo;
    }

    public function findByUsername(string $username): ?array { //trả về array or null
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1"); //lệnh prepare chuẩn bị code
        $stmt->execute([$username]); //truyền đối tượng vào prepare và thực thi
        $user = $stmt->fetch(); // thực thi xong lấy data về
        return $user ? $user : null; //trả user if có user k thì nul
    }
    public function register(array $userData): bool { //thành công thì true và ngc lại
        $hash = password_hash($userData['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (fullname, username, email, password, phone, role, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $userData['fullname'],
            $userData['username'],
            $userData['email'],
            $hash,
            $userData['phone'],
            $userData['role'] ?? 'user'
        ]);
    }
    public function getAll(): array {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);// trả về mảng key value
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ? $user : null;
    }
    // public function update(int $id, array $userData): bool {
    //     $hash = password_hash($userData['password'], PASSWORD_DEFAULT);// chuyển đổi thành mã băm, tham số 1 là pass usser input 2 là hàm thực thi băm
    //     $sql = "UPDATE users SET 
    //             fullname = ?, username = ?, email = ?, password = ?, phone = ?, role = ?, updated_at = NOW()
    //             WHERE id = ?";
    //     $stmt = $this->pdo->prepare($sql);
    //     return $stmt->execute([
    //         $userData['fullname'],
    //         $userData['username'],
    //         $userData['email'],
    //         $hash,
    //         $userData['phone'],
    //         $userData['role'],
    //         $userData['id']
    //     ]);
    // }
    public function updateProfile(int $id, array $data): bool {
        if (!empty($data['password'])) {
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users 
                    SET fullname = ?, email = ?, phone = ?, password = ?, updated_at = NOW()
                    WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['fullname'],
                $data['email'],
                $data['phone'],
                $hash,
                $id
            ]);
        } else {
            $sql = "UPDATE users 
                    SET fullname = ?, email = ?, phone = ?, updated_at = NOW()
                    WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['fullname'],
                $data['email'],
                $data['phone'],
                $id
            ]);
        }
    }

    public function updateWithoutPassword(array $userData): bool {
        $sql = "UPDATE users SET 
                fullname = ?, username = ?, email = ?, phone = ?, role = ?, updated_at = NOW()
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $userData['fullname'],
            $userData['username'],
            $userData['email'],
            $userData['phone'],
            $userData['role'],
            $userData['id']
        ]);
    }
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function countAll(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM users");
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    public function checkRole():bool{
        $stmt = $this->pdo->query("SELECT role FROM users");
        $result=$stmt->fetch();
        if($result['role']=='admin'){
            return true;
        }
        return false;
    }

    // thêm
    public function updateInfo(array $userData): bool {
        $sql = "UPDATE users SET 
                    fullname = ?, 
                    username = ?, 
                    email = ?, 
                    phone = ?, 
                    role = ?, 
                    updated_at = NOW()
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            $userData['fullname'],
            $userData['username'],
            $userData['email'],
            $userData['phone'],
            $userData['role'], 
            $userData['id']
        ]);
    }
    public function updatePassword(int $id, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET 
                    password = ?, 
                    updated_at = NOW()
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        
        // Truyền $hash trước, sau đó là $id
        return $stmt->execute([
            $hash,
            $id
        ]);
    }

    //===================================================================
    public function calculateTotalSpent(int $userId): float { // tính tổng chi tiêu nếu đơn hàng có trạng thái Giao thành công thì mới tính
        $sql = "SELECT SUM(total) 
                FROM orders 
                WHERE user_id = ? AND status = 'Giao thành công'"; 
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $total = $stmt->fetchColumn();
        return (float)($total ?? 0.0); 
    }

    
    public function updateLevel(int $userId, float $totalSpent, string $level): bool { // update level tổng chi tiêu
        $sql = "UPDATE users SET total_spent = ?, level = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$totalSpent, $level, $userId]);
    }


    public function determineLevel(float $totalSpent): string { // xác định level dựa trên số tiền đã tiêu
        if ($totalSpent >= 100000000) {  // 100tr
            return 'Diamond';
        } elseif ($totalSpent >= 50000000) { // 50tr
            return 'Gold';
        } elseif ($totalSpent >= 10000000) { // 10tr
            return 'Silver';
        } else {
            return 'Common'; // thấp hơn 10tr mặc định là Common
        }
    }

    public function updateStatus(int $id, int $status): bool { // update status
        $sql = "UPDATE users SET 
                    status = ?, 
                    updated_at = NOW()
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            $status,
            $id
        ]);
    }
}