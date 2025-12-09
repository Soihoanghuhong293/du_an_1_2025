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
        } elseif ($user->status == 0) {
            // Tài khoản bị khóa
            $errors[] = "Tài khoản của bạn đang bị khóa!";
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
