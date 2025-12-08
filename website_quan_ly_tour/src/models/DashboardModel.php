<?php
// src/models/DashboardModel.php
require_once __DIR__ . '/../helpers/database.php';

class DashboardModel {
    
    // 1. Lấy thống kê tổng quan (4 ô trên cùng)
    public static function getSummary() {
        $db = getDB();
        
        // Tổng Booking
        $totalBookings = $db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
        
        // Tổng Khách hàng
        $totalGuests = $db->query("SELECT COUNT(*) FROM booking_guests")->fetchColumn();

        // Tổng Doanh thu (Giả sử doanh thu = giá tour trong bảng tours. 
        // Thực tế bạn nên có cột total_price trong bảng bookings)
        $revenue = $db->query("
            SELECT SUM(t.price) 
            FROM bookings b 
            JOIN tours t ON b.tour_id = t.id 
            WHERE b.status = 3 -- Chỉ tính tour Hoàn tất
        ")->fetchColumn();

        // Tổng Chi phí (Từ bảng booking_services)
        $cost = $db->query("
            SELECT SUM(cost) 
            FROM booking_services 
            WHERE status = 2 -- Chỉ tính dịch vụ đã chốt
        ")->fetchColumn();

        return [
            'bookings' => $totalBookings,
            'guests'   => $totalGuests,
            'revenue'  => $revenue ?? 0,
            'profit'   => ($revenue ?? 0) - ($cost ?? 0)
        ];
    }

    // 2. Doanh thu theo tháng (Cho biểu đồ cột)
    public static function getRevenueByMonth() {
        $db = getDB();
        $sql = "
            SELECT 
                MONTH(b.start_date) as month, 
                SUM(t.price) as total
            FROM bookings b
            JOIN tours t ON b.tour_id = t.id
            WHERE b.status = 3 AND YEAR(b.start_date) = YEAR(CURRENT_DATE())
            GROUP BY MONTH(b.start_date)
            ORDER BY month ASC
        ";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Tỷ lệ trạng thái Booking (Cho biểu đồ tròn)
    public static function getStatusRatio() {
        $db = getDB();
        $sql = "
            SELECT 
                ts.name as status_name, 
                COUNT(b.id) as count
            FROM bookings b
            JOIN tour_statuses ts ON b.status = ts.id
            GROUP BY ts.name
        ";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}