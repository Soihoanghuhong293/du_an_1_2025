<?php
// src/models/BookingService.php

require_once __DIR__ . '/../helpers/database.php';

class BookingService {
    
    // Lấy danh sách dịch vụ theo booking_id
    public static function getByBookingId($bookingId) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM booking_services WHERE booking_id = ? ORDER BY use_date ASC");
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm dịch vụ mới
    public static function add($data) {
        $db = getDB();
        $sql = "INSERT INTO booking_services (booking_id, service_type, provider_name, use_date, quantity, note, status) 
                VALUES (:booking_id, :service_type, :provider_name, :use_date, :quantity, :note, :status)";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':booking_id'    => $data['booking_id'],
            ':service_type'  => $data['service_type'],
            ':provider_name' => $data['provider_name'],
            ':use_date'      => $data['use_date'],
            ':quantity'      => $data['quantity'],
            ':note'          => $data['note'],
            ':status'        => $data['status']
        ]);
    }

    // Xóa dịch vụ
    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM booking_services WHERE id = ?");
        return $stmt->execute([$id]);
    }
}