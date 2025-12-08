<?php
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// Loại bỏ query string (?act=...) để lấy đường dẫn gốc
$baseUrl = strtok($url, '?');
// Loại bỏ index.php nếu có
$baseUrl = str_replace('index.php', '', $baseUrl);

// Định nghĩa Hằng số BASE_URL
define('BASE_URL', $baseUrl);
// BẬT HIỂN THỊ LỖI
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', __DIR__);

// Nạp cấu hình chung của ứng dụng
$config = require __DIR__ . '/config/config.php';

// Helper
require_once __DIR__ . '/src/helpers/helpers.php';
require_once __DIR__ . '/src/helpers/database.php';

// Models
require_once __DIR__ . '/src/models/User.php';
require_once __DIR__ . '/src/models/TourModel.php';
require_once __DIR__ . '/src/models/Booking.php'; 
require_once __DIR__ . '/src/models/BookingService.php'; 
require_once __DIR__ . '/src/models/Category.php'; // Ensure Category model is included

// Controllers
require_once __DIR__ . '/src/controllers/HomeController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/TourController.php';
require_once __DIR__ . '/src/controllers/BookingController.php';
require_once __DIR__ . '/src/controllers/CategoryController.php';
require_once __DIR__ . '/src/controllers/UserController.php';
require_once __DIR__ . '/src/controllers/GuideController.php';
require_once __DIR__ . '/src/controllers/BookingServiceController.php';

// Khởi tạo controller
$homeController = new HomeController();
$authController = new AuthController();
$tourController = new TourController();
$bookingController = new BookingController();
$categoryController = new CategoryController();
$userController = new UserController();
$guideController = new GuideController();
$bookingServiceController = new BookingServiceController();

// Lấy tham số act (mặc định '/')
$act = $_GET['act'] ?? '/';

// Router đặc biệt (không dùng match)
if ($act === 'users/toggleStatus') {
    (new UserController())->toggleStatus();
    exit;
}

// Router chính
match ($act) {

    // ===============================
    // ⭐ TRANG CHỦ / WELCOME
    // ===============================
    '/'         => $homeController->welcome(),
    'welcome'   => $homeController->welcome(),
    'home'      => $homeController->home(),

    // ===============================
    // ⭐ AUTH (Đăng nhập/Đăng ký)
    // ===============================
    'login'           => $authController->login(),
    'register'        => $authController->register(),
    'check-login'     => $authController->checkLogin(),
    'handle-register' => $authController->handleRegister(),
    'logout'          => $authController->logout(),

    // ===============================
    // ⭐ BOOKING MANAGEMENT
    // ===============================
    'bookings'       => $bookingController->index(),
    'booking-create' => $bookingController->create(),
    'booking-store'  => $bookingController->store(),
    'booking-delete' => $bookingController->delete($_GET['id'] ?? null),
    'booking-show'   => $bookingController->show($_GET['id'] ?? null),

    // Khách hàng trong booking
    'guest-add'          => $bookingController->addGuest(),
    'guest-delete'       => $bookingController->deleteGuest(),
    'guest-update-rooms' => $bookingController->updateRooms(),
    'guest-ajax-checkin' => $bookingController->ajaxCheckin(),

    // API & AJAX cho Booking
    'api-get-tour-info'       => $bookingController->getTourInfo(),
    'api-get-available-guides'=> $bookingController->getAvailableGuides(), // Added missing API route
    'booking-update-diary'    => $bookingController->updateDiary(),
    'booking-update-schedule' => $bookingController->updateSchedule(),
    'booking-update-service'  => $bookingController->updateService(),

    // Phân bổ dịch vụ
    'booking-service-add'    => $bookingServiceController->add(),
    'booking-service-delete' => $bookingServiceController->delete(),

    // ===============================
    // ⭐ USER MANAGEMENT
    // ===============================
    'users'        => $userController->index(),
    'users/create' => $userController->create(),
    'users/store'  => $userController->store(),
    'users/edit'   => $userController->edit(),
    'users/update' => $userController->update(),
    'users/show'   => $userController->detail(),
    'users/delete' => $userController->delete(),

    // ===============================
    // ⭐ CATEGORIES
    // ===============================
    'categories'      => $categoryController->index(),
    'category-delete' => $categoryController->delete($_GET['id'] ?? null),
    'category-add'    => $categoryController->add(),
    'category-edit'   => $categoryController->edit($_GET['id'] ?? null),

    // ===============================
    // ⭐ TOUR MANAGEMENT
    // ===============================
    'tours'       => $tourController->index(), // Main list
    'tour'        => $tourController->index(), // Alias
    'tour-create' => $tourController->add(),   // Changed to 'tour-create' to match Booking naming convention
    'tour-add'    => $tourController->add(),   // Alias
    'tour-edit'   => $tourController->edit(),
    'tour-delete' => $tourController->delete(),
'tour-show' => $tourController->show(),
    // ===============================
    // ⭐ HƯỚNG DẪN VIÊN (HDV)
    // ===============================
    'guide-tours'     => $guideController->assignedTours(),
    'guide-customers' => $guideController->customers(),
    'guide-diary'     => $guideController->diary(),
    'guide-diary-store'=> $guideController->diaryStore(),
    'guide-schedule'  => $guideController->schedule(),
    'guide-download'  => $guideController->downloadAssignment(),
    'guide-confirm'   => $guideController->confirm(),
    'guide-reject'    => $guideController->reject(),
    
    // 404
    default => $homeController->notFound(),
};