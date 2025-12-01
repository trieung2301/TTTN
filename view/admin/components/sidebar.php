<?php
if (!function_exists('isActive')) {
    function isActive($actionName, $currentAction) {
        if ($actionName === $currentAction) {
            return 'active';
        }
        return '';
    }
}
$base_path = '/php-pj/';
$current_action = trim(str_replace($base_path, '', strtok($_SERVER['REQUEST_URI'], '?')), '/');

?>
<div class="sidebar text-white p-3">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo isActive('homeAdmin', $current_action); ?>" 
                href="/php-pj/homeAdmin">
                <i class="fa-solid fa-tachometer-alt"></i> Trang chủ 
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo isActive('admin/users', $current_action); ?>" 
            href="/php-pj/admin/users">
            <i class="fa-solid fa-users"></i> Quản lý Khách hàng
        </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo isActive('admin/staff', $current_action); ?>" 
            href="/php-pj/admin/staff">
            <i class="fa-solid fa-user-tie"></i> Quản lý Nhân viên
        </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo isActive('admin/products', $current_action); ?>" 
            href="/php-pj/admin/products">
            <i class="fa-solid fa-box-open"></i> Quản lý Sản phẩm
        </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo isActive('admin/discounts', $current_action); ?>" 
            href="/php-pj/admin/discounts">
                <i class="fa-solid fa-percent"></i> Quản lý Giảm giá
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo isActive('admin/orders', $current_action); ?>" 
                href="/php-pj/admin/orders">
                <i class="fa-solid fa-shopping-cart"></i> Quản lý đơn hàng
            </a>
        </li>
    </ul>
</div>