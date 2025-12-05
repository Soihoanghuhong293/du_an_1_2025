<?php

require_once BASE_PATH . '/src/models/Booking.php';

class BookingController
{
    // =====================================================
    // ⭐ 1. Danh sách Booking
    // =====================================================
    public function index(): void
    {
        $bookings = Booking::all();

        view('bookings.index', [
            'bookings' => $bookings,
            'title' => 'Quản lý Booking'
        ]);
    }

    // =====================================================
    // ⭐ 2. Hiển thị form tạo Booking
    // =====================================================
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

    // =====================================================
    // ⭐ 3. Lưu Booking vào DB
    // =====================================================
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('booking-create');
            return;
        }

        $data = [
            'tour_id' => $_POST['tour_id'],
            'created_by' => getCurrentUser()->id ?? 1,
            'assigned_guide_id' => !empty($_POST['guide_id']) ? $_POST['guide_id'] : null,
            'status' => $_POST['status'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'notes' => $_POST['notes']
        ];

        if (Booking::create($data)) {
            redirect("bookings");
        } else {
            die("Có lỗi xảy ra khi lưu Booking.");
        }
    }

    // =====================================================
    // ⭐ 4. Xóa Booking
    // =====================================================
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
}
