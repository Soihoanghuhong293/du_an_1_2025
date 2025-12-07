<?php
require_once __DIR__ . '/../models/TourModel.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/User.php';

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
    // ⭐ SHOW (Chi tiết tour)
    // =============================================================
    public function show($id) {
        if (!$id) { header('Location:' . BASE_URL . 'index.php?act=tour'); exit; }

        $tour = $this->tourModel->getById($id);
        if (!$tour) {
            // Nếu không tìm thấy thì hiển thị trang 404
            view('not_found');
            return;
        }

        ob_start();
        view('tour.show', ['tour' => $tour]);
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Chi tiết Tour',
            'content' => $content
        ]);
    }


    // =============================================================
    // ⭐ 2. ADD (Thêm mới)
    // =============================================================
    public function add()
{
    $errors = [];
    $categories = Category::all();
    $suppliersList = User::all();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $data = [
            'ten_tour'    => trim($_POST['ten_tour']),
            'mo_ta'       => trim($_POST['mo_ta']),
            'gia'         => (int)($_POST['gia'] ?? 0),
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'lich_trinh'  => trim($_POST['lich_trinh'] ?? ''),
            'chinh_sach'  => trim($_POST['chinh_sach'] ?? ''),
            // detailed prices and suppliers
            'prices'      => [
                'adult' => (int)($_POST['prices']['adult'] ?? 0),
                'child' => (int)($_POST['prices']['child'] ?? 0),
            ],
            // Allow suppliers entered manually as comma-separated text or as ids
            'suppliers'   => (function() {
                if (!empty($_POST['suppliers_text'])) {
                    $text = $_POST['suppliers_text'];
                    $parts = array_map('trim', explode(',', $text));
                    // Remove empty
                    return array_values(array_filter($parts, function($v){ return $v !== ''; }));
                }
                // Fallback to numeric ids if provided
                return array_map('intval', array_values($_POST['suppliers'] ?? []));
            })()
        ];

        // VALIDATION
        if ($data['ten_tour'] === '') $errors[] = "Tên tour không được để trống.";
        if ($data['gia'] <= 0) $errors[] = "Giá tour không hợp lệ.";
        if ($data['category_id'] === 0) $errors[] = "Chưa chọn danh mục.";

        if (empty($errors)) {

            // Tạo tour
            if ($this->tourModel->create($data)) {

                // Lấy ID tour vừa tạo
                $tourId = $this->tourModel->getLastInsertId();

                // Xử lý ảnh
                $uploadedFiles = [];
                if (!empty($_FILES['hinh_anh']['name'][0])) {

                    foreach ($_FILES['hinh_anh']['name'] as $i => $fileName) {

                        $tmp = $_FILES['hinh_anh']['tmp_name'][$i];
                        $newName = time() . "_" . $fileName;

                        $path = "uploads/tours/" . $newName;
                        move_uploaded_file($tmp, $path);

                        $uploadedFiles[] = $path;
                    }

                    // Lưu ảnh vào DB
                    $this->tourModel->saveImages($tourId, $uploadedFiles);
                }

                header('Location: ' . BASE_URL . 'index.php?act=tour');
                exit;
            }

            $errors[] = "Thêm tour thất bại.";
        }
    }

    ob_start();
    view('tour.add', [
        'errors' => $errors,
        'categories' => $categories,
        'suppliersList' => $suppliersList
    ]);
    $content = ob_get_clean();

    view('layouts.AdminLayout', [
        'title' => 'Thêm Tour',
        'content' => $content
    ]);
}
    // =============================================================
    // ⭐ 3. EDIT (Sửa) - ĐÃ FIX
    // =============================================================
    public function edit($id) {
    if (!$id) { header('Location:' . BASE_URL . 'index.php?act=tour'); exit; }

    $tour = $this->tourModel->getById($id);
    if (!$tour) die("Tour không tồn tại");

    $categories = Category::all();
    $suppliersList = User::all();
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'ten_tour'    => $_POST['ten_tour'] ?? '',
            'mo_ta'       => $_POST['mo_ta'] ?? '',
            'gia'         => $_POST['gia'] ?? 0,
            'category_id' => $_POST['category_id'] ?? $tour['category_id'],
            'lich_trinh'  => $_POST['lich_trinh'] ?? ($tour['lich_trinh'] ?? ''),
            'chinh_sach'  => $_POST['chinh_sach'] ?? ($tour['chinh_sach'] ?? ''),
            'prices'      => [
                'adult' => (int)($_POST['prices']['adult'] ?? ($tour['gia_chi_tiet']['adult'] ?? 0)),
                'child' => (int)($_POST['prices']['child'] ?? ($tour['gia_chi_tiet']['child'] ?? 0)),
            ],
            'suppliers'   => (function() use ($tour) {
                if (!empty($_POST['suppliers_text'])) {
                    $text = $_POST['suppliers_text'];
                    $parts = array_map('trim', explode(',', $text));
                    return array_values(array_filter($parts, function($v){ return $v !== ''; }));
                }
                // Fallback to numeric ids (if the existing tour stored ids)
                return array_map('intval', array_values($_POST['suppliers'] ?? ($tour['nha_cung_cap_ids'] ?? [])));
            })(),
        ];

        if (empty($errors)) {
            if ($this->tourModel->update($id, $data)) {
                header('Location:' . BASE_URL . 'index.php?act=tour');
                exit;
            } else {
                $errors[] = "Không thể cập nhật tour!";
            }
        }

        $tour = array_merge($tour, $data);
    }

    ob_start();
    view('tour.edit', [
        'tour' => $tour,
        'errors' => $errors,
        'categories' => $categories,
        'suppliersList' => $suppliersList
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
        if (!$id) { header('Location: ' . BASE_URL . 'index.php?act=tour'); exit; }

        if ($this->tourModel->delete($id)) {
            header('Location: ' . BASE_URL . 'index.php?act=tour');
            exit;
        } else {
            die("Xóa tour thất bại!");
        }
    }
}
// Lưu ý: Hàm view() nên để ở file helpers.php dùng chung, không nên để trong Class Controller
// Nếu bạn chưa có file helpers, hãy chuyển hàm view() ra ngoài class.