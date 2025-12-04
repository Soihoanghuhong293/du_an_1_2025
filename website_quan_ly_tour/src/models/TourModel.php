<?php

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
        $tour['schedule']   = $this->safeJson($tour['schedule'] ?? null);
        $tour['images']     = $this->safeJson($tour['images'] ?? null);
        $tour['prices']     = $this->safeJson($tour['prices'] ?? null);
        $tour['policies']   = $this->safeJson($tour['policies'] ?? null);
        $tour['suppliers']  = $this->safeJson($tour['suppliers'] ?? null);
        return $tour;
    }

    public function getAll() {
        $pdo = getDB();
        if (!$pdo) return [];
        try {
            $sql = "SELECT * FROM tours ORDER BY id DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            foreach ($rows as &$tour) {
                $tour = $this->mapJsonFields($tour);
            }
            return $rows;
        } catch (PDOException $e) {
            error_log("Lỗi getAll: " . $e->getMessage());
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
            error_log("Lỗi getById: " . $e->getMessage());
            return null;
        }
    }

    public function create($data) {
        $pdo = getDB();
        try {
            $sql = "INSERT INTO tours 
                (name, description, category_id, price, schedule, images, prices, policies, suppliers, status, created_at, updated_at)
                VALUES 
                (:name, :description, :category_id, :price, :schedule, :images, :prices, :policies, :suppliers, 1, NOW(), NOW())";

            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':name'         => $data['ten_tour'],
                ':description'  => $data['mo_ta'],
                ':category_id'  => $data['category_id'],
                ':price'        => $data['gia'],
                ':schedule'     => json_encode([]),
                ':images'       => json_encode([]),
                ':prices'       => json_encode([]),
                ':policies'     => json_encode([]),
                ':suppliers'    => json_encode([])
            ]);
        } catch (PDOException $e) {
            error_log("Lỗi create(): " . $e->getMessage());
            return false;
        }
    }

    // ĐÃ FIX: Bổ sung cập nhật category_id
    public function update($id, $data) {
        $pdo = getDB();
        try {
            $sql = "UPDATE tours SET
                        name = :name,
                        description = :description,
                        price = :price,
                        category_id = :category_id, 
                        updated_at = NOW()
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':id'          => $id,
                ':name'        => $data['ten_tour'],
                ':description' => $data['mo_ta'],
                ':price'       => $data['gia'],
                ':category_id' => $data['category_id'] // Thêm dòng này
            ]);
        } catch (PDOException $e) {
            error_log("Lỗi update(): " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        $pdo = getDB();
        try {
            $stmt = $pdo->prepare("DELETE FROM tours WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Lỗi delete(): " . $e->getMessage());
            return false;
        }
    }
}