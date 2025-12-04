<?php
require_once __DIR__ . '/../models/TourModel.php';
require_once __DIR__ . '/../models/Category.php';

class TourController {
    private $tourModel;

    public function __construct() {
        $this->tourModel = new Tour();
    }

    // =============================================================
    // ⭐ 1. INDEX
    // =============================================================
    public function index() {
        $tours = $this->tourModel->getAll(); 
        
        ob_start();
        view('tour.list', ['tours' => $tours]);
        $content = ob_get_clean();
        
        view('layouts.AdminLayout', [
            'title' => 'Danh sách Tour',
            'content' => $content
        ]);
    }

    // =============================================================
    // ⭐ 2. ADD (Thêm mới)
    // =============================================================
    public function add() {
        $errors = [];
        $categories = Category::all(); // Lấy danh mục

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ten_tour'    => $_POST['ten_tour'] ?? '',
                'mo_ta'       => $_POST['mo_ta'] ?? '',
                'gia'         => $_POST['gia'] ?? 0,
                'category_id' => $_POST['category_id'] ?? 0,
            ];

            // Validate
            if (empty($data['ten_tour'])) $errors[] = "Tên tour không được để trống.";
            if (empty($data['gia']) || $data['gia'] <= 0) $errors[] = "Giá tour không hợp lệ.";
            if (empty($data['category_id'])) $errors[] = "Chưa chọn danh mục.";

            if (empty($errors)) {
                if ($this->tourModel->create($data)) {
                    // FIX: Chuyển hướng về đường dẫn chuẩn
                    header('Location: ' . BASE_URL . 'tour');
                    exit;
                } else {
                    $errors[] = "Thêm tour thất bại do lỗi hệ thống.";
                }
            }
        }

        ob_start();
        view('tour.add', [
            'errors' => $errors,
            'categories' => $categories
        ]);
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Thêm Tour Mới',
            'content' => $content
        ]);
    }

    // =============================================================
    // ⭐ 3. EDIT (Sửa) - ĐÃ FIX
    // =============================================================
    public function edit($id) {
        if (!$id) { header('Location: ' . BASE_URL . 'tour'); exit; }

        $tour = $this->tourModel->getById($id);
        if (!$tour) { die("Tour không tồn tại!"); }

        // FIX: Phải lấy danh mục để hiển thị trong dropdown
        $categories = Category::all();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             $data = [
                'ten_tour'    => $_POST['ten_tour'] ?? '',
                'mo_ta'       => $_POST['mo_ta'] ?? '',
                'gia'         => $_POST['gia'] ?? 0,
                'category_id' => $_POST['category_id'] ?? $tour['category_id'], // Giữ cũ nếu ko chọn
            ];

            if (empty($data['ten_tour'])) $errors[] = "Tên tour không được để trống.";
            if (empty($data['category_id'])) $errors[] = "Danh mục không được để trống.";

            if (empty($errors)) {
                if ($this->tourModel->update($id, $data)) {
                    // FIX: Redirect chuẩn
                    header('Location: ' . BASE_URL . 'tour');
                    exit;
                } else {
                    $errors[] = "Cập nhật tour thất bại.";
                }
            }
            // Nếu lỗi thì giữ lại dữ liệu form đang nhập để hiện lại
            $tour = array_merge($tour, $data); 
        }

        ob_start();
        view('tour.edit', [
            'tour' => $tour, 
            'errors' => $errors,
            'categories' => $categories // FIX: Truyền danh mục sang view
        ]);
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Chỉnh sửa Tour',
            'content' => $content
        ]);
    }   

    // =============================================================
    // ⭐ 4. DELETE (Xóa)
    // =============================================================
    public function delete($id) {
        if (!$id) { header('Location: ' . BASE_URL . 'tour'); exit; }

        if ($this->tourModel->delete($id)) {
            header('Location: ' . BASE_URL . 'tour');
            exit;
        } else {
            die("Xóa tour thất bại!");
        }
    }
}
// Lưu ý: Hàm view() nên để ở file helpers.php dùng chung, không nên để trong Class Controller
// Nếu bạn chưa có file helpers, hãy chuyển hàm view() ra ngoài class.