<?php

require_once BASE_PATH . '/src/models/Booking.php';

class BookingController
{
   public function index(): void
{
    // 1. Lấy dữ liệu từ Model
    // (Đảm bảo bạn đã require model hoặc dùng autoloader)
    $bookings = Booking::all();

    // 2. Gọi hàm view helper
    view('bookings.index', [
        'bookings' => $bookings,
        'title'    => 'Quản lý Bookzcxing'
    ]);
}

    // 1. Hiển thị Form thêm mới
    public function create()
    {
        $tours = Booking::getTours();
        $guides = Booking::getGuides();
        $statuses = Booking::getStatuses();

        $title = "Thêm mới Booking";
        
        ob_start();
        require_once './views/bookings/create.php';
        $content = ob_get_clean();
        
        require_once './views/layouts/AdminLayout.php';
    }

    // 2. Xử lý lưu dữ liệu khi nhấn Submit
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lấy dữ liệu từ form
            $data = [
                'tour_id' => $_POST['tour_id'],
                // Nếu bạn có session login thì lấy ID người đang login: $_SESSION['user_id']
                // Tạm thời mình để cứng là 1 (Admin) theo DB mẫu
                'created_by' => 1, 
                'assigned_guide_id' => !empty($_POST['guide_id']) ? $_POST['guide_id'] : null,
                'status' => $_POST['status'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'notes' => $_POST['notes']
            ];

            // Gọi Model để lưu
            if (Booking::create($data)) {
                // Thành công thì chuyển về trang danh sách
               header("Location: bookings");
                exit;
            } else {
                echo "Có lỗi xảy ra, vui lòng thử lại!";
            }
        }
    }
    // ... Các hàm index, create, store giữ nguyên

    // 3. Xử lý xóa
    public function delete($id)
    {
        // Kiểm tra xem ID có tồn tại không
        $booking = Booking::find($id);

        if (!$booking) {
            // Có thể set session flash message lỗi ở đây
            echo "Booking không tồn tại!";
            return;
        }

        // Thực hiện xóa
        if (Booking::delete($id)) {
            // Xóa thành công, quay về trang danh sách
            header("Location: " . BASE_URL . "bookings"); 
            // Lưu ý: Đảm bảo đường dẫn header location đúng với router của bạn
            exit;
        } else {
            echo "Xóa thất bại! Có lỗi hệ thống.";
        }
    }
}
