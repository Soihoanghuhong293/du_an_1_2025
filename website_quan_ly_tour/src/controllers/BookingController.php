<?php
require_once BASE_PATH . '/src/models/BookingGuest.php';
require_once BASE_PATH . '/src/models/Booking.php';

class BookingController
{
    //lits
    public function index(): void
    {
        $bookings = Booking::all();

        view('bookings.index', [
            'bookings' => $bookings,
            'title' => 'Quản lý Booking'
        ]);
    }

 // hiển thị form tạo booking
    public function create()
    {
        $tours = Booking::getTours();
        $guides = Booking::getGuides();
        $statuses = Booking::getStatuses();

        view('bookings.create', [
            'tours' => $tours,
            'guides' => $guides,
            'statuses' => $statuses,
            'title' => 'Thêm Booking mới'
        ]);
    }


public function store()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('booking-create');
        return;
    }

    // ho tro xu li json
    $processJsonInput = function($input) {
        $input = trim($input ?? '');
        if ($input === '') {
            return null; 
        }
        // check input
        json_decode($input);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $input; // json giu nguyen
        }
        // Nếu là chữ thường, ép kiểu thành JSON string
        return json_encode($input, JSON_UNESCAPED_UNICODE);
    };
    // ------------------------------------------

    $data = [
        'tour_id'           => $_POST['tour_id'],
        'created_by'        => getCurrentUser()->id ?? 1,
        'assigned_guide_id' => !empty($_POST['guide_id']) ? $_POST['guide_id'] : null,
        'status'            => !empty($_POST['status']) ? $_POST['status'] : 1,
        
        // Format ngày tháng chuẩn MySQL
        'start_date'        => date('Y-m-d', strtotime($_POST['start_date'])),
        'end_date'          => date('Y-m-d', strtotime($_POST['end_date'])),
        
        'notes'             => $_POST['notes'],

        // xu li cot json
        'schedule_detail'   => $processJsonInput($_POST['schedule_detail']),
        'service_detail'    => $processJsonInput($_POST['service_detail']),
        'diary'             => $processJsonInput($_POST['diary']),
        'lists_file'        => $processJsonInput($_POST['lists_file']),
    ];

    if ($data['end_date'] < $data['start_date']) {
        die("Lỗi: Ngày kết thúc không được nhỏ hơn ngày bắt đầu.");
    }

    try {
        if (Booking::create($data)) {
            redirect("bookings");
        } else {
            die("Có lỗi xảy ra khi lưu Booking.");
        }
    } catch (\PDOException $e) {
        // In lỗi chi tiết nếu vẫn bị
        die("Lỗi Database: " . $e->getMessage());
    }
}    public function delete($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            die("Booking không tồn tại!");
        }

        if (Booking::delete($id)) {
            redirect("bookings");
        } else {
            die("Xóa thất bại!");
        }
    }

    public function show($id)
    {
        if (!$id) { header("Location: index.php?act=bookings"); exit; }

        $booking = Booking::getDetail($id);
        $logs = Booking::getLogs($id);
        
        //  Lấy danh sách khách hàng từ bảng mới
        $guests = BookingGuest::getByBookingId($id);

        if (!$booking) { echo "Booking không tồn tại!"; return; }

        $title = "Chi tiết Booking #" . $booking['id'];
        
        ob_start();
        require_once './views/bookings/show.php';
        $content = ob_get_clean();
        require_once './views/layouts/AdminLayout.php';
    }


    //thêm khách
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

    // xóa khách
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

    // Xử lý lưu phân phòng 
    public function updateRooms()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $booking_id = $_POST['booking_id'];
            $rooms = $_POST['rooms'] ?? []; 

            foreach ($rooms as $guest_id => $room_name) {
                BookingGuest::updateRoom($guest_id, $room_name);
            }

            header("Location: index.php?act=booking-show&id=" . $booking_id);
            exit;
        }
    }
}
