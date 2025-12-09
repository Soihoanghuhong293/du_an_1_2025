<?php

require_once __DIR__ . '/User.php'; // Kiểm tra lại đường dẫn này có đúng file User model không
// require_once __DIR__ . '/../helpers/database.php'; // Đảm bảo file này tồn tại và hàm getDB() hoạt động

class TourModel {

    private static $lastError = null;

    public function __construct() {}

    /**
     * FIX: Nếu decode thất bại (do là text thường), trả về nguyên gốc chuỗi đó
     */
    private function safeJson($value) {
        if (empty($value)) return []; 
        if (is_array($value)) return $value;
        
        $arr = json_decode($value, true);
        
        // Nếu là JSON hợp lệ và là mảng
        if (json_last_error() === JSON_ERROR_NONE && is_array($arr)) {
            return $arr;
        }
        
        // Nếu lỗi decode (tức là dữ liệu dạng text bình thường), trả về text
        return $value;
    }

    /**
     * Map dữ liệu thô từ DB sang định dạng chuẩn cho View
     */
    private function mapJsonFields($tour) {
        if (!$tour) return null;

        // 1. Giải mã các trường JSON (Sử dụng hàm safeJson đã sửa)
        $tour['schedule']   = $this->safeJson($tour['schedule'] ?? null);
        $tour['images']     = $this->safeJson($tour['images'] ?? null);
        $tour['prices']     = $this->safeJson($tour['prices'] ?? null);
        $tour['policies']   = $this->safeJson($tour['policies'] ?? null);
        $tour['suppliers']  = $this->safeJson($tour['suppliers'] ?? null);

        // 2. Xử lý Logic Nhà cung cấp
        $supplier_ids = [];
        $supplier_names = [];
        
        // Kiểm tra kỹ xem biến suppliers có phải mảng không trước khi foreach
        if (is_array($tour['suppliers'])) {
            foreach ($tour['suppliers'] as $s) {
                if ($s === null || $s === '') continue;
                if (is_numeric($s)) {
                    $supplier_ids[] = (int)$s;
                } else {
                    $supplier_names[] = (string)$s;
                }
            }
        }

        // Tìm tên User nếu có ID
        $mappedNames = [];
        if (!empty($supplier_ids) && class_exists('User')) {
            foreach ($supplier_ids as $supId) {
                $u = User::findById($supId); // Đảm bảo User Model có method này
                if ($u) {
                    $mappedNames[] = is_object($u) ? ($u->name ?? '') : ($u['name'] ?? '');
                }
            }
        }

        $finalSupplierNames = array_values(array_filter(array_merge($mappedNames, $supplier_names)));
        $tour['supplier_ids'] = $supplier_ids;
        $tour['suppliers']    = $finalSupplierNames;

        // 3. Tương thích ngược (Backward Compatibility)
        $tour['lich_trinh']    = $tour['schedule'];
        $tour['hinh_anh']      = $tour['images'];
        $tour['gia_chi_tiet']  = $tour['prices'];
        $tour['chinh_sach']    = $tour['policies'];
        $tour['nha_cung_cap']  = $tour['suppliers'];
        
        $tour['ten_tour']      = $tour['name'] ?? '';
        $tour['mo_ta']         = $tour['description'] ?? '';
        $tour['gia']           = $tour['price'] ?? 0;

        return $tour;
    }

    // =========================================================================
    // CRUD OPERATIONS
    // =========================================================================

    public function getAll() {
        $pdo = getDB();
        try {
            $stmt = $pdo->prepare("SELECT * FROM tours ORDER BY id DESC");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as &$tour) {
                $tour = $this->mapJsonFields($tour);
            }
            return $rows;
        } catch (PDOException $e) {
            self::$lastError = $e->getMessage();
            return [];
        }
    }

    public function getById($id) {
        $pdo = getDB();
        try {
            $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $tour = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->mapJsonFields($tour);
        } catch (PDOException $e) {
            self::$lastError = $e->getMessage();
            return null;
        }
    }

    public static function find($id) {
        $instance = new self();
        return $instance->getById($id);
    }

    public function create($data) {
        $pdo = getDB();
        try {
            $sql = "INSERT INTO tours 
                (name, description, category_id, price, duration_days, status, schedule, policies, images, prices, suppliers, created_at, updated_at)
                VALUES 
                (:name, :description, :category_id, :price, :duration_days, :status, :schedule, :policies, :images, :prices, :suppliers, NOW(), NOW())";

            $stmt = $pdo->prepare($sql);

            // Logic xử lý JSON khi lưu (Giữ nguyên logic của bạn vì nó chuẩn hóa dữ liệu đầu vào)
            $scheduleData = $data['schedule'] ?? ($data['lich_trinh'] ?? []);
            if (!is_array($scheduleData)) {
                 // Nếu người dùng nhập text, tự động bọc vào JSON structure chuẩn
                 // LƯU Ý: Nếu bạn muốn lưu raw text thì bỏ đoạn if này đi và gán thẳng. 
                 // Nhưng giữ lại như này tốt hơn cho App sau này.
                 $scheduleJson = json_encode([
                    'days' => [
                        [ 'date' => '', 'activities' => [$scheduleData] ]
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                $scheduleJson = json_encode($scheduleData, JSON_UNESCAPED_UNICODE);
            }

            $policyData = $data['policies'] ?? ($data['chinh_sach'] ?? '');
            if (!is_array($policyData)) {
                $policiesJson = json_encode(['booking' => $policyData], JSON_UNESCAPED_UNICODE);
            } else {
                $policiesJson = json_encode($policyData, JSON_UNESCAPED_UNICODE);
            }

            return $stmt->execute([
                ':name'          => $data['name'] ?? $data['ten_tour'] ?? '',
                ':description'   => $data['description'] ?? $data['mo_ta'] ?? '',
                ':category_id'   => $data['category_id'] ?? null,
                ':price'         => $data['price'] ?? $data['gia'] ?? 0,
                ':duration_days' => $data['duration_days'] ?? 1,
                ':status'        => $data['status'] ?? 1,
                ':schedule'      => $scheduleJson,
                ':policies'      => $policiesJson,
                ':images'        => is_string($data['images']) ? $data['images'] : json_encode($data['images'] ?? [], JSON_UNESCAPED_UNICODE),
                ':prices'        => is_string($data['prices']) ? $data['prices'] : json_encode($data['prices'] ?? [], JSON_UNESCAPED_UNICODE),
                ':suppliers'     => is_string($data['suppliers']) ? $data['suppliers'] : json_encode($data['suppliers'] ?? [], JSON_UNESCAPED_UNICODE)
            ]);
        } catch (PDOException $e) {
            self::$lastError = $e->getMessage();
            return false;
        }
    }

    public function update($id, $data) {
        $pdo = getDB();
        try {
            $sql = "UPDATE tours SET 
                name = :name,
                description = :description,
                category_id = :category_id,
                price = :price,
                duration_days = :duration_days,
                status = :status,
                schedule = :schedule,
                policies = :policies,
                prices = :prices,
                suppliers = :suppliers,
                images = :images,
                updated_at = NOW()
            WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            // Xử lý Lịch trình
            $scheduleData = $data['schedule'] ?? ($data['lich_trinh'] ?? []);
            if (!is_array($scheduleData)) {
                 $scheduleJson = json_encode([
                    'days' => [[ 'date' => '', 'activities' => [$scheduleData] ]]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                $scheduleJson = json_encode($scheduleData, JSON_UNESCAPED_UNICODE);
            }

            // Xử lý Chính sách
            $policyData = $data['policies'] ?? ($data['chinh_sach'] ?? '');
            if (!is_array($policyData)) {
                $policiesJson = json_encode(['booking' => $policyData], JSON_UNESCAPED_UNICODE);
            } else {
                $policiesJson = json_encode($policyData, JSON_UNESCAPED_UNICODE);
            }

            return $stmt->execute([
                ':id'            => $id,
                ':name'          => $data['name'] ?? $data['ten_tour'] ?? '',
                ':description'   => $data['description'] ?? $data['mo_ta'] ?? '',
                ':category_id'   => $data['category_id'] ?? null,
                ':price'         => $data['price'] ?? $data['gia'] ?? 0,
                ':duration_days' => $data['duration_days'] ?? 1,
                ':status'        => $data['status'] ?? 1,
                ':schedule'      => $scheduleJson,
                ':policies'      => $policiesJson,
                ':images'        => is_string($data['images']) ? $data['images'] : json_encode($data['images'] ?? [], JSON_UNESCAPED_UNICODE),
                ':prices'        => is_string($data['prices']) ? $data['prices'] : json_encode($data['prices'] ?? [], JSON_UNESCAPED_UNICODE),
                ':suppliers'     => is_string($data['suppliers']) ? $data['suppliers'] : json_encode($data['suppliers'] ?? [], JSON_UNESCAPED_UNICODE)
            ]);
        } catch (PDOException $e) {
            self::$lastError = $e->getMessage();
            return false;
        }
    }

    public function delete($id) {
        $pdo = getDB();
        try {
            $stmt = $pdo->prepare("DELETE FROM tours WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            self::$lastError = $e->getMessage();
            return false;
        }
    }

    public function getLastInsertId() {
        $pdo = getDB();
        return $pdo ? $pdo->lastInsertId() : null;
    }

    public function getLastError() {
        return self::$lastError;
    }
}