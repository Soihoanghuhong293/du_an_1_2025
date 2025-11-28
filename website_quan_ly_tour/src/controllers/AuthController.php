<?php

class AuthController
{

       // HIỂN THỊ FORM ĐĂNG NHẬP
    public function login()
    {
        // Nếu đã đăng nhập → chuyển về home
        if (isLoggedIn()) {
            header('Location: ' . BASE_URL . 'home');
            exit;
        }

        // URL quay lại sau khi đăng nhập
        $redirect = $_GET['redirect'] ?? BASE_URL . 'home';

        view('auth.login', [
            'title' => 'Đăng nhập',
            'redirect' => $redirect,
        ]);
    }

    /* ================================
        XỬ LÝ ĐĂNG NHẬP
    ================================= */
   public function checkLogin()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . 'home');
        exit;
    }

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $errors = [];

    if (empty($email)) $errors[] = 'Vui lòng nhập email';
    if (empty($password)) $errors[] = 'Vui lòng nhập mật khẩu';

    if (!empty($errors)) {
        view('auth.login', [
            'title' => 'Đăng nhập',
            'errors' => $errors,
            'email' => $email
        ]);
        return;
    }

    // Lấy user từ DB
    $user = User::findByEmail($email);

    if (!$user) {
        $errors[] = 'Email không tồn tại';
    } elseif (!password_verify($password, $user['password'])) {
        $errors[] = 'Mật khẩu không đúng';
    }

    if (!empty($errors)) {
        view('auth.login', data: [
            'title' => 'Đăng nhập',
            'errors' => $errors,
            'email' => $email
        ]);
        return;
    }

    // Lưu session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
  loginUser((object)$user);


    // Redirect về home
    header('Location: ' . BASE_URL . 'home');
    exit;
}



        // HIỂN THỊ FORM ĐĂNG KÝ
    public function register()
    {
        if (isLoggedIn()) {
            header('Location: ' . BASE_URL . '?act=home');
            exit;
        }

        view('auth.register', [
            'title' => 'Đăng ký tài khoản'
        ]);
    }

    
      //  XỬ LÝ ĐĂNG KÝ
   public function handleRegister()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '?act=register');
        exit;
    }

    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    $errors = [];

    if (empty($fullname)) $errors[] = "Vui lòng nhập họ tên";
    if (empty($email)) $errors[] = "Vui lòng nhập email";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ";
    if (empty($password)) $errors[] = "Vui lòng nhập mật khẩu";
    if ($password !== $confirm_password) $errors[] = "Mật khẩu xác nhận không khớp";

    // Kiểm tra email đã tồn tại
    if (User::findByEmail($email)) {
        $errors[] = "Email đã được sử dụng";
    }

    if (!empty($errors)) {
        view('auth.register', [
            'title' => 'Đăng ký tài khoản',
            'errors' => $errors,
            'fullname' => $fullname,
            'email' => $email,
        ]);
        return;
    }

    // Lưu user mới
    User::create([
        'fullname' => $fullname,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT)
    ]);

    // Chuyển sang trang login
    header('Location: ' . BASE_URL . 'login');
    exit;
}
public function logout()
{
    // Xóa session login
    if (isset($_SESSION['user'])) {
        unset($_SESSION['user']);
    }

    // Hoặc nếu bạn có hàm helper logoutUser()
    if (function_exists('logoutUser')) {
        logoutUser();
    }

    // Chuyển về trang welcome
    header('Location: ' . BASE_URL . 'welcome');
    exit;
}

    
}
