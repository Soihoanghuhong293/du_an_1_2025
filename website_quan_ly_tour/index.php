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

// Khởi tạo controller
$homeController = new HomeController();
$authController = new AuthController();

// Lấy tham số act (mặc định '/')
$act = $_GET['act'] ?? '/';

match ($act) {

    // Trang welcome (chưa đăng nhập)
    '/', 'welcome' => $homeController->welcome(),

    // Trang home
    'home' => $homeController->home(),

    // ===============================
    // ⭐ ROUTER ĐĂNG NHẬP / ĐĂNG KÝ
    // ===============================

    // Form đăng nhập
    'login' => $authController->login(),

    // Form đăng ký mới (⭐ bạn thêm dòng này)
    'register' => $authController->register(),

    // Xử lý đăng nhập
    'check-login' => $authController->checkLogin(),

    // Xử lý đăng ký (⭐ bạn thêm dòng này)
    'handle-register' => $authController->handleRegister(),

    // Đăng xuất
    'logout' => $authController->logout(),

    // 404
    default => $homeController->notFound(),
    
};
