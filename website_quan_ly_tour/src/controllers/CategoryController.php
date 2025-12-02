<?php

class CategoryController
{
    public function index()
     {
        // Nạp model
        require_once __DIR__ . '/../models/Category.php';

        // Lấy dữ liệu từ model
        $categories = Category::all();

        // Tạo biến tiêu đề
        $title = "Quản lý Danh mục";

        // Nhúng view con vào layout admin
        ob_start();
        require_once __DIR__ . '/../../views/categories/index.php'; // view con
        $content = ob_get_clean();

        // Gọi layout admin
        require_once __DIR__ . '/../../views/layouts/AdminLayout.php';
    }
}
