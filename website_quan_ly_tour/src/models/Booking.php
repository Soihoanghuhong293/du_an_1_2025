<?php

require_once __DIR__ . '/../helpers/database.php'; // ⭐ FIX

class Booking
{
    public static function all()
    {
        $conn = getDB();

        $sql = "SELECT 
                b.*, 
                t.name AS tour_name, 
                u_creator.name AS creator_name, 
                u_guide.name AS guide_name,
                ts.name AS status_name
            FROM bookings b
            -- Nối bảng tours để lấy tên tour
            LEFT JOIN tours t ON b.tour_id = t.id
            -- Nối bảng users (lần 1) để lấy tên người tạo
            LEFT JOIN users u_creator ON b.created_by = u_creator.id
            -- Nối bảng users (lần 2) để lấy tên HDV
            LEFT JOIN users u_guide ON b.assigned_guide_id = u_guide.id
            -- Nối bảng status để lấy tên trạng thái (nếu cần)
            LEFT JOIN tour_statuses ts ON b.status = ts.id
            ORDER BY b.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
