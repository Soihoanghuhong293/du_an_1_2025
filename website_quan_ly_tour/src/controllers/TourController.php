<?php
// ...existing code...
require_once __DIR__ . '/../models/TourModel.php';
require_once __DIR__ . '/../models/Category.php';

class TourController {
    private $tourModel;

    public function __construct() {
        // Khởi tạo Tour Model (Không cần tham số $db)
        $this->tourModel = new Tour();
    }

    // =============================================================
    // ⭐ 1. Hành động index (Hiển thị danh sách)
    // =============================================================
    public function index() {
        $tours = $this->tourModel->getAll(); 
        ob_start();
        // >>> changed: use view() helper to load view from views/tour/list.php
        view('tour.list', ['tours' => $tours]);
        $content = ob_get_clean();
        view('layouts.AdminLayout', [
            'title' => 'Danh sách Tour',
            'content' => $content
        ]);
    }

    // =============================================================
    // ⭐ 2. Hành động add (Thêm tour)
    // =============================================================
    public function add() {
    $errors = [];

    // Lấy danh sách danh mục từ model
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

        if (empty($data['gia']) || $data['gia'] <= 0)
            $errors[] = "Giá tour không hợp lệ.";

        if (empty($data['category_id']))
            $errors[] = "Chưa chọn danh mục.";

        if (empty($errors)) {
            if ($this->tourModel->create($data)) {
                header('Location:' . BASE_URL . 'tour');
                exit;
            } else {
                $errors[] = "Thêm tour thất bại do lỗi hệ thống.";
            }
        }
    }

    ob_start();
    view('tour.add', [
        'errors' => $errors,
        'categories' => $categories    // <<<<<<  BẮT BUỘC CÓ !
    ]);
    $content = ob_get_clean();

    view('layouts.AdminLayout', [
        'title' => 'Thêm Tour Mới',
        'content' => $content
    ]);
}

    // =============================================================
    // ⭐ 3. Hành động edit (Sửa tour)
    // =============================================================
    public function edit($id) {
        if (!$id) { header('Location: index.php?act=tour'); exit; }

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

            if (empty($data['ten_tour'])) $errors[] = "Tên tour không được để trống.";

            if (empty($errors)) {
                if ($this->tourModel->update($id, $data)) {
                    header('Location: index.php?act=tour');
                    exit;
                } else {
                    $errors[] = "Cập nhật tour thất bại.";
                }
            }
            $tour = array_merge($tour, $data); 
        }
        ob_start();
        // >>> changed: use view() helper and pass $tour + $errors
        view('tour.edit', ['tour' => $tour, 'errors' => $errors]);
        $content = ob_get_clean();
        view('layouts.AdminLayout', [
            'title' => 'Chỉnh sửa Tour',
            'content' => $content
        ]);
    }   

    // =============================================================
    // ⭐ 4. Hành động delete (Xóa tour)
    // =============================================================
    public function delete($id) {
        if (!$id) { header('Location: index.php?act=tour'); exit; }

        if ($this->tourModel->delete($id)) {
            header('Location: index.php?act=tour');
            exit;
        } else {
            die("Xóa tour thất bại!");
        }
    }
    function view($path, $data = [])
{
    // chuyển dots → slash: 'tour.list' => 'tour/list'
    $path = str_replace('.', '/', $path);

    // tạo biến từ mảng $data
    extract($data);

    // đường dẫn tới thư mục views
    $file = __DIR__ . '/../views/' . $path . '.php';

    if (file_exists($file)) {
        require $file;
    } else {
        echo "View not found: " . $file;
    }
}
}