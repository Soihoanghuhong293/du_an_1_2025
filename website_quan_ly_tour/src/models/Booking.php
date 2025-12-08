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

    // Tạo booking mới
// Trong file Booking.php

public static function create($data)
{
    $db = getDB();
    $sql = "INSERT INTO bookings (
                tour_id, created_by, assigned_guide_id, status, 
                start_date, end_date, notes, 
                schedule_detail, service_detail, diary, lists_file,
                number_of_adults, number_of_children, total_price,
                created_at
            )
            VALUES (
                :tour_id, :created_by, :assigned_guide_id, :status, 
                :start_date, :end_date, :notes, 
                :schedule_detail, :service_detail, :diary, :lists_file,
                :number_of_adults, :number_of_children, :total_price,
                NOW()
            )";

    $stmt = $db->prepare($sql);

    $params = [
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
        ':lists_file'        => $data['lists_file'] ?? null,
        ':number_of_adults'   => $data['number_of_adults'],
        ':number_of_children' => $data['number_of_children'],
        ':total_price'        => $data['total_price']
    ];

    if ($stmt->execute($params)) {
        // --- THAY ĐỔI QUAN TRỌNG: TRẢ VỀ ID VỪA TẠO ---
        return $db->lastInsertId(); 
    }
    
    return false;
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

    // Xóa booking
    public static function delete($id)
    {
        $db = getDB();
        try {
            $db->beginTransaction();

            $db->prepare("DELETE FROM booking_status_logs WHERE booking_id = :id")
               ->execute([':id' => $id]);

            $db->prepare("DELETE FROM bookings WHERE id = :id")
               ->execute([':id' => $id]);

            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    // Chi tiết Booking
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
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lịch sử trạng thái
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
        $stmt->execute([':id' => $booking_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // HDV xem booking được giao
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

    // Lấy diary
    public static function getDiary($bookingId)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT diary FROM bookings WHERE id = ?");
        $stmt->execute([$bookingId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || empty($row['diary'])) return ['entries' => []];

        $data = json_decode($row['diary'], true);
        return is_array($data) && isset($data['entries']) ? $data : ['entries' => []];
    }

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

    // Cập nhật trạng thái
    public static function updateStatus($bookingId, $newStatus, $userId, $note = null)
    {
        $db = getDB();

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT status FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $oldStatus = $stmt->fetchColumn();

            if ($oldStatus === false) {
                $db->rollBack();
                return false;
            }

            $db->prepare("UPDATE bookings SET status = :status WHERE id = :id")
               ->execute([':status' => $newStatus, ':id' => $bookingId]);

            $db->prepare("
                INSERT INTO booking_status_logs (booking_id, old_status, new_status, changed_by, note)
                VALUES (:booking_id, :old_status, :new_status, :changed_by, :note)
            ")->execute([
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

    // Tour
    public static function getTourById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM tours WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update diary
    public static function updateDiaryData($id, $content)
    {
        $db = getDB();
        $sql = "UPDATE bookings SET diary = :diary, updated_at = NOW() WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':diary' => $content,
            ':id'    => $id
        ]);
    }

    public static function updateScheduleData($id, $content)
    {
        $db = getDB();
        $sql = "UPDATE bookings SET schedule_detail = :content, updated_at = NOW() WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':content' => $content, ':id' => $id]);
    }

    public static function updateServiceData($id, $content)
    {
        $db = getDB();
        $sql = "UPDATE bookings SET service_detail = :content, updated_at = NOW() WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':content' => $content, ':id' => $id]);
    }
    // Trong file src/models/Booking.php

public static function getAvailableGuides($startDate, $endDate)
{
    $db = getDB();

    // 1. Logic tìm HDV BẬN:
    // - Có assigned_guide_id trong bảng bookings
    // - Trạng thái KHÔNG PHẢI là "Hủy" (status != 4 theo bảng tour_statuses của bạn)
    // - Thời gian bị trùng lặp
    
    $sql = "SELECT id, name FROM users 
            WHERE role = 'guide' 
            AND status = 1 -- Chỉ lấy user đang hoạt động (nếu bảng users có cột status)
            AND id NOT IN (
                SELECT assigned_guide_id 
                FROM bookings 
                WHERE assigned_guide_id IS NOT NULL 
                AND status IN (1, 2) 
                AND (start_date <= :end_date AND end_date >= :start_date)
            )";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':start_date' => $startDate,
        ':end_date'   => $endDate
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
