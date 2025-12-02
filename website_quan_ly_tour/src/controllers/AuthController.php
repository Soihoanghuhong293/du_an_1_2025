<?php

class AuthController
{
    // HIỂN THỊ FORM ĐĂNG NHẬP
    public function login()
    {
        if (isLoggedIn()) {
            header('Location: ' . BASE_URL . 'home');
            exit;
        }

        $redirect = $_GET['redirect'] ?? BASE_URL . 'home';

        view('auth.login', [
            'title' => 'Đăng nhập',
            'redirect' => $redirect,
        ]);
    }

    // XỬ LÝ ĐĂNG NHẬP
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

        $user = User::findByEmail($email);

        if (!$user) {
            $errors[] = 'Email không tồn tại';
        } elseif (!password_verify($password, $user->password)) {
            $errors[] = 'Mật khẩu không đúng';
        }

        if (!empty($errors)) {
            view('auth.login', [
                'title' => 'Đăng nhập',
                'errors' => $errors,
                'email' => $email
            ]);
            return;
        }

        // Lưu session
        loginUser($user);

        $redirect = $_POST['redirect'] ?? BASE_URL . 'home';
        header('Location: ' . $redirect);
        exit;
    }

    // HIỂN THỊ FORM ĐĂNG KÝ
    public function register()
    {
        if (isLoggedIn()) {
            header('Location: ' . BASE_URL . 'home');
            exit;
        }

        view('auth.register', [
            'title' => 'Đăng ký tài khoản'
        ]);
    }

    // XỬ LÝ ĐĂNG KÝ
    public function handleRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'register');
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

        // Lưu user mới (name thay cho fullname, password chưa hash)
       User::create([
    'name' => $fullname,
    'email' => $email,
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'role' => 'user',
    'status' => 1
]);
        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    // ĐĂNG XUẤT
    public function logout()
    {
        if (function_exists('logoutUser')) {
            logoutUser();
        }

        header('Location: ' . BASE_URL . 'welcome');
        exit;
    }
}
