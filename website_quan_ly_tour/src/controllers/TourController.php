<?php
require_once __DIR__ . '/../models/TourModel.php';
require_once __DIR__ . '/../models/Category.php';

class TourController {
    private $tourModel;

    public function __construct() {
        // Khởi tạo Model
        // Lưu ý: Đảm bảo class trong file TourModel.php tên là 'TourModel'
        // Nếu class tên là 'Tour', hãy sửa thành new Tour();
        $this->tourModel = new TourModel(); 
    }

    // =============================================================
    // ⭐ 1. INDEX: Danh sách Tour
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
    // ⭐ 2. ADD: Thêm Tour
    // =============================================================
    public function add() {
        $errors = [];
        $categories = Category::all(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Validate cơ bản
            $name = $_POST['name'] ?? ''; 
            $price = $_POST['price'] ?? 0;
            
            if (empty($name)) $errors[] = "Tên tour không được để trống.";
            if ($price < 0) $errors[] = "Giá tour không hợp lệ.";
            if (empty($_POST['category_id'])) $errors[] = "Chưa chọn danh mục.";

            // 2. Xử lý dữ liệu JSON
            $pricesJson = json_encode([
                'adult' => $_POST['prices']['adult'] ?? 0,
                'child' => $_POST['prices']['child'] ?? 0
            ], JSON_UNESCAPED_UNICODE);

            $suppliersText = $_POST['suppliers_text'] ?? '';
            $suppliersArray = array_filter(array_map('trim', explode(',', $suppliersText)));
            $suppliersJson = json_encode(array_values($suppliersArray), JSON_UNESCAPED_UNICODE);

            $scheduleJson = $this->ensureJson($_POST['schedule_text'] ?? '');
            $policiesJson = $this->ensureJson($_POST['policy_text'] ?? '');

            // 3. Xử lý Upload Ảnh (QUAN TRỌNG)
            $images = $this->handleImageUpload();
            $imagesJson = json_encode($images, JSON_UNESCAPED_UNICODE);

            $data = [
                'name'          => $name,
                'category_id'   => $_POST['category_id'] ?? null,
                'description'   => $_POST['description'] ?? '',
                'price'         => $price,
                'duration_days' => $_POST['duration_days'] ?? 1,
                'status'        => $_POST['status'] ?? 1,
                'prices'        => $pricesJson,
                'suppliers'     => $suppliersJson,
                'schedule'      => $scheduleJson,
                'policies'      => $policiesJson,
                'images'        => $imagesJson
            ];

            if (empty($errors)) {
                if ($this->tourModel->create($data)) {
                    header('Location: index.php?act=tours');
                    exit;
                } else {
                    $errors[] = "Thêm tour thất bại. Lỗi hệ thống.";
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
    // ⭐ 3. EDIT: Sửa Tour (ĐÃ SỬA LOGIC ẢNH)
    // =============================================================
    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: index.php?act=tours'); exit; }

        $tour = $this->tourModel->getById($id);
        $categories = Category::all();

        if (!$tour) { die("Tour không tồn tại!"); }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $name = $_POST['name'] ?? '';
            if (empty($name)) $errors[] = "Tên tour không được để trống.";

            // Xử lý JSON các trường thông tin
            $pricesJson = json_encode([
                'adult' => $_POST['prices']['adult'] ?? 0,
                'child' => $_POST['prices']['child'] ?? 0
            ], JSON_UNESCAPED_UNICODE);

            $suppliersJson = json_encode(array_values(array_filter(array_map('trim', explode(',', $_POST['suppliers_text'] ?? '')))), JSON_UNESCAPED_UNICODE);
            $scheduleJson = $this->ensureJson($_POST['schedule_text'] ?? '');
            $policiesJson = $this->ensureJson($_POST['policy_text'] ?? '');

            // ==================================================
            // 🔥 FIX QUAN TRỌNG: LOGIC CẬP NHẬT ẢNH
            // ==================================================
            
            // 1. Lấy danh sách ảnh cũ mà người dùng MUỐN GIỮ LẠI (từ input hidden)
            // Nếu người dùng xóa hết ảnh cũ, mảng này sẽ rỗng.
            $keepImages = $_POST['current_images'] ?? []; 
            
            // 2. Upload ảnh mới (nếu có)
            $newImages = $this->handleImageUpload();

            // 3. Gộp ảnh cũ (đã lọc) và ảnh mới
            $finalImages = array_merge($keepImages, $newImages);
            $imagesJson = json_encode($finalImages, JSON_UNESCAPED_UNICODE);
            
            // ==================================================

            $data = [
                'name'          => $name,
                'category_id'   => $_POST['category_id'] ?? null,
                'description'   => $_POST['description'] ?? '',
                'price'         => $_POST['price'] ?? 0,
                'duration_days' => $_POST['duration_days'] ?? 1,
                'status'        => $_POST['status'] ?? 1,
                'prices'        => $pricesJson,
                'suppliers'     => $suppliersJson,
                'schedule'      => $scheduleJson,
                'policies'      => $policiesJson,
                'images'        => $imagesJson
            ];

            if (empty($errors)) {
                if ($this->tourModel->update($id, $data)) {
                    header('Location: index.php?act=tours');
                    exit;
                } else {
                    $errors[] = "Cập nhật thất bại.";
                }
            }
            // Nếu có lỗi, cập nhật lại biến $tour để hiển thị lại form với dữ liệu vừa nhập
            $tour = array_merge($tour, $data); 
        }

        ob_start();
        view('tour.edit', [
            'tour' => $tour, 
            'categories' => $categories,
            'errors' => $errors
        ]);
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Chỉnh sửa Tour',
            'content' => $content
        ]);
    }   

    // =============================================================
    // ⭐ 4. DELETE: Xóa Tour
    // =============================================================
    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: index.php?act=tours'); exit; }

        if ($this->tourModel->delete($id)) {
            header('Location: index.php?act=tours');
            exit;
        } else {
            die("Xóa tour thất bại!");
        }
    }

    // =============================================================
    // ⭐ 5. SHOW: Chi tiết Tour
    // =============================================================
    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: index.php?act=tours'); exit; }

        $tour = $this->tourModel->getById($id);

        if (!$tour) { die("Tour không tồn tại"); }

        ob_start();
        view('tour.show', ['tour' => $tour]); 
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Chi tiết tour',
            'content' => $content
        ]);
    }

    // =============================================================
    // ⭐ PRIVATE HELPERS
    // =============================================================
    
    private function ensureJson($text) {
        $text = trim($text);
        if ($text === '') return json_encode([], JSON_UNESCAPED_UNICODE);
        // Kiểm tra xem chuỗi nhập vào có phải là JSON hợp lệ không
        json_decode($text);
        if (json_last_error() === JSON_ERROR_NONE) return $text;
        // Nếu không phải JSON, gói nó vào object text (để tránh lỗi frontend parse)
        return json_encode(['text' => $text], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Xử lý upload ảnh
     * Lưu vào: public/uploads/tours/
     * Trả về: Mảng tên file
     */
    private function handleImageUpload() {
        $images = [];

        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            
            // Đường dẫn upload (Cần chắc chắn thư mục này tồn tại và có quyền ghi)
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/tours/';
            
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    error_log("Không thể tạo thư mục upload: " . $uploadDir);
                    return [];
                }
            }

            $count = count($_FILES['images']['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    
                    $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                    // Tạo tên file ngẫu nhiên để tránh trùng lặp
                    $uniqueName = time() . '_' . uniqid() . '.' . $ext;
                    
                    $targetPath = $uploadDir . $uniqueName;

                    if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $targetPath)) {
                        // CHỈ LƯU TÊN FILE VÀO DATABASE
                        $images[] = $uniqueName; 
                    }
                }
            }
        }
        return $images;
    }
}