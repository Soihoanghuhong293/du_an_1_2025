<?php

class Tour {

    public function __construct() {}

    // Hàm decode JSON an toàn
    private function safeJson($value) {
        if (empty($value)) return [];
        if (!is_string($value)) return [];
        $arr = json_decode($value, true);
        return is_array($arr) ? $arr : [];
    }

    // Áp dụng safeJson cho từng cột JSON
    private function mapJsonFields($tour) {
        if (!$tour) return null;

        $tour['lich_trinh']   = $this->safeJson($tour['lich_trinh'] ?? null);
        $tour['hinh_anh']     = $this->safeJson($tour['hinh_anh'] ?? null);
        $tour['chinh_sach']   = $this->safeJson($tour['chinh_sach'] ?? null);
        $tour['nha_cung_cap'] = $this->safeJson($tour['nha_cung_cap'] ?? null);

        return $tour;
    }

    // Lấy tất cả tour
    public function getAll() {
        $pdo = getDB();
        if (!$pdo) return [];

        try {
            $sql = "SELECT * FROM tours ORDER BY id DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $rows = $stmt->fetchAll();
            
            // Decode JSON cho từng bản ghi
            foreach ($rows as &$tour) {
                $tour = $this->mapJsonFields($tour);
            }

            return $rows;

        } catch (PDOException $e) {
            error_log("Lỗi getAll: " . $e->getMessage());
            return [];
        }
    }

    // Lấy tour theo ID
    public function getById($id) {
        $pdo = getDB();
        if (!$pdo) return null;

        try {
            $sql = "SELECT * FROM tours WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $tour = $stmt->fetch();
            return $this->mapJsonFields($tour);

        } catch (PDOException $e) {
            error_log("Lỗi getById: " . $e->getMessage());
            return null;
        }
    }

    // Tạo tour mới
    public function create($data)
{
    $pdo = getDB();
    if (!$pdo) return false;

    try {
        $sql = "INSERT INTO tours 
            (name, description, category_id, price, lich_trinh, hinh_anh, chinh_sach, nha_cung_cap, status, created_at, updated_at)
            VALUES 
            (:name, :description, :category_id, :price, :lich_trinh, :hinh_anh, :chinh_sach, :nha_cung_cap, 1, NOW(), NOW())";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            ':name'         => $data['ten_tour'],
            ':description'  => $data['mo_ta'],
            ':category_id'  => $data['category_id'],
            ':price'        => $data['gia'],

            // JSON mặc định nếu không nhập
            ':lich_trinh'   => json_encode(['days' => []]),
            ':hinh_anh'     => json_encode([]),
            ':chinh_sach'   => json_encode(['booking' => '']),
            ':nha_cung_cap' => json_encode([]),
        ]);

    } catch (PDOException $e) {
        error_log("Lỗi create(): " . $e->getMessage());
        return false;
    }
}


    // Update tour
    public function update($id, $data) {
        $pdo = getDB();
        if (!$pdo) return false;

        try {
            $sql = "UPDATE tours SET
                        ten_tour = :ten_tour,
                        mo_ta = :mo_ta,
                        gia = :gia,
                        ngay_khoi_hanh = :ngay_khoi_hanh,
                        diem_den = :diem_den
                    WHERE id = :id";

            $data['id'] = $id;

            $stmt = $pdo->prepare($sql);
            return $stmt->execute($data);

        } catch (PDOException $e) {
            error_log("Lỗi update(): " . $e->getMessage());
            return false;
        }
    }

    // Xóa tour
    public function delete($id) {
        $pdo = getDB();
        if (!$pdo) return false;

        try {
            $sql = "DELETE FROM tours WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Lỗi delete(): " . $e->getMessage());
            return false;
        }
    }
}
