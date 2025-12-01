<?php
require_once __DIR__ . "/../../model/User.php";

class AuthController {
    private User $userModel;
    public function __construct(User $userModel) {//gọi tới sẽ nhận từ model đã có kết nối pdo sẵn
        $this->userModel = $userModel;
    }
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {//nếu request gửi
            $username = $_POST['username'] ?? ''; //gán
            $password = $_POST['password'] ?? '';
            if (empty($username) || empty($password)){ //check
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin!";
                header("Location: /php-pj/login"); //redirect khi sai
                exit;
            }
            $userFromDB = $this->userModel->findByUsername($username);//gọi từ model kiểm tra có tk k
            if ($userFromDB && password_verify($password, $userFromDB['password'])) { // so sánh pass và hàm băm
                $role = $userFromDB['role'] ?? 'user';
                $_SESSION['user'] = ['id' => $userFromDB['id'], 'username' => $userFromDB['username'],'role' => $role]; //lưu session với cặp key id và username + thêm : lưu role để có thể chuyển sang admin
                $_SESSION['login-success'] = "Đăng nhập thành công!"; // lưu session thông báo
                if ($role === 'admin') {
                    header("Location: /php-pj/homeAdmin"); 
                } else {
                    header("Location: /php-pj/home");
                }
                exit;
            } else {
                $_SESSION['login-error'] = "Sai tên đăng nhập hoặc mật khẩu!";
            }
        }
        include __DIR__ . "/../../view/site/login.php";
    }
    public function logout(): void {
        session_unset(); // xóa các session đã lưu
        session_destroy();
        header("Location: /php-pj/login");
        exit;
    }
    public function register(): void{
        if($_SERVER['REQUEST_METHOD']=='POST') //lấy giá trị
        {
            $fullname=$_POST['fullname']??'';
            $username=$_POST['username']??'';
            $phone=$_POST['phone']??'';
            $email=$_POST['email']??'';
            $password=$_POST['password']??'';
            $confirmPassword=$_POST['confirm_password']??'';
            if($password != $confirmPassword) //kiểm tra pass đối chiếu
            {
                $_SESSION['regis-error']="Mật khẩu không khớp!";
                header("Location: /php-pj/register");
                exit;
            }
            if ($this->userModel->findByUsername($username)) { //kiểm tra user đã tồn tại chưa
                $_SESSION['regis-error'] = "Username đã tồn tại!";
                header("Location: /php-pj/register");
                exit;
            }
            $result =$this->userModel->register([ // gọi db để tạo , truyền vào cặp key value nên db phải gọi array
                'fullname'=> $fullname, 'username'=> $username, 'phone'=> $phone, 'email'=> $email, 'password'=> $password, 'role'=>'user'
            ]);
            if($result)
            {
                header('location: /php-pj/login');
            }
            else{
                $_SESSION['regis-error']='Thất bại';
                header('location: /php-pj/register');
            }
            

        }
        include __DIR__ . "/../../view/site/register.php";   
    }
}