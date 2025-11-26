<?php

// Controller xử lý các chức năng liên quan đến xác thực (đăng nhập, đăng xuất)
class AuthController
{
    
    // Hiển thị form đăng nhập
    public function login()
    {
        // Nếu đã đăng nhập rồi thì chuyển về trang home
        if (isLoggedIn()) {
            header('Location: ' . BASE_URL . 'home');
            exit;   
        }

        // Lấy URL redirect nếu có (để quay lại trang đang xem sau khi đăng nhập)
        // Mặc định redirect về trang home
        $redirect = $_GET['redirect'] ?? BASE_URL . 'home';

        // Hiển thị view login
        view('auth.login', [
            'title' => 'Đăng nhập',
            'redirect' => $redirect,
        ]);
    }
    

    // Xử lý đăng nhập (nhận dữ liệu từ form POST)
    public function checkLogin()
    {
        // Chỉ xử lý khi là POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        // Lấy dữ liệu từ form
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        // Mặc định redirect về trang home sau khi đăng nhập
        $redirect = $_POST['redirect'] ?? BASE_URL . 'home';

        // Validate dữ liệu đầu vào
        $errors = [];

        if (empty($email)) {
            $errors[] = 'Vui lòng nhập email';
        }

        if (empty($password)) {
            $errors[] = 'Vui lòng nhập mật khẩu';
        }
        // Nếu có lỗi validation thì quay lại form login
        if (!empty($errors)) {
            view('auth.login', [
                'title' => 'Đăng nhập',
                'errors' => $errors,
                'email' => $email,
                'redirect' => $redirect,
            ]);
            return;
        }
         $user = $this->userModel->findByEmail($email);
         if(!$user){
            $errors[] = "Email không tồn tại";
            view('auth.login', compact('errors','email','redirect'));
            return;
         }

        $query = "SELECT password FROM users WHERE email = ?";
        $stmt = $this->userModel->conn->prepare($query);
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($password, $row['password'])) {
            $errors[] = "Mật khẩu không đúng";
            view('auth.login', compact('errors', 'email', 'redirect'));
            return;
        }
        $user = new Usẻ($row);

        // ❗ Check role để vào admin
        if ($user->role !== 'admin') {
            $errors[] = "Bạn không có quyền truy cập trang quản trị!";
            view('auth.login', compact('errors', 'email', 'redirect'));
            return;
        }

        // Đăng nhập thành công
        loginUser($user);

        header("Location: " . BASE_URL . "admin/dashboard");
        exit;
    }

        // Tạo user mẫu để đăng nhập (không kiểm tra database)
        // Chỉ để demo giao diện

        // Đăng nhập thành công: lưu vào session

    // Xử lý đăng xuất
    public function logout()
    {
        // Xóa session và đăng xuất
        logoutUser();

        // Chuyển hướng về trang welcome
        header('Location: ' . BASE_URL . 'welcome');
        exit;
    }
    
}


