<?php
// src/controllers/BookingServiceController.php

require_once BASE_PATH . '/src/models/BookingService.php';

class BookingServiceController
{
    // Xử lý thêm dịch vụ
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $booking_id = $_POST['booking_id'];
            
            $data = [
                'booking_id'    => $booking_id,
                'service_type'  => $_POST['service_type'],
                'provider_name' => $_POST['provider_name'],
                'use_date'      => $_POST['use_date'],
                'quantity'      => $_POST['quantity'] ?? 1,
                'note'          => $_POST['note'] ?? '',
                'status'        => $_POST['status'] ?? 0
            ];

            BookingService::add($data);

            // Quay lại trang chi tiết và nhảy tới tab #operations
            header("Location: index.php?act=booking-show&id=" . $booking_id . "#operations");
            exit;
        }
        
        // Nếu không phải POST thì quay về danh sách
        header("Location: index.php?act=bookings");
    }

    // Xử lý xóa dịch vụ
    public function delete()
    {
        $id = $_GET['id'] ?? null;
        $booking_id = $_GET['booking_id'] ?? null;

        if ($id && $booking_id) {
            BookingService::delete($id);
            header("Location: index.php?act=booking-show&id=" . $booking_id . "#operations");
            exit;
        }
        
        header("Location: index.php?act=bookings");
    }
}