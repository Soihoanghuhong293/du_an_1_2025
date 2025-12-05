<?php
require_once __DIR__ . '/../helpers/database.php';

class BookingGuest
{
    // 1. Lấy danh sách khách theo Booking ID
    public static function getByBookingId($bookingId)
    {
        $conn = getDB();
        $stmt = $conn->prepare("SELECT * FROM booking_guests WHERE booking_id = :booking_id ORDER BY room_name ASC, id ASC");
        $stmt->bindParam(':booking_id', $bookingId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Thêm khách mới
    public static function add($data)
    {
        $conn = getDB();
        $sql = "INSERT INTO booking_guests (booking_id, full_name, gender, birthdate, phone, note, room_name) 
                VALUES (:booking_id, :full_name, :gender, :birthdate, :phone, :note, :room_name)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($data);
    }

    // 3. Xóa khách
    public static function delete($id)
    {
        $conn = getDB();
        $stmt = $conn->prepare("DELETE FROM booking_guests WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // 4. Cập nhật phòng (Phân phòng)
    public static function updateRoom($id, $roomName)
    {
        $conn = getDB();
        $stmt = $conn->prepare("UPDATE booking_guests SET room_name = :room_name WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':room_name', $roomName);
        return $stmt->execute();
    }
}