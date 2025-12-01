<?php
include __DIR__ . "/components/header.php";
include __DIR__ . "/components/navbar.php";
include __DIR__ . "/components/sidebar.php";

$ss='$_SESSION';
$err=$ss . "['error_message']";$suc=$ss . "['success_message']";
$E=$$err??'';$S=$$suc??'';$C=$error_message??'';
unset($$err,$$suc);
?>

<div class="main-content">
    <div class="container-fluid">
        <h1 class="mb-4 text-dark"><i class="fa-solid fa-user-plus"></i> Thêm Nhân viên Mới</h1>
        
        <?php if($C||$E):?><div class="alert alert-danger"><?=htmlspecialchars($C?:$E)?></div><?php endif;?>
        <?php if($S):?><div class="alert alert-success"><?=htmlspecialchars($S)?></div><?php endif;?>
            
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="m-0 font-weight-bold">Thông tin Nhân viên</h6>
            </div>
            <div class="card-body">
                
                <form action="/php-pj/<?=htmlspecialchars($actionUrl)?>" method="POST"> 
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fullname" class="form-label">Họ tên</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" 
                                value="<?=htmlspecialchars($oldInput['fullname']??'')?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Tên đăng nhập (*)</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                value="<?=htmlspecialchars($oldInput['username']??'')?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                value="<?=htmlspecialchars($oldInput['email']??'')?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                value="<?=htmlspecialchars($oldInput['phone']??'')?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Mật khẩu (*)</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận Mật khẩu (*)</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
                            <div class="invalid-feedback" id="pass-match-feedback">Mật khẩu xác nhận không khớp.</div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="/php-pj/admin/staff" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Quay lại Danh sách
                        </a>
                        <button type="submit" class="btn btn-success" id="submitAddUser">
                            <i class="fa-solid fa-save"></i> Lưu Nhân viên
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>