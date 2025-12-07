<?php

require_once __DIR__ . '/../models/TourModel.php';
require_once __DIR__ . '/../models/Category.php';

class TourController {
    private $tourModel;

    public function __construct() {
        $this->tourModel = new Tour();
    }

    // =============================================================
    // ⭐ INDEX: Danh sách Tour
    // =============================================================
    public function index() {
        $tours = $this->tourModel->getAll();

        ob_start();
        view('tour.list', [
            'tours' => $tours
        ]);
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Danh sách Tour',
            'content' => $content
        ]);
    }

    // =============================================================
    // ⭐ ADD: Thêm Tour
    // =============================================================
    public function add() {
        $errors = [];
        $categories = Category::all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'ten_tour' => $_POST['ten_tour'] ?? '',
                'mo_ta' => $_POST['mo_ta'] ?? '',
                'gia' => $_POST['gia'] ?? 0,
                'category_id' => $_POST['category_id'] ?? 0,
            ];

            if (empty($data['ten_tour']))
                $errors[] = "Tên tour không được để trống.";

            if ($data['gia'] <= 0)
                $errors[] = "Giá tour không hợp lệ.";

            if (empty($data['category_id']))
                $errors[] = "Chưa chọn danh mục.";

            if (empty($errors)) {
                $this->tourModel->create($data);
                header('Location: ' . BASE_URL . '?controller=tour');
                exit;
            }
        }

        ob_start();
        view('tour.add', [
            'errors' => $errors,
            'categories' => $categories
        ]);
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Thêm Tour',
            'content' => $content
        ]);
    }

    // =============================================================
    // ⭐ EDIT: Sửa Tour
    // =============================================================
    public function edit() {

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . BASE_URL . '?controller=tour'); exit; }

        $tour = $this->tourModel->getById($id);
        if (!$tour) { die("Tour không tồn tại!"); }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'ten_tour' => $_POST['ten_tour'] ?? '',
                'mo_ta' => $_POST['mo_ta'] ?? '',
                'gia' => $_POST['gia'] ?? 0,
                'ngay_khoi_hanh' => $_POST['ngay_khoi_hanh'] ?? '',
                'diem_den' => $_POST['diem_den'] ?? ''
            ];

            if (empty($data['ten_tour']))
                $errors[] = "Tên tour không được để trống.";

            if (empty($errors)) {
                $this->tourModel->update($id, $data);
                header('Location: ' . BASE_URL . '?controller=tour');
                exit;
            }

            $tour = array_merge($tour, $data);
        }

        ob_start();
        view('tour.edit', [
            'tour' => $tour,
            'errors' => $errors
        ]);
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Chỉnh sửa Tour',
            'content' => $content
        ]);
    }

    // =============================================================
    // ⭐ DELETE: Xóa Tour
    // =============================================================
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->tourModel->delete($id);
        }
        header('Location: ' . BASE_URL . '?controller=tour');
        exit;
    }

    // =============================================================
    // ⭐ DETAIL: Chi tiết Tour
    // =============================================================
   public function show() {

    $id = $_GET['id'] ?? null;

    if (!$id) {
        header('Location: index.php?controller=tour&action=index');
        exit;
    }

    $tour = $this->tourModel->getById($id);

    if (!$tour) {
        die("Tour không tồn tại");
    }

    ob_start();
    view('tour.detail', ['tour' => $tour]);
    $content = ob_get_clean();

    view('layouts.AdminLayout', [
        'title' => 'Chi tiết tour',
        'content' => $content
    ]);
}


}

