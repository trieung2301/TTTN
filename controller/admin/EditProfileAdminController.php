<?php
require_once __DIR__ . "/../../model/User.php";

class EditProfileAdminController {
    private User $userModel;

    public function __construct(User $userModel) {
        $this->userModel = $userModel;
    }

    private function adminCheck(): void {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') { 
            header("Location: /php-pj/login");
            exit;
        }
    }

    public function editProfileAdmin(): void {
        $this->adminCheck();
        if (!isset($_SESSION['user'])) {
            header("Location: /php-pj/login");
            exit;
        }
        $userId = $_SESSION['user']['id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            if (empty($fullname) || empty($email)) {
                $_SESSION['profile-error'] = "Vui lòng nhập đầy đủ họ tên và email!";
                header("Location: /php-pj/editProfileAdmin");
                exit;
            }

            if (!empty($password) && $password !== $confirmPassword) {
                $_SESSION['profile-error'] = "Mật khẩu xác nhận không khớp!";
                header("Location: /php-pj/editProfileAdmin");
                exit;
            }
            $data = [
                'fullname' => $fullname,
                'email'    => $email,
                'phone'    => $phone
            ];
            if (!empty($password)) {
                $data['password'] = $password;
            }
            $result = $this->userModel->updateProfile($userId, $data);

            if ($result) {
                $_SESSION['profile-success'] = "Cập nhật thành công!";
            } else {
                $_SESSION['profile-error'] = "Cập nhật thất bại!";
            }
            header("Location: /php-pj/editProfileAdmin");
            exit;
        }
        $user = $this->userModel->getById($userId);
        include __DIR__ . "/../../view/admin/editProfileAdmin.php";
    }
}
