<?php

// Nạp cấu hình chung của ứng dụng
$config = require __DIR__ . '/config/config.php';

// Helpers
require_once __DIR__ . '/src/helpers/helpers.php';
require_once __DIR__ . '/src/helpers/database.php';

// Model
require_once __DIR__ . '/src/models/User.php';

// Controllers
require_once __DIR__ . '/src/controllers/HomeController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/AdminController.php';

// Khởi tạo controller
$homeController = new HomeController();
$authController = new AuthController();
$adminController = new AdminController();

// Xác định route
$act = $_GET['act'] ?? '/';

match ($act) {

    '/', 'welcome' => $homeController->welcome(),

    'home' => $homeController->home(),

    'login' => $authController->login(),
    'check-login' => $authController->checkLogin(),
    'logout' => $authController->logout(),

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

    default => $homeController->notFound(),
};
