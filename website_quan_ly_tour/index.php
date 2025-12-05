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
require_once __DIR__ . '/src/controllers/BookingController.php';
require_once __DIR__ . '/src/controllers/CategoryController.php';
require_once __DIR__ . '/src/controllers/UserController.php';

// Khởi tạo controller
$homeController = new HomeController();
$authController = new AuthController();
$tourController = new TourController();
$bookingController = new BookingController();
$categoryController = new CategoryController();// Lấy tham số act (mặc định '/')
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
    // Trang danh sách booking
'bookings' => $bookingController->index(),
'booking-create' => $bookingController->create(),

    // 2. Xử lý lưu dữ liệu (khi bấm nút Submit)
    'booking-store'  => $bookingController->store(),
    'booking-delete' => $bookingController->delete($_GET['id'] ?? null),




 'users' => $userController->index(),

 'categories' => $categoryController->index(),
  'category-delete' => $categoryController->delete($_GET['id'] ?? null),


  


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
    'category-add' => $categoryController->add(),
    'category-edit' => $categoryController->edit($_GET['id'] ?? null),

    
    // ===============================
    // ⭐ ROUTER QUẢN LÝ NGƯỜI DÙNG
    // ===============================
    'user' => $userController->index(),                     // danh sách người dùng
    'users/create' => $userController->create(),            // form tạo mới
    'users/store' => $userController->store(),              // xử lý lưu mới
    'users/edit' => $userController->edit(),                // form sửa
    'users/update' => $userController->update(),            // xử lý update
    'users/show' => $userController->detail(),             // xem chi tiết
    'users/delete' => $userController->delete(),            // xóa người dùng

    // 404
    default => $homeController->notFound(),
    
};
