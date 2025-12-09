<?php
// BẬT HIỂN THỊ LỖI (Tắt khi deploy)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', __DIR__);

// Nạp cấu hình & Helpers
$config = require __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/helpers/helpers.php';
require_once __DIR__ . '/src/helpers/database.php';

// Models
require_once __DIR__ . '/src/models/User.php';
require_once __DIR__ . '/src/models/TourModel.php';
require_once __DIR__ . '/src/models/Booking.php'; 
require_once __DIR__ . '/src/models/BookingService.php';

// Controllers
require_once __DIR__ . '/src/controllers/HomeController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/TourController.php';
require_once __DIR__ . '/src/controllers/BookingController.php';
require_once __DIR__ . '/src/controllers/CategoryController.php';
require_once __DIR__ . '/src/controllers/UserController.php';
require_once __DIR__ . '/src/controllers/GuideController.php';
require_once __DIR__ . '/src/controllers/BookingServiceController.php';
require_once __DIR__ . '/src/controllers/DashboardController.php';

// Khởi tạo controller
$homeController          = new HomeController();
$authController          = new AuthController();
$tourController          = new TourController();
$bookingController       = new BookingController();
$categoryController      = new CategoryController();
$userController          = new UserController();
$guideController         = new GuideController();
$bookingServiceController= new BookingServiceController();
$dashboardController     = new DashboardController();

// Lấy tham số act
$act = $_GET['act'] ?? '/';

// Xử lý Route đặc biệt (AJAX toggle status user)
if ($act === 'users/toggleStatus') {
    $userController->toggleStatus();
    exit;
}

// Router chính
match ($act) {

    // ===============================
    // ⭐ TRANG CHỦ & AUTH
    // ===============================
    '/', 'welcome'      => $homeController->welcome(),
    'home'              => $homeController->home(),
    
    'login'             => $authController->login(),
    'register'          => $authController->register(),
    'check-login'       => $authController->checkLogin(),
    'handle-register'   => $authController->handleRegister(),
    'logout'            => $authController->logout(),

    // ===============================
    // ⭐ THỐNG KÊ (DASHBOARD)
    // ===============================
    'dashboard'         => $dashboardController->index(),

    // ===============================
    // ⭐ BOOKING (QUẢN LÝ ĐẶT TOUR)
    // ===============================
    'bookings'          => $bookingController->index(),
    'booking-create'    => $bookingController->create(),
    'booking-store'     => $bookingController->store(),
    'booking-delete'    => $bookingController->delete($_GET['id'] ?? null),
    'booking-show'      => $bookingController->show($_GET['id'] ?? null),
    
    // AJAX / API Booking
    'api-get-tour-info'       => $bookingController->getTourInfo(),     // <-- QUAN TRỌNG: ĐỂ FILL DATA
    'booking-update-diary'    => $bookingController->updateDiary(),
    'booking-update-schedule' => $bookingController->updateSchedule(),
    'booking-update-service'  => $bookingController->updateService(),
    'guest-ajax-checkin'      => $bookingController->ajaxCheckin(),

    // Quản lý khách trong Booking
    'guest-add'          => $bookingController->addGuest(),
    'guest-delete'       => $bookingController->deleteGuest(),
    'guest-update-rooms' => $bookingController->updateRooms(),

    // Phân bổ dịch vụ (Booking Service)
    'booking-service-add'    => $bookingServiceController->add(),
    'booking-service-delete' => $bookingServiceController->delete(),

    // ===============================
    // ⭐ USER MANAGEMENT
    // ===============================
    'users'              => $userController->index(),
    'users/create'       => $userController->create(),
    'users/store'        => $userController->store(),
    'users/edit'         => $userController->edit(),
    'users/update'       => $userController->update(),
    'users/show'         => $userController->detail(),
    'users/delete'       => $userController->delete(),

    // ===============================
    // ⭐ CATEGORIES
    // ===============================
    'categories'         => $categoryController->index(),
    'category-add'       => $categoryController->add(),
    'category-edit'      => $categoryController->edit($_GET['id'] ?? null),
    'category-delete'    => $categoryController->delete($_GET['id'] ?? null),

    // ===============================
    // ⭐ TOUR MANAGEMENT
    'tours'              => $tourController->index(),
    'tour-add'           => $tourController->add(),
    'tour-edit'          => $tourController->edit($_GET['id'] ?? null),
    'tour-delete'        => $tourController->delete($_GET['id'] ?? null),
    'tour-detail'        => $tourController->detail($_GET['id'] ?? null),

    // ===============================
    // ⭐ HƯỚNG DẪN VIÊN (GUIDE PORTAL)
    // ===============================
    'guide-tours'        => $guideController->assignedTours(),
    'guide-customers'    => $guideController->customers(),
    'guide-show'         => $guideController->show(),
    
    'guide-diary'        => $guideController->diary(),
    'guide-diary-save'   => $guideController->saveDiary(), // Có thể bạn dùng cái này hoặc guide-diary-store
    'guide-diary-store'  => $guideController->diaryStore(),
    
    'guide-schedule'     => $guideController->schedule(),
    'guide-download'     => $guideController->downloadAssignment(),
    
    // Thao tác Guide
    'guide-confirm'      => $guideController->confirm(),
    'guide-reject'       => $guideController->reject(),
    'guide-finish'       => $guideController->finish(),

    
'api-get-available-guides' => $bookingController->getAvailableGuides(),


    default => $homeController->notFound(),
};