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

    // 1. Helper xử lý JSON cho các trường Text (giữ nguyên của bạn)
    $processJsonInput = function($input) {
        $input = trim($input ?? '');
        if ($input === '') return null;
        json_decode($input);
        if (json_last_error() === JSON_ERROR_NONE) return $input;
        return json_encode($input, JSON_UNESCAPED_UNICODE);
    };

    // =================================================================
    // 2. XỬ LÝ FILE UPLOAD (ĐOẠN CODE MỚI THÊM)
    // =================================================================
    $filePaths = []; // Mảng chứa tên các file upload thành công

    if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
        // Đường dẫn thư mục lưu (Bạn nhớ tạo thư mục này trong dự án: public/uploads/bookings hoặc tương tự)
        // Dùng BASE_PATH nếu cần đường dẫn tuyệt đối, ví dụ:
        // $uploadDir = BASE_PATH . '/public/uploads/bookings/'; 
        // Ở đây mình ví dụ đường dẫn tương đối:
        $uploadDir = 'uploads/bookings/'; 
        
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $countFiles = count($_FILES['files']['name']);

        for ($i = 0; $i < $countFiles; $i++) {
            $fileName = basename($_FILES['files']['name'][$i]);
            $tmpName  = $_FILES['files']['tmp_name'][$i];
            $error    = $_FILES['files']['error'][$i];

            if ($error === UPLOAD_ERR_OK) {
                // Đổi tên file để tránh trùng: timestamp_tenfile
                $newFileName = time() . '_' . $i . '_' . $fileName;
                $targetPath = $uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    // Chỉ lưu tên file vào mảng để sau này gọi ra
                    $filePaths[] = $newFileName;
                }
            }
        }
    }

    // Chuyển mảng tên file thành JSON để lưu vào DB
    $jsonFiles = !empty($filePaths) ? json_encode($filePaths, JSON_UNESCAPED_UNICODE) : null;
    // =================================================================


    // 3. Chuẩn bị dữ liệu lưu DB
    $data = [
        'tour_id'           => $_POST['tour_id'],
        'created_by'        => getCurrentUser()->id ?? 1,
        'assigned_guide_id' => !empty($_POST['guide_id']) ? $_POST['guide_id'] : null,
        'status'            => !empty($_POST['status']) ? $_POST['status'] : 1,
        
        'start_date'        => date('Y-m-d', strtotime($_POST['start_date'])),
        'end_date'          => date('Y-m-d', strtotime($_POST['end_date'])),
        
        'notes'             => $_POST['notes'],

        'schedule_detail'   => $processJsonInput($_POST['schedule_detail']),
        'service_detail'    => $processJsonInput($_POST['service_detail']),
        'diary'             => $processJsonInput($_POST['diary']),

        // SỬA DÒNG NÀY: Thay vì lấy từ POST, ta lấy biến $jsonFiles vừa xử lý ở trên
        'lists_file'        => $jsonFiles, 
    ];

    // Kiểm tra logic ngày
    if ($data['end_date'] < $data['start_date']) {
        die("Lỗi: Ngày kết thúc không được nhỏ hơn ngày bắt đầu.");
    }

    // Lưu vào DB
    try {
        if (Booking::create($data)) {
            redirect("bookings");
        } else {
            die("Có lỗi xảy ra khi lưu Booking.");
        }
    } catch (\PDOException $e) {
        die("Lỗi Database: " . $e->getMessage());
    }
}
   public function delete($id)
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
