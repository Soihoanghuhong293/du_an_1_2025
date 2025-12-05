<?php

require_once __DIR__ . '/../helpers/database.php';

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

        return $stmt->execute([
            ':tour_id' => $data['tour_id'],
            ':created_by' => $data['created_by'],
            ':assigned_guide_id' => $data['assigned_guide_id'],
            ':status' => $data['status'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':notes' => $data['notes']
        ]);
    }


    public static function getTours()
    {
        $conn = getDB();
        return $conn->query("SELECT id, name FROM tours WHERE status = 1")->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getGuides()
    {
        $conn = getDB();
        return $conn->query("SELECT id, name FROM users WHERE role = 'guide'")->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getStatuses()
    {
        $conn = getDB();
        return $conn->query("SELECT id, name FROM tour_statuses")->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function find($id)
    {
        $conn = getDB();
        $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function delete($id)
    {
        $conn = getDB();
        try {
            $conn->beginTransaction();

            $conn->prepare("DELETE FROM booking_status_logs WHERE booking_id = :id")
                 ->execute([':id' => $id]);

            $conn->prepare("DELETE FROM bookings WHERE id = :id")
                 ->execute([':id' => $id]);

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            return false;
        }
    }
    // ... (Các hàm cũ giữ nguyên)

    // 7. Lấy chi tiết Booking (Kèm tên Tour, tên HDV, tên người tạo)
   public static function getDetail($id)
    {
        $conn = getDB();
        $sql = "SELECT 
                    b.*, 
                    t.name AS tour_name,
                    t.price AS tour_price,
                    u_creator.name AS creator_name,
                    u_guide.name AS guide_name,
                    -- u_guide.phone AS guide_phone,  <-- XÓA HOẶC COMMENT DÒNG NÀY
                    ts.name AS status_name
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users u_creator ON b.created_by = u_creator.id
                LEFT JOIN users u_guide ON b.assigned_guide_id = u_guide.id
                LEFT JOIN tour_statuses ts ON b.status = ts.id
                WHERE b.id = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 8. Lấy lịch sử thay đổi trạng thái (Booking Logs)
    public static function getLogs($booking_id)
    {
        $conn = getDB();
        $sql = "SELECT 
                    log.*, 
                    u.name AS changer_name,
                    ts_old.name AS old_status_name,
                    ts_new.name AS new_status_name
                FROM booking_status_logs log
                LEFT JOIN users u ON log.changed_by = u.id
                LEFT JOIN tour_statuses ts_old ON log.old_status = ts_old.id
                LEFT JOIN tour_statuses ts_new ON log.new_status = ts_new.id
                WHERE log.booking_id = :id
                ORDER BY log.changed_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $booking_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   



    // ============================================================
    // ⭐ CHỨC NĂNG 1: HDV xem tour được phân công
    // ============================================================
    public static function getAssignedBookings($guideId)
    {
        $pdo = getDB();

        $sql = "SELECT b.*, t.name AS tour_name
                FROM bookings b
                JOIN tours t ON b.tour_id = t.id
                WHERE b.assigned_guide_id = :guide_id
                ORDER BY b.start_date DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['guide_id' => $guideId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ============================================================
    // ⭐ CHỨC NĂNG 2: Lấy danh sách khách trong booking
    // ============================================================
    public static function getCustomersByBooking($bookingId)
    {
        $pdo = getDB();

        $sql = "SELECT * FROM booking_customers WHERE booking_id = :bid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['bid' => $bookingId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ============================================================
    // ⭐ CHỨC NĂNG 3: Lấy nhật ký từ cột `diary`

    public static function getDiary($bookingId)
    {
        $pdo = getDB();

        $stmt = $pdo->prepare("SELECT diary FROM bookings WHERE id = ?");
        $stmt->execute([$bookingId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || empty($row['diary'])) {
            return ['entries' => []];
        }

        $data = json_decode($row['diary'], true);

        if (!is_array($data) || !isset($data['entries'])) {
            return ['entries' => []];
        }

        return $data;
    }


    // ⭐ CHỨC NĂNG 4: Lưu nhật ký bằng cách cập nhật cột `diary`
    // ============================================================
    public static function updateDiary($bookingId, $diary)
    {
        $pdo = getDB();

        $json = json_encode($diary, JSON_UNESCAPED_UNICODE);

        $stmt = $pdo->prepare("
            UPDATE bookings 
            SET diary = :diary, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':diary' => $json,
            ':id' => $bookingId
        ]);
    }
}
