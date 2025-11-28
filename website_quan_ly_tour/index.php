<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Nạp cấu hình chung của ứng dụng
$config = require __DIR__ . '/config/config.php';

<<<<<<< HEAD
// Helpers
require_once __DIR__ . '/src/helpers/helpers.php';
require_once __DIR__ . '/src/helpers/database.php';

// Model
=======
// Helper
require_once __DIR__ . '/src/helpers/helpers.php';
require_once __DIR__ . '/src/helpers/database.php';

// Models
>>>>>>> 11f51ec63f7155b0a02a45c49afa1511a1026c59
require_once __DIR__ . '/src/models/User.php';

// Controllers
require_once __DIR__ . '/src/controllers/HomeController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/AdminController.php';

// Khởi tạo controller
$homeController = new HomeController();
$authController = new AuthController();
$adminController = new AdminController();

<<<<<<< HEAD
// Xác định route
=======
// Lấy tham số act (mặc định '/')
>>>>>>> 11f51ec63f7155b0a02a45c49afa1511a1026c59
$act = $_GET['act'] ?? '/';

match ($act) {

<<<<<<< HEAD
    '/', 'welcome' => $homeController->welcome(),

    'home' => $homeController->home(),

=======
    // Trang welcome (chưa đăng nhập)
    '/', 'welcome' => $homeController->welcome(),

    // Trang home
    'home' => $homeController->home(),

    // ===============================
    // ⭐ ROUTER ĐĂNG NHẬP / ĐĂNG KÝ
    // ===============================

    // Form đăng nhập
>>>>>>> 11f51ec63f7155b0a02a45c49afa1511a1026c59
    'login' => $authController->login(),

    // Form đăng ký mới (⭐ bạn thêm dòng này)
    'register' => $authController->register(),

    // Xử lý đăng nhập
    'check-login' => $authController->checkLogin(),

    // Xử lý đăng ký (⭐ bạn thêm dòng này)
    'handle-register' => $authController->handleRegister(),

    // Đăng xuất
    'logout' => $authController->logout(),

<<<<<<< HEAD
    //
    // Route admin
    //
    'admin' => (function () use ($adminController) {
        requireLogin();
        if (!isAdmin()) {
            header('location:' . BASE_URL);
            exit;
        }
        $adminController->dashboard();
    })(),

    'admin/dashboard' => (function () use ($adminController) {
        requireLogin();
        if (!isAdmin()) {
            header('location:' . BASE_URL);
            exit;
        }
        $adminController->dashboard();
    })(),

=======
    // 404
>>>>>>> 11f51ec63f7155b0a02a45c49afa1511a1026c59
    default => $homeController->notFound(),
    
};
