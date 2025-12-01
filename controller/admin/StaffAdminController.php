<?php
require_once __DIR__ . "/../../model/User.php";

class StaffAdminController {
    private User $userModel;

    public function __construct(User $userModel) {
        $this->userModel = $userModel;
    }

    private function getCurrentAdminId(): int {
        return $_SESSION['user']['id'] ?? 0;
    }

    private function adminCheck(): void {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') { 
            header("Location: /php-pj/login");
            exit;
        }
    }

    protected function checkStaffRole(?array $user): void {
        if (!$user || ($user['role'] ?? 'N/A') !== 'admin') {
            $_SESSION['error_message'] = "Không tìm thấy Nhân viên hoặc ID không hợp lệ.";
            header("Location: /php-pj/admin/staff");
            exit;
        }
    }

    public function getStaff(): void { 
        $this->adminCheck();
        $searchTerm = trim($_GET['search'] ?? '');
        $allUsers = $this->userModel->getAll(); 

        $staff = [];
        foreach ($allUsers as $user) {
            if ($user['role'] === 'admin') { 
                $isMatch = empty($searchTerm) || stripos($user['fullname'] ?? '', $searchTerm) !== false
                                              || stripos($user['username'] ?? '', $searchTerm) !== false;
                if ($isMatch) {
                    $staff[] = $user;
                }
            }
        }
        include __DIR__ . "/../../view/admin/staffsAdmin.php"; 
    }
    
    public function createStaff(): void {
        $this->adminCheck();
        
        $oldInput = $_SESSION['old_input'] ?? [];
        $error_message = $_SESSION['error_message'] ?? '';
        
        unset($_SESSION['old_input']); //unset huỷ bỏ old_input
        unset($_SESSION['error_message']);
        
        $actionUrl = 'admin/staff/addStaff';
        
        include __DIR__ . '/../../view/admin/staffAdd.php'; 
    }
    
    public function addStaff(): void {
        $this->adminCheck();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /php-pj/admin/staff");
            exit;
        }
        
        $userData = [
            'fullname' => trim($_POST['fullname'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'phone' => trim($_POST['phone'] ?? ''),
            'role' => 'admin', 
        ];
        
        $_SESSION['old_input'] = $userData;
        
        $errorMessage = '';
        if (empty($userData['username']) || empty($userData['password'])) {
            $errorMessage = "Tên đăng nhập và Mật khẩu không được để trống.";
        } 
        elseif ($this->userModel->findByUsername($userData['username'])) {
            $errorMessage = "Tên đăng nhập đã tồn tại, vui lòng chọn tên khác.";
        }
        elseif (strlen($userData['password']) < 6) {
            $errorMessage = "Mật khẩu phải có tối thiểu 6 ký tự.";
        }
        elseif ($userData['password'] !== ($_POST['confirm_password'] ?? '')) {
            $errorMessage = "Lỗi xác nhận: Mật khẩu xác nhận không khớp.";
        }
        
        if (!empty($errorMessage)) {
            $_SESSION['error_message'] = $errorMessage;
            header("Location:/php-pj/admin/staff/add");
            exit;
        }
        
        $result = $this->userModel->register($userData); 

        if ($result === true) { 
            $_SESSION['success_message'] = "Thêm nhân viên **{$userData['username']}** thành công! ✅";
            unset($_SESSION['old_input']);
            header("Location: /php-pj/admin/staff");
            exit;
        } else {
            error_log("Lỗi DB khi thêm nhân viên: " . print_r($result, true)); 
            $_SESSION['error_message'] = "Lỗi khi thêm nhân viên vào cơ sở dữ liệu. Vui lòng kiểm tra Log.";
            header("Location: /php-pj/admin/staff/add");
            exit;
        }
    }
    
    public function editStaff(): void {
        $this->adminCheck();
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->getById($id); 
        
        $this->checkStaffRole($user); 
        
        include __DIR__ . "/../../view/admin/staffEdit.php"; 
    }

    public function updateStaff(): void {
        $this->adminCheck();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /php-pj/admin/staff");
            exit;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $userOld = $this->userModel->getById($id);
        
        $errorMessage = '';
        if (!$userOld) {
            $errorMessage = "Không tìm thấy thông tin người dùng cần cập nhật.";
        } 
        elseif ($id === $this->getCurrentAdminId()) {
             $errorMessage = "Không thể cập nhật. Bạn không thể tự sửa tài khoản của chính mình.";
        }
        
        if (!empty($errorMessage)) {
            $_SESSION['error_message'] = $errorMessage;
            header("Location: /php-pj/admin/staff"); 
            exit;
        }

        $newRole = trim($_POST['role'] ?? $userOld['role']); 
        
        $userData = [
            'id' => $id,
            'fullname' => trim($_POST['fullname'] ?? $userOld['fullname']),
            'username' => trim($_POST['username'] ?? $userOld['username']),
            'email' => trim($_POST['email'] ?? $userOld['email']),
            'phone' => trim($_POST['phone'] ?? $userOld['phone']),
            'role' => $newRole,
        ];
        
        if ($this->userModel->updateInfo($userData)) { 
            $_SESSION['success_message'] = "Cập nhật người dùng ID: {$id} thành công! (Vai trò mới: {$newRole}) ✅";
        } else {
            $_SESSION['error_message'] = "Lỗi khi cập nhật người dùng.";
        }
        
        header("Location: /php-pj/admin/staff"); 
        exit;
    }

    public function changeStaffPassword(): void {
        $this->adminCheck();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /php-pj/admin/staff");
            exit;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $newPassword = $_POST['new_password'] ?? '';
        $userOld = $this->userModel->getById($id);
        
        $errorMessage = '';
        
        if ($id <= 0 || strlen($newPassword) < 6) { 
            $errorMessage = "ID không hợp lệ hoặc mật khẩu quá ngắn (tối thiểu 6 ký tự).";
        } 
        elseif (!$userOld || $userOld['role'] !== 'admin') {
            $errorMessage = "Không tìm thấy Nhân viên hoặc ID không hợp lệ.";
        }
        
        if (!empty($errorMessage)) {
            $_SESSION['error_message'] = $errorMessage;
            header("Location: /php-pj/admin/staff");
            exit;
        }
        
        if ($this->userModel->updatePassword($id, $newPassword)) {
            $_SESSION['success_message'] = "Đổi mật khẩu cho nhân viên ID: {$id} thành công! ✅";
        } else {
            $_SESSION['error_message'] = "Lỗi khi cập nhật mật khẩu.";
        }
        
        header("Location: /php-pj/admin/staff");
        exit;
    }
    
    public function deleteStaff(): void {
        $this->adminCheck();
        
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->getById($id);
        
        $errorMessage = '';
        if (!$user || $user['role'] !== 'admin') {
            $errorMessage = "Không tìm thấy Nhân viên hoặc ID không hợp lệ.";
        }
        elseif ($id === $this->getCurrentAdminId()) {
            $errorMessage = "Bạn không thể tự hạ cấp tài khoản của chính mình.";
        } 
        
        if (!empty($errorMessage)) {
            $_SESSION['error_message'] = $errorMessage;
            header("Location: /php-pj/admin/staff");
            exit;
        }
        
        $userDataToUpdate = [
            'id' => $id,
            'fullname' => $user['fullname'] ?? '',
            'username' => $user['username'] ?? '',
            'email' => $user['email'] ?? '',
            'phone' => $user['phone'] ?? '',
            'role' => 'user', 
        ];

        if ($this->userModel->updateInfo($userDataToUpdate)) {
            $_SESSION['success_message'] = "Hạ cấp nhân viên ID: **{$id}** thành Khách hàng (User) thành công! ✅";
        } else {
            $_SESSION['error_message'] = "Lỗi khi hạ cấp nhân viên.";
        }
        
        header("Location: /php-pj/admin/staff");
        exit;
    }
    
    public function toggleStaffStatus(): void { // khoá/mở khoá nhân viên
        $this->adminCheck();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /php-pj/admin/staff");
            exit;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $currentStatus = (int)($_POST['status'] ?? 0); 
        $newStatus = $currentStatus == 0 ? 1 : 0; 
        
        $userOld = $this->userModel->getById($id);
        
        $errorMessage = '';
        if (!$userOld || $userOld['role'] !== 'admin') {
            $errorMessage = "Không tìm thấy Nhân viên hoặc ID không hợp lệ.";
        } 
        elseif ($id === $this->getCurrentAdminId()) {
            $errorMessage = "Bạn không thể tự khóa/mở khóa tài khoản của chính mình.";
        }
        
        if (!empty($errorMessage)) {
            $_SESSION['error_message'] = $errorMessage;
            header("Location:/php-pj/admin/staff");
            exit;
        }

        if ($this->userModel->updateStatus($id, $newStatus)) {
             $action = $newStatus == 0 ? 'mở khóa' : 'khóa'; 
            $_SESSION['success_message'] = "Đã {$action} tài khoản nhân viên ID: {$id} thành công! ✅";
        } else {
            $_SESSION['error_message'] = "Lỗi khi cập nhật trạng thái nhân viên.";
        }
        
        header("Location: /php-pj/admin/staff");
        exit;
    }
}