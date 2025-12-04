<?php

require_once __DIR__ . '/../helpers/database.php';

class Booking
{
    public static function all()
    {
        $conn = getDB(); // ✅ Đã đúng

        $sql = "SELECT 
                b.*, 
                t.name AS tour_name, 
                u_creator.name AS creator_name, 
                u_guide.name AS guide_name,
                ts.name AS status_name
            FROM bookings b
            LEFT JOIN tours t ON b.tour_id = t.id
            LEFT JOIN users u_creator ON b.created_by = u_creator.id
            LEFT JOIN users u_guide ON b.assigned_guide_id = u_guide.id
            LEFT JOIN tour_statuses ts ON b.status = ts.id
            ORDER BY b.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public static function create($data)
    {
        $conn = getDB();
        $sql = "INSERT INTO bookings (tour_id, created_by, assigned_guide_id, status, start_date, end_date, notes, created_at)
                VALUES (:tour_id, :created_by, :assigned_guide_id, :status, :start_date, :end_date, :notes, NOW())";

        $stmt = $conn->prepare($sql);

        $stmt -> bindParam(':tour_id', $data['tour_id']);
        $stmt -> bindParam(':created_by', $data['created_by']);
        $stmt->bindParam(':assigned_guide_id', $data['assigned_guide_id']);
        $stmt->bindParam(':status', $data['status']);
         $stmt->bindParam(':start_date', $data['start_date']);
         $stmt->bindParam(':end_date', $data['end_date']);
          $stmt->bindParam(':notes', $data['notes']);

          return $stmt -> execute();
    }



    public static function getTours(){
           $conn = getDB();
        
        $stmt = $conn->query("SELECT id, name FROM tours WHERE status = 1");
        return $stmt-> fetchAll(PDO::FETCH_ASSOC);
    }
    // 3. Hàm lấy danh sách HDV
    public static function getGuides() {
        // ❌ Bỏ dòng: global $conn;
        $conn = getDB(); // ✅ Sửa thành dòng này
        
        $stmt = $conn->query("SELECT id, name FROM users WHERE role = 'guide'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Hàm lấy Status
    public static function getStatuses() {
        // ❌ Bỏ dòng: global $conn;
        $conn = getDB(); // ✅ Sửa thành dòng này
        
        $stmt = $conn->query("SELECT id, name FROM tour_statuses");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function find($id)
    {
        $conn = getDB();
        $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function delete($id)
    {
        $conn = getDB();
        try {
            // Bắt đầu transaction
            $conn->beginTransaction();

            //  Xóa các log trạng thái liên quan đến booking này trước
            //  bảng booking_status_logs có khóa ngoại trỏ về bookings)
            $stmtLog = $conn->prepare("DELETE FROM booking_status_logs WHERE booking_id = :id");
            $stmtLog->bindParam(':id', $id);
            $stmtLog->execute();

            //  Xóa booking chính
            $stmtBooking = $conn->prepare("DELETE FROM bookings WHERE id = :id");
            $stmtBooking->bindParam(':id', $id);
            $stmtBooking->execute();

            
            $conn->commit();
            return true;
        } catch (Exception $e) {
           
            $conn->rollBack();
            return false;
        }
    }
}