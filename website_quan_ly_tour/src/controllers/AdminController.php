<?php

class AdminController
{
    public function __construct()
    {
        // Middleware: chỉ admin mới vào được
        requireAdmin();
    }

    // Trang Dashboard
    public function dashboard()
    {
        $user = getCurrentUser();

        view('admin.dashboard', [
            'title' => 'Bảng điều khiển Admin',
            'user' => $user
        ]);
    }

    // Quản lý người dùng
    public function users()
    {
        // Lấy danh sách user từ database
        global $conn;

        $stmt = $conn->query("SELECT * FROM users ORDER BY id DESC");
        $users = $stmt->fetchAll();

        view('admin.users.index', [
            'title' => 'Quản lý người dùng',
            'users' => $users
        ]);
    }

    // Quản lý hướng dẫn viên
    public function guides()
    {
        global $conn;

        $stmt = $conn->query("SELECT * FROM users WHERE role = 'guide'");
        $guides = $stmt->fetchAll();

        view('admin.guides.index', [
            'title' => 'Quản lý hướng dẫn viên',
            'guides' => $guides
        ]);
    }

    // Quản lý tour
    public function tours()
    {
        global $conn;

        $stmt = $conn->query("SELECT * FROM tours ORDER BY id DESC");
        $tours = $stmt->fetchAll();

        view('admin.tours.index', [
            'title' => 'Quản lý Tours',
            'tours' => $tours
        ]);
    }

    // Quản lý đặt tour
    public function bookings()
    {
        global $conn;

        $stmt = $conn->query("SELECT * FROM bookings ORDER BY id DESC");
        $bookings = $stmt->fetchAll();

        view('admin.bookings.index', [
            'title' => 'Quản lý đặt tour',
            'bookings' => $bookings
        ]);
    }

    // Đăng xuất admin
    public function logout()
    {
        logoutUser();
        header('Location: ' . BASE_URL . '?act=login');
        exit;
    }
}
