<?php

require_once __DIR__ . '/../helpers/database.php'; // ⭐ FIX

class Booking
{
    public static function all()
    {
        $conn = getDB();

        $sql = "SELECT * FROM bookings ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
