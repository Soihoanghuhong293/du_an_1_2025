<?php
require_once __DIR__ . '/../models/Booking.php';

class GuideController
{
    public function assignedTours()
    {
        requireLogin(); // kiểm tra login

        $user = getCurrentUser();

        if (!$user->isGuide()) {
            die("Bạn không có quyền truy cập!");
        }

        // Lấy danh sách booking được giao cho HDV
        $bookings = Booking::getAssignedBookings($user->id);

        // GỌI VIEW ĐÚNG CÁCH
        view('guide.assigned_tours', [
            'bookings' => $bookings,
            'title' => 'Danh sách tour được phân công'
        ]);
    }
    public function customers()
{
    requireLogin();
    $user = getCurrentUser();

    if (!$user->isGuide()) {
        die("Bạn không có quyền truy cập!");
    }

    $bookingId = $_GET['id'] ?? null;

    if (!$bookingId) {
        die("Thiếu ID booking!");
    }

    // Lấy danh sách khách
    $customers = Booking::getCustomersByBooking($bookingId);

    // Lấy thông tin tour để hiển thị
    $bookings = Booking::getAssignedBookings($user->id);
    $currentBooking = null;

    foreach ($bookings as $b) {
        if ($b['id'] == $bookingId) {
            $currentBooking = $b;
            break;
        }
    }

    if (!$currentBooking) {
        die("Bạn không được phân công tour này!");
    }

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

    // 👉 Nếu không có id => chuyển về danh sách tour
    if (!$bookingId) {
        redirect("guide-tours");
        return;
    }

    // Kiểm tra xem có đúng tour được phân công không
    $assigned = Booking::getAssignedBookings($user->id);
    $allowed = false;

    foreach ($assigned as $a) {
        if ((int)$a['id'] === (int)$bookingId) {
            $allowed = true;
            break;
        }
    }

    if (!$allowed) die("Bạn không được phân công tour này!");

    // Lấy nhật ký hiện tại
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

    $bookingId = $_POST['booking_id'];
    $entry = trim($_POST['entry']);

    // Lấy diary hiện có
    $diary = Booking::getDiary($bookingId);

    // Append entry mới
    $diary["entries"][] = date("Y-m-d") . ": " . $entry;

    // Cập nhật database
    Booking::updateDiary($bookingId, $diary);

    // ⭐ SỬA LỖI redirect
    redirect("guide-diary", ["id" => $bookingId]);
}
public function schedule()
{
    requireLogin();
    $user = getCurrentUser();

    if (!$user->isGuide()) die("Bạn không có quyền!");

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
    $id = $_GET['id'] ?? null;
    if (!$id) die("Thiếu ID!");

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT assignment_file FROM bookings WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();

    if (!$file) die("Không có file!");

    $path = BASE_PATH . "/uploads/assignments/" . $file;

    if (!file_exists($path)) die("File không tồn tại!");

    header("Content-Disposition: attachment; filename=" . basename($path));
    header("Content-Type: application/octet-stream");
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

    Booking::updateStatus($id, 4, $user->id, "HDV từ chối tour"); // ví dụ status 5 = từ chối

    redirect("guide-tours");
}

}
