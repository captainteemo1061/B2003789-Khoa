<?php
// Khởi tạo session
session_start();

// Xóa tất cả dữ liệu trong session
session_unset();

// Hủy session
session_destroy();

// Xóa cookie (nếu có)
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Chuyển hướng đến trang đăng nhập hoặc trang chính
header("Location: log.php");
exit();
?>