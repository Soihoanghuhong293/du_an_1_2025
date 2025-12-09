<?php
require_once __DIR__ . '/../models/TourModel.php';
require_once __DIR__ . '/../models/Category.php';

class TourController {
    private $tourModel;

    public function __construct() {
        $this->tourModel = new TourModel();
    }

    // =============================================================
    // 1. Danh sách tour
    // =============================================================
    public function index() {
        $search = $_GET['search'] ?? '';
        $tours = $this->tourModel->getFiltered($search, null); // chỉ tìm theo tên
    
        ob_start();
        view('tour.list', ['tours' => $tours]);
        $content = ob_get_clean();
    
        view('layouts.AdminLayout', [
            'title'     => 'Danh sách Tour',
            'content'   => $content,
            'noSidebar' => true
        ]);
    }
            // =============================================================
    // 2. Thêm tour
    // =============================================================
    public function add() {
        $categories = Category::all();
        $errors = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name'        => $_POST['ten_tour'] ?? '',
                'description' => $_POST['mo_ta'] ?? '',
                'category_id' => $_POST['category_id'] ?? 0,
                'price'       => $_POST['gia'] ?? 0,
                'schedule'    => json_decode($_POST['lich_trinh'] ?? '[]', true),
                'policies'    => json_decode($_POST['chinh_sach'] ?? '[]', true),
                'prices'      => json_decode($_POST['gia_chi_tiet'] ?? '[]', true),
                'status'      => 1,
                'images'      => $this->handleImagesUpload()
            ];
    
            if (empty($data['name'])) $errors[] = "Tên tour không được để trống.";
    
            if (empty($errors)) {
                $this->tourModel->create($data);
                header("Location: index.php?act=tours");
                exit;
            }
        }
    
        // --- Render view ra layout ---
        ob_start();
        view('tour.add', [
            'categories' => $categories,
            'errors'     => $errors
        ]);
        $content = ob_get_clean();
    
        view('layouts.AdminLayout', [
            'title'   => 'Thêm Tour Mới',
            'content' => $content,
            'noSidebar' => true
        ]);
    }
    
    // =============================================================
    // 3. Sửa tour
    // =============================================================
    public function edit($id) {
        if (!$id) { header("Location: index.php?act=tours"); exit; }

        $tour = $this->tourModel->getById($id);
        if (!$tour) die("Tour không tồn tại!");

        $categories = Category::all();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name'        => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'category_id' => $_POST['category_id'] ?? 0,
                'price'       => $_POST['price'] ?? 0,
                'schedule'    => json_decode($_POST['schedule'] ?? '[]', true),
                'policies'    => json_decode($_POST['policies'] ?? '[]', true),
                'prices'      => json_decode($_POST['prices'] ?? '[]', true),
                'suppliers'   => json_decode($_POST['suppliers'] ?? '[]', true),
                'status'      => isset($_POST['status']) ? 1 : 0,
                'images'      => $tour['images'] ?? []
            ];

            // Upload ảnh mới nếu có
            $newImages = $this->handleImagesUpload();
            if (!empty($newImages)) {
                $data['images'] = array_merge($tour['images'] ?? [], $newImages);
            }

            if (empty($data['name'])) $errors[] = "Tên tour không được để trống.";

            if (empty($errors)) {
                $this->tourModel->update($id, $data);
                header("Location: index.php?act=tour-detail&id=$id");
                exit;
            }

            $tour = array_merge($tour, $data);
        }

        ob_start();
        view('tour.edit', [
            'tour'       => $tour,
            'categories' => $categories,
            'errors'     => $errors
        ]);
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title'     => 'Chỉnh sửa Tour',
            'content'   => $content,
            'noSidebar' => true
        ]);
    }

    // =============================================================
    // 4. Xóa tour
    // =============================================================
    public function delete($id) {
        if ($id && $this->tourModel->delete($id)) {
            header("Location: index.php?act=tours");
            exit;
        }
        die("Xóa tour thất bại!");
    }

    // =============================================================
    // 5. Chi tiết tour
    // =============================================================
    public function detail($id) {
        if (!$id) { header("Location: index.php?act=tours"); exit; }

        $tour = $this->tourModel->getById($id);
        if (!$tour) die("Tour không tồn tại!");

        ob_start();
        view('tour.detail', ['tour' => $tour]);
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title'     => 'Chi tiết Tour',
            'content'   => $content,
            'noSidebar' => true
        ]);
    }

    // =============================================================
    // Upload ảnh
    // =============================================================
    private function handleImagesUpload() {
        $uploaded = [];
        $uploadDir = "public/uploads/tours/";

        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $i => $name) {
                $filename = time() . "_" . basename($name);
                $path = $uploadDir . $filename;
                move_uploaded_file($_FILES['images']['tmp_name'][$i], $path);
                $uploaded[] = $filename;
            }
        }

        return $uploaded;
    }
    
}
