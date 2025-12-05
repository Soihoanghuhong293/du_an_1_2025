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
$categoryController = new CategoryController();
$userController = new UserController();

// Lấy tham số act (mặc định '/')
$act = $_GET['act'] ?? '/';

// ===============================
// ⭐ ROUTER CHÍNH DUY NHẤT
// ===============================
match ($act) {

    '/', 'welcome' => $homeController->welcome(),

    'home' => $homeController->home(),

    // Auth
    'login' => $authController->login(),
    'register' => $authController->register(),
    'check-login' => $authController->checkLogin(),
    'handle-register' => $authController->handleRegister(),
    'logout' => $authController->logout(),

    // Booking
    'bookings' => $bookingController->index(),
    'booking-create' => $bookingController->create(),
    'booking-store'  => $bookingController->store(),

    // Category
    'categories' => $categoryController->index(),
    'category-add' => $categoryController->add(),
    'category-edit' => $categoryController->edit($_GET['id'] ?? null),
    'category-delete' => $categoryController->delete($_GET['id'] ?? null),

    // ⭐ TOUR
    'tours' => $tourController->index(),
    'tour-add' => $tourController->add(),
    'tour-edit' => $tourController->edit($_GET['id'] ?? null),
    'tour-delete' => $tourController->delete($_GET['id'] ?? null),
    'tour-detail' => $tourController->detail(),

    // User
    'user' => $userController->index(),
    'users/create' => $userController->create(),
    'users/store' => $userController->store(),
    'users/edit' => $userController->edit(),
    'users/update' => $userController->update(),
    'users/show' => $userController->detail(),
    'users/delete' => $userController->delete(),

    default => $homeController->notFound(),
};
