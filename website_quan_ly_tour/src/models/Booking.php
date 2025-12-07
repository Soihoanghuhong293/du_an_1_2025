<?php

require_once __DIR__ . '/../helpers/database.php';

class Booking
{
    // Lấy tất cả booking
    public static function all()
    {
        $db = getDB();

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

        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tạo booking mới (Đã bao gồm lists_file)
    public static function create($data)
    {
        $db = getDB();
        $sql = "INSERT INTO bookings (
                    tour_id, created_by, assigned_guide_id, status, 
                    start_date, end_date, notes, 
                    schedule_detail, service_detail, diary, lists_file, 
                    created_at
                )
                VALUES (
                    :tour_id, :created_by, :assigned_guide_id, :status, 
                    :start_date, :end_date, :notes, 
                    :schedule_detail, :service_detail, :diary, :lists_file, 
                    NOW()
                )";

        $stmt = $db->prepare($sql);

        return $stmt->execute([
            ':tour_id'           => $data['tour_id'],
            ':created_by'        => $data['created_by'],
            ':assigned_guide_id' => $data['assigned_guide_id'],
            ':status'            => $data['status'],
            ':start_date'        => $data['start_date'],
            ':end_date'          => $data['end_date'],
            ':notes'             => $data['notes'],
            ':schedule_detail'   => $data['schedule_detail'] ?? null,
            ':service_detail'    => $data['service_detail'] ?? null,
            ':diary'             => $data['diary'] ?? null,
            ':lists_file'        => $data['lists_file'] ?? null, // Cột lưu JSON file upload
        ]);
    }

    public static function getTours()
    {
        $db = getDB();
        return $db->query("SELECT id, name FROM tours WHERE status = 1")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getGuides()
    {
        $db = getDB();
        return $db->query("SELECT id, name FROM users WHERE role = 'guide'")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getStatuses()
    {
        $db = getDB();
        return $db->query("SELECT id, name FROM tour_statuses")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM bookings WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Xóa booking (Sử dụng Transaction an toàn)
    public static function delete($id)
    {
        $db = getDB();
        try {
            $db->beginTransaction();

            // Xóa log trạng thái trước
            $db->prepare("DELETE FROM booking_status_logs WHERE booking_id = :id")
               ->execute([':id' => $id]);

            // Xóa file đính kèm (Nếu muốn xóa cả file vật lý thì cần code thêm logic ở Controller trước khi xóa DB)
            
            // Xóa booking
            $db->prepare("DELETE FROM bookings WHERE id = :id")
               ->execute([':id' => $id]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    // Lấy chi tiết Booking 
    public static function getDetail($id)
    {
        $db = getDB();
        $sql = "SELECT 
                    b.*, 
                    t.name AS tour_name,
                    t.price AS tour_price,
                    u_creator.name AS creator_name,
                    u_guide.name AS guide_name,
                    ts.name AS status_name
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users u_creator ON b.created_by = u_creator.id
                LEFT JOIN users u_guide ON b.assigned_guide_id = u_guide.id
                LEFT JOIN tour_statuses ts ON b.status = ts.id
                WHERE b.id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy lịch sử thay đổi trạng thái (Booking Logs)
    public static function getLogs($booking_id)
    {
        $db = getDB();
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

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $booking_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // ⭐ CHỨC NĂNG 1: HDV xem tour được phân công
    // ============================================================
    public static function getAssignedBookings($guideId)
    {
        $db = getDB();
        $sql = "SELECT b.*, t.name AS tour_name
                FROM bookings b
                JOIN tours t ON b.tour_id = t.id
                WHERE b.assigned_guide_id = :guide_id
                ORDER BY b.start_date DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute(['guide_id' => $guideId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // ⭐ CHỨC NĂNG 2: Lấy danh sách khách trong booking
    // ============================================================
    public static function getCustomersByBooking($bookingId)
    {
        $db = getDB();
        $sql = "SELECT * FROM booking_customers WHERE booking_id = :bid";
        $stmt = $db->prepare($sql);
        $stmt->execute(['bid' => $bookingId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // ⭐ CHỨC NĂNG 3: Lấy nhật ký từ cột `diary`
    // ============================================================
    public static function getDiary($bookingId)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT diary FROM bookings WHERE id = ?");
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

    // ============================================================
    // ⭐ CHỨC NĂNG 4: Lưu nhật ký bằng cách cập nhật cột `diary`
    // ============================================================
    public static function updateDiary($bookingId, $diary)
    {
        $db = getDB();
        $json = json_encode($diary, JSON_UNESCAPED_UNICODE);

        $stmt = $db->prepare("
            UPDATE bookings 
            SET diary = :diary, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':diary' => $json,
            ':id' => $bookingId
        ]);
    }

    // ============================================================
    // ⭐ CẬP NHẬT TRẠNG THÁI (Có Transaction an toàn)
    // ============================================================
    public static function updateStatus($bookingId, $newStatus, $userId, $note = null)
    {
        $db = getDB();

        try {
            $db->beginTransaction();

            // 1. Lấy trạng thái cũ
            $stmt = $db->prepare("SELECT status FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $oldStatus = $stmt->fetchColumn();

            if ($oldStatus === false) {
                $db->rollBack();
                return false;
            }

            // 2. Cập nhật trạng thái mới
            $stmt = $db->prepare("UPDATE bookings SET status = :status WHERE id = :id");
            $stmt->execute([
                ':status' => $newStatus,
                ':id'     => $bookingId
            ]);

            // 3. Lưu log lịch sử
            $stmt = $db->prepare("
                INSERT INTO booking_status_logs (booking_id, old_status, new_status, changed_by, note)
                VALUES (:booking_id, :old_status, :new_status, :changed_by, :note)
            ");
            $stmt->execute([
                ':booking_id' => $bookingId,
                ':old_status' => $oldStatus,
                ':new_status' => $newStatus,
                ':changed_by' => $userId,
                ':note'       => $note
            ]);

            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}