<?php
require_once __DIR__ . '/../models/Booking.php';
require_once BASE_PATH . '/src/models/GuideProfile.php';

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
        // [QUAN TRỌNG] Đã thêm kiểm tra đăng nhập và quyền hạn
        requireLogin();
        $user = getCurrentUser();
        if (!$user->isGuide()) die("Bạn không có quyền!");

        $id = $_GET['id'] ?? null;
        if (!$id) die("Thiếu ID!");

        // [QUAN TRỌNG] Kiểm tra xem file này có thuộc tour mà HDV được giao không
        $this->checkOwnership($id, $user->id);

        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT assignment_file FROM bookings WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetchColumn();

        if (!$file) die("Không có file đính kèm!");

        // Sử dụng đường dẫn tuyệt đối an toàn
        $path = BASE_PATH . "/uploads/assignments/" . $file;

        if (!file_exists($path)) die("File vật lý không tồn tại!");

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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['booking_id'] ?? null;
            
            // Lấy ghi chú người dùng nhập. Nếu để trống thì dùng câu mặc định.
            $userNote = trim($_POST['confirm_note'] ?? '');
            $note = !empty($userNote) ? $userNote : 'HDV xác nhận tham gia tour';

            $userId = $_SESSION['user_id'] ?? null;

            if ($bookingId && $userId) {
                // Gọi hàm updateStatus với ghi chú tùy chỉnh
                $result = Booking::updateStatus($bookingId, 2, $userId, $note);

                if ($result) {
                    header("Location: index.php?act=guide-show&id=" . $bookingId);
                    exit;
                }
            }
        }
        
        // Nếu truy cập sai cách hoặc lỗi
        header("Location: index.php?act=guide-tours");
        exit;
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
    public function show() {
        // Kiểm tra đăng nhập (nếu cần)
        // ... 

        $id = $_GET['id'] ?? null;
        if (!$id) { header("Location: index.php?act=guide-tours"); exit; }

        $booking = Booking::getDetail($id);
        
        // Kiểm tra xem tour này có đúng là của HDV đang đăng nhập không
        // $currentUserId = $_SESSION['user_id'];
        // if ($booking['assigned_guide_id'] != $currentUserId) { die("Bạn không có quyền truy cập tour này"); }

        $guests = BookingGuest::getByBookingId($id);
        $services = BookingService::getByBookingId($id);

        view('guide.show', [
            'booking' => $booking,
            'guests' => $guests,
            'services' => $services,
            'title' => 'Chi tiết Tour: ' . $booking['tour_name']
        ]);
    }

    // [MỚI] Lưu nhật ký dành cho HDV
    public function saveDiary() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['booking_id'];
            $content = $_POST['diary_content'];
            
            // Tái sử dụng hàm cập nhật nhật ký của Booking Model
            $jsonContent = json_encode($content, JSON_UNESCAPED_UNICODE);
            Booking::updateDiaryData($id, $jsonContent);

            header("Location: index.php?act=guide-show&id=" . $id . "#diary");
            exit;
        }
    }
    // Kết thúc tour (Finish) - Chuyển sang status 3
    public function finish()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['booking_id'] ?? null;
            
            // Lấy ghi chú, nếu rỗng thì điền mặc định
            $userNote = trim($_POST['finish_note'] ?? '');
            $note = !empty($userNote) ? $userNote : 'HDV xác nhận đã hoàn tất tour';

            $userId = $_SESSION['user_id'] ?? null;

            if ($bookingId && $userId) {
                // Status 3 = Hoàn tất (Dựa theo bảng tour_statuses của bạn)
                $result = Booking::updateStatus($bookingId, 3, $userId, $note);

                if ($result) {
                    header("Location: index.php?act=guide-show&id=" . $bookingId);
                    exit;
                }
            }
        }
        
        header("Location: index.php?act=guide-tours");
        exit;
    }

    // Danh sách hướng dẫn viên
    public function list()
    {
        $pdo = getDB();
        $stmt = $pdo->prepare("
            SELECT gp.*, u.name, u.email, u.role, u.status 
            FROM guide_profiles gp 
            LEFT JOIN users u ON gp.user_id = u.id 
            ORDER BY gp.id ASC
        ");
        $stmt->execute();
        $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        view('guides.index', [
            'guides' => $guides,
            'title' => 'Danh sách hướng dẫn viên'
        ]);
    }
    // Hiển thị form thêm mới
    public function create()
    {
        // Lấy danh sách users để chọn user_id khi tạo hồ sơ HDV
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, name, email FROM users ORDER BY name ASC");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách tours để cho chọn vào history
        $stmt2 = $pdo->prepare("SELECT id, name FROM tours ORDER BY name ASC");
        $stmt2->execute();
        $tours = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        view('guides.create', [
            'users' => $users,
            'tours' => $tours,
            'title' => 'Thêm hướng dẫn viên'
        ]);
            }

    // Lưu hướng dẫn viên mới
    public function store()
    {
        // Chuẩn hóa dữ liệu cho bảng guide_profiles
        // Sanitize and normalize inputs
        $ratingInput = $_POST['rating'] ?? '';
        if ($ratingInput === '' || $ratingInput === null) {
            $rating = 0;
        } else {
            // allow decimal like 4.5, clamp to reasonable range 0-5
            $rating = floatval(str_replace(',', '.', $ratingInput));
            if ($rating < 0) $rating = 0;
            if ($rating > 5) $rating = 5;
        }

        $data = [
            'user_id' => $_POST['user_id'] ?? null,
            'birthdate' => $_POST['birthdate'] ?? '',
            'phone' => trim($_POST['phone'] ?? ''),
            'certificate' => trim($_POST['certificate'] ?? ''),
            'languages' => trim($_POST['languages'] ?? ''),
            'experience' => trim($_POST['experience'] ?? ''),
            // history may be provided as selected tour IDs (history_tours[]) or free text
            'history' => trim($_POST['history'] ?? ''),
            'rating' => $rating,
            'health_status' => trim($_POST['health_status'] ?? ''),
            'group_type' => trim($_POST['group_type'] ?? ''),
            'specialty' => trim($_POST['specialty'] ?? ''),
        ];

        // If user selected tours for history, convert to JSON stored format
        if (!empty($_POST['history_tours']) && is_array($_POST['history_tours'])) {
            $ids = array_values(array_map('intval', $_POST['history_tours']));
            $data['history'] = json_encode(['tours' => $ids], JSON_UNESCAPED_UNICODE);
            // preserve for old input
            $_SESSION['old']['history_tours'] = $ids;
        }

        // Server-side validation
        $errors = [];
        if (empty($data['user_id'])) {
            $errors[] = 'Vui lòng chọn người dùng (User).';
        }
        if (empty($data['phone'])) {
            $errors[] = 'Vui lòng nhập số điện thoại.';
        }

        if (!empty($errors)) {
            // Save errors and old input into session and redirect back to create form
            $_SESSION['errors'] = $errors;
            // ensure history_tours preserved if present
            if (isset($_SESSION['old']['history_tours'])) {
                $tmp = $_SESSION['old'];
                $tmp = array_merge($data, ['history_tours' => $_SESSION['old']['history_tours']]);
                $_SESSION['old'] = $tmp;
            } else {
                $_SESSION['old'] = $data;
            }
            header('Location: index.php?act=guides/create');
            exit;
        }

        // Xử lý upload avatar nếu có
        if (!empty($_FILES['avatar']['name'])) {
            $uploadDir = BASE_PATH . '/public/uploads/guides/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('guide_') . '.' . $ext;
            $target = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                $data['avatar'] = $filename;
            }
        }

        $ok = GuideProfile::create($data);
        // clear old/errors
        unset($_SESSION['errors'], $_SESSION['old']);
        header('Location: index.php?act=guides');
        exit;
    }

    // Hiển thị form sửa
    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?act=guides');
            exit;
        }
        $guide = GuideProfile::find($id);

        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, name, email FROM users ORDER BY name ASC");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách tours để chọn trong edit
        $stmt2 = $pdo->prepare("SELECT id, name FROM tours ORDER BY name ASC");
        $stmt2->execute();
        $tours = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        view('guides.edit', [
            'guide' => $guide,
            'users' => $users,
            'tours' => $tours,
            'title' => 'Cập nhật hướng dẫn viên'
        ]);
            }

    // Cập nhật hướng dẫn viên
    public function update()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: index.php?act=guides');
            exit;
        }
        // Lấy dữ liệu hiện có để xử lý avatar cũ
        $existing = GuideProfile::find($id);

        // Sanitize and normalize inputs for update
        $ratingInput = $_POST['rating'] ?? null;
        if ($ratingInput === '' || $ratingInput === null) {
            $rating = $existing['rating'] ?? 0;
        } else {
            $rating = floatval(str_replace(',', '.', $ratingInput));
            if ($rating < 0) $rating = 0;
            if ($rating > 5) $rating = 5;
        }

        $data = [
            'user_id' => $_POST['user_id'] ?? ($existing['user_id'] ?? null),
            'birthdate' => $_POST['birthdate'] ?? ($existing['birthdate'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ($existing['phone'] ?? '')),
            'certificate' => trim($_POST['certificate'] ?? ($existing['certificate'] ?? '')),
            'languages' => trim($_POST['languages'] ?? ($existing['languages'] ?? '')),
            'experience' => trim($_POST['experience'] ?? ($existing['experience'] ?? '')),
            'history' => trim($_POST['history'] ?? ($existing['history'] ?? '')),
            'rating' => $rating,
            'health_status' => trim($_POST['health_status'] ?? ($existing['health_status'] ?? '')),
            'group_type' => trim($_POST['group_type'] ?? ($existing['group_type'] ?? '')),
            'specialty' => trim($_POST['specialty'] ?? ($existing['specialty'] ?? '')),
        ];

        // If user selected tours for history in edit, convert to JSON
        if (!empty($_POST['history_tours']) && is_array($_POST['history_tours'])) {
            $ids = array_values(array_map('intval', $_POST['history_tours']));
            $data['history'] = json_encode(['tours' => $ids], JSON_UNESCAPED_UNICODE);
            // preserve for old input
            $_SESSION['old']['history_tours'] = $ids;
        }

        // Server-side validation for update
        $errors = [];
        if (empty($data['user_id'])) {
            $errors[] = 'Vui lòng chọn người dùng (User).';
        }
        if (empty($data['phone'])) {
            $errors[] = 'Vui lòng nhập số điện thoại.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: index.php?act=guides/edit&id=' . $id);
            exit;
        }

        // Xử lý upload avatar mới
        if (!empty($_FILES['avatar']['name'])) {
            $uploadDir = BASE_PATH . '/public/uploads/guides/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('guide_') . '.' . $ext;
            $target = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                // xóa avatar cũ nếu có
                if (!empty($existing['avatar']) && file_exists($uploadDir . $existing['avatar'])) {
                    @unlink($uploadDir . $existing['avatar']);
                }
                $data['avatar'] = $filename;
            }
        }

        GuideProfile::update($id, $data);
        header('Location: index.php?act=guides');
        exit;
    }

    // Xóa hướng dẫn viên
    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            // xóa avatar file nếu có
            $existing = GuideProfile::find($id);
            if ($existing && !empty($existing['avatar'])) {
                $file = BASE_PATH . '/public/uploads/guides/' . $existing['avatar'];
                if (file_exists($file)) @unlink($file);
            }
            GuideProfile::delete($id);
        }
        header('Location: index.php?act=guides');
        exit;
    }

    // Hiển thị chi tiết hướng dẫn viên
    public function showDetail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?act=guides');
            exit;
        }

        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT gp.*, u.name, u.email, u.role, u.status FROM guide_profiles gp LEFT JOIN users u ON gp.user_id = u.id WHERE gp.id = ? LIMIT 1");
        $stmt->execute([$id]);
        $guide = $stmt->fetch(PDO::FETCH_ASSOC);

        view('guides.show', [
            'guide' => $guide,
            'title' => 'Chi tiết hướng dẫn viên'
        ]);
            }
}