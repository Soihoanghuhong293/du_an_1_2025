<?php

class GuideProfile
{
    public $id;
    public $user_id;
    public $birthdate;
    public $avatar;
    public $phone;
    public $certificate;
    public $languages;
    public $experience;
    public $history;
    public $rating;
    public $health_status;
    public $group_type;
    public $specialty;
    public $created_at;
    public $updated_at;

    public function __construct($data = [])
    {
        if (is_array($data)) {
            $this->id = $data['id'] ?? null;
            $this->user_id = $data['user_id'] ?? null;
            $this->birthdate = $data['birthdate'] ?? '';
            $this->avatar = $data['avatar'] ?? '';
            $this->phone = $data['phone'] ?? '';
            $this->certificate = $data['certificate'] ?? '';
            $this->languages = $data['languages'] ?? '';
            $this->experience = $data['experience'] ?? '';
            $this->history = $data['history'] ?? '';
            $this->rating = $data['rating'] ?? 0;
            $this->health_status = $data['health_status'] ?? '';
            $this->group_type = $data['group_type'] ?? '';
            $this->specialty = $data['specialty'] ?? '';
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
        }
    }

    public static function findByUserId($user_id)
    {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM guide_profiles WHERE user_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $user_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new self($data) : null;
    }

    public static function createOrUpdate($user_id, $data)
    {
        $pdo = getDB();
        // Kiểm tra xem hồ sơ đã tồn tại hay chưa
        $existing = self::findByUserId($user_id);
            // Normalize: only write columns that exist in the table to avoid "unknown column" errors
            $existing = self::findByUserId($user_id);
            if ($existing) {
                return self::update($existing['id'], array_merge(['user_id' => $user_id], $data));
            }

            // Ensure user_id is present
            $data['user_id'] = $user_id;
            return self::create($data);
    }

    // Lấy tất cả hướng dẫn viên
    public static function all()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM guide_profiles");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm hướng dẫn viên theo id
    public static function find($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM guide_profiles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm mới hướng dẫn viên (chuẩn bảng guide_profiles)
    public static function create($data)
    {
        $db = getDB();

        // Get table columns and filter incoming data to only existing columns
        $cols = self::getTableColumns();
        $filtered = [];
        foreach ($data as $k => $v) {
            if (in_array($k, $cols)) $filtered[$k] = $v;
        }

        // If table has created_at/updated_at and they are not provided, set them
        $now = date('Y-m-d H:i:s');
        if (in_array('created_at', $cols) && !isset($filtered['created_at'])) $filtered['created_at'] = $now;
        if (in_array('updated_at', $cols) && !isset($filtered['updated_at'])) $filtered['updated_at'] = $now;

        if (empty($filtered)) return false;

        $placeholders = implode(', ', array_fill(0, count($filtered), '?'));
        $columns = implode(', ', array_keys($filtered));
        $stmt = $db->prepare("INSERT INTO guide_profiles ($columns) VALUES ($placeholders)");
        return $stmt->execute(array_values($filtered));
    }

    // Cập nhật hướng dẫn viên (chuẩn bảng guide_profiles)
    public static function update($id, $data)
    {
        $db = getDB();

        $cols = self::getTableColumns();
        $filtered = [];
        foreach ($data as $k => $v) {
            if (in_array($k, $cols) && $k !== 'id') $filtered[$k] = $v;
        }

        // set updated_at if present in table
        if (in_array('updated_at', $cols)) $filtered['updated_at'] = date('Y-m-d H:i:s');

        if (empty($filtered)) return false;

        $setParts = [];
        foreach (array_keys($filtered) as $c) {
            $setParts[] = "$c = ?";
        }
        $sql = "UPDATE guide_profiles SET " . implode(', ', $setParts) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $params = array_values($filtered);
        $params[] = $id;
        return $stmt->execute($params);
    }

    // Xóa hướng dẫn viên
    public static function delete($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM guide_profiles WHERE id=?");
        $stmt->execute([$id]);
    }

    // Lấy danh sách cột của bảng (cache) để tránh lỗi khi DB thay đổi
    private static function getTableColumns()
    {
        static $cols = null;
        if ($cols !== null) return $cols;

        $db = getDB();
        $stmt = $db->prepare("DESCRIBE guide_profiles");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cols = array_column($rows, 'Field');
        return $cols;
    }
}
?>
