<?php

require_once __DIR__ . '/User.php';

class Tour {

    public function __construct() {}

    private function safeJson($value) {
        if (empty($value)) return [];
        if (!is_string($value)) return [];
        $arr = json_decode($value, true);
        return is_array($arr) ? $arr : [];
    }

    private function mapJsonFields($tour) {
        if (!$tour) return null;
        // Decode stored JSON fields (if properly stored as JSON objects/arrays)
        $tour['schedule']   = $this->safeJson($tour['schedule'] ?? null);
        $tour['images']     = $this->safeJson($tour['images'] ?? null);
        $tour['prices']     = $this->safeJson($tour['prices'] ?? null);
        $tour['policies']   = $this->safeJson($tour['policies'] ?? null);
        $tour['suppliers']  = $this->safeJson($tour['suppliers'] ?? null);
        // Collect numeric supplier IDs and string supplier names separately
        $supplier_ids = [];
        $supplier_names = [];
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

        // Map numeric ids to names
        $mappedNames = [];
        foreach ($supplier_ids as $supId) {
            $u = User::findById($supId);
            if ($u) {
                $mappedNames[] = is_object($u) ? ($u->name ?? '') : ($u['name'] ?? '');
            }
        }

        // Final supplier names: mapped names (from ids) plus any free-form names
        $finalSupplierNames = array_values(array_filter(array_merge($mappedNames, $supplier_names)));

        $tour['supplier_ids'] = $supplier_ids;
        $tour['suppliers'] = $finalSupplierNames;
        // Backwards-compat: some views use Vietnamese keys (lich_trinh, hinh_anh, ...)
        // Populate those keys from decoded fields so views keep working.
        $tour['lich_trinh']    = $tour['schedule'];
        $tour['hinh_anh']      = $tour['images'];
        $tour['gia_chi_tiet']  = $tour['prices'];
        $tour['chinh_sach']    = $tour['policies'];
    $tour['nha_cung_cap']  = $tour['suppliers'];
    $tour['nha_cung_cap_ids'] = $tour['supplier_ids'];

        return $tour;
    }

    public function getAll() {
        $pdo = getDB();
        try {
            $stmt = $pdo->prepare("SELECT * FROM tours ORDER BY id DESC");
            $stmt->execute();
            $rows = $stmt->fetchAll();

            foreach ($rows as &$tour) {
                $tour = $this->mapJsonFields($tour);
            }

            return $rows;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        $pdo = getDB();
        try {
            $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $tour = $stmt->fetch();
            return $this->mapJsonFields($tour);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function create($data) {
        $pdo = getDB();
        try {
            $sql = "INSERT INTO tours 
                (name, description, category_id, price, schedule, policies, images, prices, suppliers, status, created_at, updated_at)
                VALUES 
                (:name, :description, :category_id, :price, :schedule, :policies, :images, :prices, :suppliers, 1, NOW(), NOW())";

            $stmt = $pdo->prepare($sql);
            // Normalize schedule and policies into structured JSON so views can consume them predictably
            $scheduleJson = json_encode([
                'days' => [
                    [
                        'date' => '',
                        'activities' => [$data['lich_trinh'] ?? '']
                    ]
                ]
            ]);

            $policiesJson = json_encode([
                'booking' => $data['chinh_sach'] ?? ''
            ]);

            return $stmt->execute([
                ':name'         => $data['ten_tour'],
                ':description'  => $data['mo_ta'],
                ':category_id'  => $data['category_id'],
                ':price'        => $data['gia'],
                ':schedule'     => $scheduleJson,
                ':policies'     => $policiesJson,
                ':images'       => json_encode($data['images'] ?? []),
                ':prices'       => json_encode($data['prices'] ?? []),
                ':suppliers'    => json_encode($data['suppliers'] ?? [])
            ]);
        } catch (PDOException $e) {
            // Log DB errors to help debugging
            error_log('[Tour::create] PDOException: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Trả về ID của bản ghi vừa được chèn gần nhất
     * Sử dụng PDO::lastInsertId()
     */
    public function getLastInsertId() {
        $pdo = getDB();
        if (!$pdo) return null;
        try {
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Lưu danh sách ảnh cho tour (mảng đường dẫn)
     * - Lấy images hiện tại (JSON), nối với $paths và cập nhật lại cột images
     * - Trả về true nếu cập nhật thành công, false nếu thất bại
     */
    public function saveImages($tourId, array $paths = []) {
        if (!$tourId) return false;
        if (empty($paths)) return false;

        $pdo = getDB();
        if (!$pdo) return false;

        try {
            // Lấy images hiện tại
            $stmt = $pdo->prepare("SELECT images FROM tours WHERE id = :id");
            $stmt->execute(['id' => $tourId]);
            $row = $stmt->fetch();

            $images = [];
            if ($row && !empty($row['images'])) {
                $decoded = json_decode($row['images'], true);
                if (is_array($decoded)) $images = $decoded;
            }

            // Nối ảnh mới
            $images = array_merge($images, array_values($paths));

            // Cập nhật lại trường images
            $update = $pdo->prepare("UPDATE tours SET images = :images, updated_at = NOW() WHERE id = :id");
            return (bool)$update->execute([
                'images' => json_encode($images),
                'id' => $tourId
            ]);
        } catch (PDOException $e) {
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
                schedule = :schedule,
                policies = :policies,
                prices = :prices,
                suppliers = :suppliers,
                updated_at = NOW()
            WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            // Normalize schedule/policies similar to create()
            $scheduleJson = json_encode([
                'days' => [
                    [
                        'date' => '',
                        'activities' => [$data['lich_trinh'] ?? '']
                    ]
                ]
            ]);

            $policiesJson = json_encode([
                'booking' => $data['chinh_sach'] ?? ''
            ]);

            return $stmt->execute([
                ':id'           => $id,
                ':name'         => $data['ten_tour'],
                ':description'  => $data['mo_ta'],
                ':category_id'  => $data['category_id'],
                ':price'        => $data['gia'],
                ':schedule'     => $scheduleJson,
                ':policies'     => $policiesJson,
                ':prices'       => json_encode($data['prices'] ?? []),
                ':suppliers'    => json_encode($data['suppliers'] ?? [])
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        $pdo = getDB();
        try {
            $stmt = $pdo->prepare("DELETE FROM tours WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
