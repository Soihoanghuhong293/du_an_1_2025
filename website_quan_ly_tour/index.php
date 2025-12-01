<?php
// BẬT HIỂN THỊ LỖI
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//index.php
define('BASE_PATH', __DIR__);

// Nạp cấu hình chung của ứng dụng
$config = require __DIR__ . '/config/config.php';

// Helper
require_once __DIR__ . '/src/helpers/helpers.php';
require_once __DIR__ . '/src/helpers/database.php';

// Models
require_once __DIR__ . '/src/models/User.php';
require_once __DIR__ . '/src/models/TourModel.php';

// Controllers
require_once __DIR__ . '/src/controllers/HomeController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/TourController.php';

// Khởi tạo controller
$homeController = new HomeController();
$authController = new AuthController();
$tourController = new TourController();

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

    // ===============================
    // ⭐ ROUTER QUẢN LÝ TOUR (BỔ SUNG)
    // ===============================

    // 1. Hiển thị danh sách tour
    'tour' => $tourController->index(), 

    // 2. Form thêm tour & xử lý thêm tour
    'tour-add' => $tourController->add(), 

    // 3. Form sửa tour & xử lý sửa tour. Lấy id từ URL: ?act=tour-edit&id=123
    'tour-edit' => $tourController->edit($_GET['id'] ?? null), 

    // 4. Xử lý xóa tour. Lấy id từ URL: ?act=tour-delete&id=123
    'tour-delete' => $tourController->delete($_GET['id'] ?? null),
    
};
