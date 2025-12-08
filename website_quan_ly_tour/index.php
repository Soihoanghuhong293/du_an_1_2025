<?php
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
require_once __DIR__ . '/src/models/BookingService.php';  // ⭐ THÊM MỚI
require_once __DIR__ . '/src/models/GuideProfile.php';  // ⭐ THÊM MỚI

// Controllers
require_once __DIR__ . '/src/controllers/HomeController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/TourController.php';
require_once __DIR__ . '/src/controllers/BookingController.php';
require_once __DIR__ . '/src/controllers/CategoryController.php';
require_once __DIR__ . '/src/controllers/UserController.php';
require_once __DIR__ . '/src/controllers/GuideController.php';  // ⭐ THÊM MỚI
require_once __DIR__ . '/src/controllers/BookingServiceController.php';
require_once __DIR__ . '/src/controllers/DashboardController.php';



// Khởi tạo controller
$homeController = new HomeController();
$authController = new AuthController();
$tourController = new TourController();
$bookingController = new BookingController();
$categoryController = new CategoryController();
$userController = new UserController();
$guideController = new GuideController();  // ⭐ THÊM MỚI
$bookingServiceController = new BookingServiceController();
$dashboardController = new DashboardController();

// Lấy tham số act (mặc định '/')
$act = $_GET['act'] ?? '/';


// Router
if ($act === 'users/toggleStatus') {
    (new UserController())->toggleStatus();
    exit;
}

// Router
match ($act) {

    // ===============================
    // ⭐ TRANG CHỦ / WELCOME
    // ===============================
    '/', 'welcome' => $homeController->welcome(),
    'home'         => $homeController->home(),

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
    'booking-delete' => $bookingController->delete($_GET['id'] ?? null),
    'booking-show'   => $bookingController->show($_GET['id'] ?? null),


'guest-add'      => $bookingController->addGuest(),
    'guest-delete'   => $bookingController->deleteGuest(),
    'guest-update-rooms' => $bookingController->updateRooms(),



  

    // ===============================
    // ⭐ USER MANAGEMENT
    // ===============================
    'users'          => $userController->index(),
    'users/create'   => $userController->create(),
    'users/store'    => $userController->store(),
    'users/edit'     => $userController->edit(),
    'users/update'   => $userController->update(),
    'users/show'     => $userController->detail(),
    'users/delete'   => $userController->delete(),
    'profile'        => $userController->profile(),
    'profile-update' => $userController->updateProfile(),

    // ===============================
    // ⭐ CATEGORIES
    // ===============================
    'categories'     => $categoryController->index(),
    'category-delete'=> $categoryController->delete($_GET['id'] ?? null),
    'category-add'   => $categoryController->add(),
    'category-edit'  => $categoryController->edit($_GET['id'] ?? null),
    // 1. Hiển thị danh sách tour
    'tour' => $tourController->index(),
    // ===============================
    // ⭐ TOUR MANAGEMENT
    // ===============================
    'tours'          => $tourController->index(),
    'tour-add'       => $tourController->add(),
    'tour-edit'      => $tourController->edit($_GET['id'] ?? null),
    'tour-delete'    => $tourController->delete($_GET['id'] ?? null),
    'tour-show'      => $tourController->show($_GET['id'] ?? null),

    // ===============================
    // ⭐ HƯỚNG DẪN VIÊN (HDV)
    // ===============================
    'guide-tours'        => $guideController->assignedTours(),
    'guide-customers'    => $guideController->customers(),

    // ⭐ Nhật ký tour
    'guide-diary'        => $guideController->diary(),
    'guide-diary-store'  => $guideController->diaryStore(),

    // ⭐ Lịch trình
    'guide-schedule'     => $guideController->schedule(),

    // ⭐ Tải file phân công
    'guide-download'     => $guideController->downloadAssignment(),

    'guide-confirm' => $guideController->confirm(),
'guide-reject'  => $guideController->reject(),
'guest-ajax-checkin' => $bookingController->ajaxCheckin(),

'guide-show'       => $guideController->show(),       // <-- Xem chi tiết
    'guide-diary-save' => $guideController->saveDiary(),
    'guide-finish'  => $guideController->finish(), 
    


  //  API LẤY THÔNG TIN TOUR 
    'api-get-tour-info' => $bookingController->getTourInfo(),
    'booking-update-diary' => $bookingController->updateDiary(),
    'booking-update-schedule' => $bookingController->updateSchedule(),
    'booking-update-service'  => $bookingController->updateService(),

    // PHÂN BỔ DỊCH VỤ
    'booking-service-add'    => $bookingServiceController->add(),
    'booking-service-delete' => $bookingServiceController->delete(),

    // thong ke
    'dashboard' => $dashboardController->index(),
    
    default => $homeController->notFound(),
    
};

