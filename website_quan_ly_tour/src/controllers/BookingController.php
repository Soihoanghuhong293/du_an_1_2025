<?php

require_once BASE_PATH . '/src/models/Booking.php';

class BookingController
{
    public function index()
    { 
        // lấy dữ liệu
        $bookings = Booking::all();
        // chuẩn bi dữ liệu cho wiew
        // extracc giúp chuyển mảng ['bookings => $...] thành biến booking để viêw dùng
        extract(['bookings' => $bookings]);
        $title =" Quản lí Booking";
     
        ob_start();
        require_once './views/bookings/index.php'; 
        $content = ob_get_clean();
        
        require_once './views/layouts/AdminLayout.php';
        
        


    }
}
