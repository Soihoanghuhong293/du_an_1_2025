<?php
require_once __DIR__ . '/../models/Booking.php';

class GuideController
{
    // --- Helper: Kiểm tra quyền sở hữu Tour ---
    // Hàm này giúp tránh việc lặp lại code kiểm tra xem HDV có được giao tour này không
    private function checkOwnership($bookingId, $userId)
    {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id FROM bookings WHERE id = ? AND assigned_guide_id = ?");
        $stmt->execute([$bookingId, $userId]);
        
        if (!$stmt->fetch()) {
            die("Lỗi: Bạn không được phân công tour này hoặc tour không tồn tại!");
        }
    }

    public function assignedTours()
    {
        requireLogin();
        $user = getCurrentUser();

        if (!$user->isGuide()) {
            die("Bạn không có quyền truy cập!");
        }

        // Lấy danh sách booking được giao cho HDV
        $bookings = Booking::getAssignedBookings($user->id);

        view('guide.assigned_tours', [
            'bookings' => $bookings,
            'title' => 'Danh sách tour được phân công'
        ]);
    }

    public function customers()
    {
        requireLogin();
        $user = getCurrentUser();
        if (!$user->isGuide()) die("Bạn không có quyền truy cập!");

        $bookingId = $_GET['id'] ?? null;
        if (!$bookingId) die("Thiếu ID booking!");

        // 1. Kiểm tra quyền sở hữu trước
        $this->checkOwnership($bookingId, $user->id);

        // 2. Lấy thông tin chi tiết booking (để hiển thị tên tour, ngày đi...)
        // Giả sử Model có hàm getDetail($id) hoặc lấy từ danh sách
        // Ở đây query trực tiếp cho nhanh và chính xác
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT b.*, t.name as tour_name FROM bookings b JOIN tours t ON b.tour_id = t.id WHERE b.id = ?");
        $stmt->execute([$bookingId]);
        $currentBooking = $stmt->fetch(PDO::FETCH_ASSOC);

        // 3. Lấy danh sách khách
        $customers = Booking::getCustomersByBooking($bookingId);

        view('guide.customers', [
            'booking' => $currentBooking,
            'customers' => $customers
        ]);
    }

    public function diary()
    {
        requireLogin();
        $user = getCurrentUser();
        if (!$user->isGuide()) die("Bạn không có quyền!");

        $bookingId = $_GET['id'] ?? null;
        if (!$bookingId) {
            redirect("guide-tours");
            return;
        }

        // Kiểm tra quyền sở hữu (Thay thế vòng lặp foreach kém hiệu quả)
        $this->checkOwnership($bookingId, $user->id);

        // Lấy nhật ký
        $diary = Booking::getDiary($bookingId);

        view("guide.diary", [
            "booking_id" => $bookingId,
            "diary" => $diary
        ]);
    }

    public function diaryStore()
    {
        requireLogin();
        $user = getCurrentUser();
        if (!$user->isGuide()) die("Bạn không có quyền!");

        $bookingId = $_POST['booking_id'] ?? null;
        $entry = trim($_POST['entry'] ?? '');

        if (!$bookingId || empty($entry)) {
            die("Dữ liệu không hợp lệ");
        }

        // Kiểm tra quyền sở hữu trước khi lưu
        $this->checkOwnership($bookingId, $user->id);

        // Lấy diary hiện có
        $diary = Booking::getDiary($bookingId);
        
        // Kiểm tra nếu diary chưa có cấu trúc mảng
        if (!is_array($diary)) {
            $diary = ["entries" => []];
        }

        // Append entry mới kèm giờ phút cụ thể
        $diary["entries"][] = date("H:i d/m/Y") . ": " . $entry;

        // Cập nhật database
        Booking::updateDiary($bookingId, $diary);

        redirect("guide-diary", ["id" => $bookingId]);
    }

    public function schedule()
    {
        requireLogin();
        $user = getCurrentUser();
        if (!$user->isGuide()) die("Bạn không có quyền!");

        // Có thể tái sử dụng Booking::getAssignedBookings nếu hàm đó đã sắp xếp theo ngày
        // Nếu muốn giữ SQL riêng để tùy chỉnh order:
        $pdo = getDB();
        $stmt = $pdo->prepare("
            SELECT 
                b.*, 
                t.name AS tour_name
            FROM bookings b
            JOIN tours t ON b.tour_id = t.id
            WHERE b.assigned_guide_id = ?
            ORDER BY b.start_date ASC
        ");
        $stmt->execute([$user->id]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        view("guide.schedule", ["items" => $list]);
    }

    public function downloadAssignment()
    {
        requireLogin();
        $user = getCurrentUser();
        if (!$user->isGuide()) die("Bạn không có quyền!");
    
        $id = $_GET['id'] ?? null;
        if (!$id) die("Thiếu ID!");
    
        // Kiểm tra quyền sở hữu
        $this->checkOwnership($id, $user->id);
    
        $pdo = getDB();
        
        // Lấy cột lists_file (đang dùng để lưu file upload của booking)
        $stmt = $pdo->prepare("SELECT lists_file FROM bookings WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetchColumn();
    
        if (!$data) {
            die("Booking này không có file đính kèm!");
        }
    
        // lists_file là JSON -> giải mã về mảng
        $files = json_decode($data, true);
    
        if (!is_array($files) || count($files) == 0) {
            die("Không tìm thấy file hợp lệ!");
        }
    
        // Lấy file đầu tiên (hoặc bạn có thể chọn file theo index)
        $file = $files[0];
    
        // Đường dẫn file thực tế
        $path = BASE_PATH . "/uploads/bookings/" . $file;
    
        if (!file_exists($path)) {
            die("File vật lý không tồn tại!");
        }
    
        // Force download
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . basename($path));
        header("Content-Type: application/octet-stream");
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($path));
    
        readfile($path);
        exit;
    }
        public function confirm()
    {
        requireLogin();
        $user = getCurrentUser();
        if (!$user->isGuide()) die("Bạn không có quyền!");

        $id = $_GET['id'] ?? null;
        if (!$id) die("Thiếu ID booking!");

        // [QUAN TRỌNG] Phải check quyền sở hữu trước khi update
        $this->checkOwnership($id, $user->id);

        // Status 2: Đã xác nhận (Ví dụ)
        Booking::updateStatus($id, 2, $user->id, "HDV xác nhận tham gia tour");

        redirect("guide-tours");
    }

    public function reject()
    {
        requireLogin();
        $user = getCurrentUser();
        if (!$user->isGuide()) die("Bạn không có quyền!");

        $id = $_GET['id'] ?? null;
        if (!$id) die("Thiếu ID booking!");

        // [QUAN TRỌNG] Phải check quyền sở hữu trước khi update
        $this->checkOwnership($id, $user->id);

        // Status 4 hoặc 5: Từ chối
        Booking::updateStatus($id, 4, $user->id, "HDV từ chối tour"); 

        redirect("guide-tours");
    }
}