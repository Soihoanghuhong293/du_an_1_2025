<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nạp cấu hình chung của ứng dụng
$config = require __DIR__ . '/config/config.php';

// Helper
require_once __DIR__ . '/src/helpers/helpers.php';
require_once __DIR__ . '/src/helpers/database.php';

// Models
require_once __DIR__ . '/src/models/User.php';

// Controllers
require_once __DIR__ . '/src/controllers/HomeController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/UserController.php';

// Khởi tạo controller
$homeController = new HomeController();
$authController = new AuthController();
$userController = new UserController();

// Lấy tham số act (mặc định '/')
$act = $_GET['act'] ?? '/';

// Router
match ($act) {

    // Trang welcome (chưa đăng nhập)
    '/', 'welcome' => $homeController->welcome(),

    // Trang home (đã đăng nhập)
    'home' => $homeController->home(),

    // ===============================
    // ⭐ ROUTER ĐĂNG NHẬP / ĐĂNG KÝ
    // ===============================
    'login' => $authController->login(),
    'register' => $authController->register(),
    'check-login' => $authController->checkLogin(),
    'handle-register' => $authController->handleRegister(),
    'logout' => $authController->logout(),

    // ===============================
    // ⭐ ROUTER QUẢN LÝ NGƯỜI DÙNG
    // ===============================
    'users' => $userController->index(),                     // danh sách người dùng
    'users/create' => $userController->create(),            // form tạo mới
    'users/store' => $userController->store(),              // xử lý lưu mới
    'users/edit' => $userController->edit(),                // form sửa
    'users/update' => $userController->update(),            // xử lý update
    'users/show' => $userController->detail(),             // xem chi tiết
    'users/delete' => $userController->delete(),            // xóa người dùng

    // 404
    default => $homeController->notFound(),
    
};
