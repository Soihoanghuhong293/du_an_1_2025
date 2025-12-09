<?php

require_once __DIR__ . '/../models/TourModel.php';
require_once __DIR__ . '/../models/Category.php';

class TourController {
    private $tourModel;

    public function __construct() {
        // Khởi tạo Model (Bắt buộc vì Model hiện tại là dạng Instance, không phải Static)
        $this->tourModel = new Tour();
    }

    // =============================================================
    // ⭐ 1. INDEX: Danh sách Tour
    // =============================================================
    public function index() {
        // SỬA: Gọi qua $this->tourModel thay vì Tour::getAll()
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
                // SỬA: Gọi qua $this->tourModel
                if ($this->tourModel->create($data)) {
                    header('Location: index.php?act=tours');
                    exit;
                } else {
                    $errors[] = "Thêm tour thất bại. Lỗi: " . ($this->tourModel->getLastError() ?? 'Unknown');
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
    // ⭐ 3. EDIT: Sửa Tour
    // =============================================================
    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: index.php?act=tours'); exit; }

        // SỬA: Gọi qua $this->tourModel
        $tour = $this->tourModel->getById($id);
        $categories = Category::all();

        if (!$tour) { die("Tour không tồn tại!"); }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $name = $_POST['name'] ?? '';
            if (empty($name)) $errors[] = "Tên tour không được để trống.";

            // Xử lý JSON
            $pricesJson = json_encode([
                'adult' => $_POST['prices']['adult'] ?? 0,
                'child' => $_POST['prices']['child'] ?? 0
            ], JSON_UNESCAPED_UNICODE);

            $suppliersJson = json_encode(array_values(array_filter(array_map('trim', explode(',', $_POST['suppliers_text'] ?? '')))), JSON_UNESCAPED_UNICODE);
            $scheduleJson = $this->ensureJson($_POST['schedule_text'] ?? '');
            $policiesJson = $this->ensureJson($_POST['policy_text'] ?? '');

            // Xử lý Ảnh: Giữ ảnh cũ + Thêm ảnh mới
            $oldImages = $tour['images']; 
            if (!is_array($oldImages)) $oldImages = [];
            
            $newImages = $this->handleImageUpload();
            $finalImages = array_merge($oldImages, $newImages);
            $imagesJson = json_encode($finalImages, JSON_UNESCAPED_UNICODE);

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
                // SỬA: Gọi qua $this->tourModel->update (Hàm trong Model tên là update, không phải updateById)
                if ($this->tourModel->update($id, $data)) {
                    header('Location: index.php?act=tours');
                    exit;
                } else {
                    $errors[] = "Cập nhật thất bại.";
                }
            }
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

        // SỬA: Gọi qua $this->tourModel
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

        // SỬA: Gọi qua $this->tourModel
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
        json_decode($text);
        if (json_last_error() === JSON_ERROR_NONE) return $text;
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
            
            // 1. Xác định đường dẫn thư mục 'public/uploads/tours'
            // __DIR__ là src/controllers
            // Đi lùi ra ngoài: src/controllers -> src -> root -> public -> uploads -> tours
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/tours/';
            
            // 2. Tạo thư mục nếu chưa có
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    // Nếu không tạo được thư mục, log lỗi hoặc return rỗng
                    error_log("Không thể tạo thư mục upload: " . $uploadDir);
                    return [];
                }
            }

            $count = count($_FILES['images']['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    
                    $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                    // Tạo tên file duy nhất để tránh trùng
                    $uniqueName = time() . '_' . uniqid() . '.' . $ext;
                    
                    $targetPath = $uploadDir . $uniqueName;

                    // Di chuyển file từ temp sang thư mục upload
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $targetPath)) {
                        // CHỈ LƯU TÊN FILE VÀO DATABASE
                        // View sẽ tự thêm đường dẫn 'uploads/tours/'
                        $images[] = $uniqueName; 
                    }
                }
            }
        }
        return $images;
    }
}