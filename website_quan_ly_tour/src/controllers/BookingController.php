<?php
require_once BASE_PATH . '/src/models/BookingGuest.php'; // <--- Thêm dòng này
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
    // ...

    // 4. Hiển thị chi tiết (Show)
    public function show($id)
    {
        if (!$id) { header("Location: index.php?act=bookings"); exit; }

        $booking = Booking::getDetail($id);
        $logs = Booking::getLogs($id);
        
        // 👇 Lấy danh sách khách hàng từ bảng mới
        $guests = BookingGuest::getByBookingId($id);

        if (!$booking) { echo "Booking không tồn tại!"; return; }

        $title = "Chi tiết Booking #" . $booking['id'];
        
        ob_start();
        require_once './views/bookings/show.php';
        $content = ob_get_clean();
        require_once './views/layouts/AdminLayout.php';
    }

    // --- CÁC HÀM MỚI ---

    // 1. Xử lý thêm khách
    public function addGuest()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $booking_id = $_POST['booking_id'];
            $data = [
                ':booking_id' => $booking_id,
                ':full_name'  => $_POST['full_name'],
                ':gender'     => $_POST['gender'],
                ':birthdate'  => !empty($_POST['birthdate']) ? $_POST['birthdate'] : null,
                ':phone'      => $_POST['phone'],
                ':note'       => $_POST['note'],
                ':room_name'  => 'Chưa xếp' // Mặc định
            ];

            BookingGuest::add($data);
            header("Location: index.php?act=booking-show&id=" . $booking_id);
            exit;
        }
    }

    // 2. Xử lý xóa khách
    public function deleteGuest()
    {
        $guest_id = $_GET['guest_id'] ?? null;
        $booking_id = $_GET['booking_id'] ?? null;

        if ($guest_id && $booking_id) {
            BookingGuest::delete($guest_id);
            header("Location: index.php?act=booking-show&id=" . $booking_id);
            exit;
        }
    }

    // 3. Xử lý lưu phân phòng (Cập nhật hàng loạt)
    public function updateRooms()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $booking_id = $_POST['booking_id'];
            $rooms = $_POST['rooms'] ?? []; // Mảng: [guest_id => room_name]

            foreach ($rooms as $guest_id => $room_name) {
                BookingGuest::updateRoom($guest_id, $room_name);
            }

            header("Location: index.php?act=booking-show&id=" . $booking_id);
            exit;
        }
    }
}
