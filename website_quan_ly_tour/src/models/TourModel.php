<?php

class TourModel {

    public function __construct() {}

    // Decode JSON an toàn
    private function safeJson($value) {
        if (!$value) return [];
        if (is_array($value)) return $value; // tránh decode nhầm
        $json = json_decode($value, true);
        return is_array($json) ? $json : [];
    }

    // Chuẩn hóa dữ liệu JSON khi lấy từ DB
    private function mapJsonFields($tour) {
        if (!$tour) return null;

        $tour['schedule']  = $this->safeJson($tour['schedule']);
        $tour['images']    = $this->safeJson($tour['images']);
        $tour['prices']    = $this->safeJson($tour['prices']);
        $tour['policies']  = $this->safeJson($tour['policies']);
        $tour['suppliers'] = $this->safeJson($tour['suppliers']);

        return $tour;
    }

    // =============================
    // LẤY TOÀN BỘ TOUR
    // =============================
    public function getAll() {
        $pdo = getDB();

        try {
            $rows = $pdo->query("SELECT * FROM tours ORDER BY id DESC")->fetchAll();

            foreach ($rows as &$t) {
                $t = $this->mapJsonFields($t);
            }
            return $rows;

        } catch (PDOException $e) {
            error_log("Lỗi getAll(): " . $e->getMessage());
            return [];
        }
    }

    // =============================
    // LẤY THEO ID
    // =============================
    public function getById($id) {
        $pdo = getDB();

        try {
            $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = :id");
            $stmt->execute(['id' => $id]);

            $tour = $stmt->fetch();
            return $this->mapJsonFields($tour);

        } catch (PDOException $e) {
            error_log("Lỗi getById(): " . $e->getMessage());
            return null;
        }
    }

    // =============================
    // TẠO TOUR
    // =============================
    public function create($data) {
        $pdo = getDB();

        try {
            $sql = "INSERT INTO tours 
                (name, description, category_id, price, schedule, images, prices, policies, suppliers, status, created_at, updated_at)
                VALUES 
                (:name, :description, :category_id, :price, :schedule, :images, :prices, :policies, :suppliers, :status, NOW(), NOW())";

            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                ':name'        => $data['name'],
                ':description' => $data['description'],
                ':category_id' => $data['category_id'],
                ':price'       => $data['price'],
                ':schedule'    => json_encode($data['schedule'], JSON_UNESCAPED_UNICODE),
                ':images'      => json_encode($data['images'], JSON_UNESCAPED_UNICODE),
                ':prices'      => json_encode($data['prices'], JSON_UNESCAPED_UNICODE),
                ':policies'    => json_encode($data['policies'], JSON_UNESCAPED_UNICODE),
                ':suppliers'   => json_encode($data['suppliers'], JSON_UNESCAPED_UNICODE),
                ':status'      => $data['status'] ?? 1
            ]);

        } catch (PDOException $e) {
            error_log("Lỗi create(): " . $e->getMessage());
            return false;
        }
    }

    // =============================
    // UPDATE TOUR
    // =============================
    public function update($id, $data) {
        $pdo = getDB();

        try {
            $sql = "UPDATE tours SET
                    name        = :name,
                    description = :description,
                    category_id = :category_id,
                    price       = :price,
                    schedule    = :schedule,
                    images      = :images,
                    prices      = :prices,
                    policies    = :policies,
                    suppliers   = :suppliers,
                    status      = :status,
                    updated_at  = NOW()
                WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                ':id'          => $id,
                ':name'        => $data['name'],
                ':description' => $data['description'],
                ':category_id' => $data['category_id'],
                ':price'       => $data['price'],
                ':schedule'    => json_encode($data['schedule'], JSON_UNESCAPED_UNICODE),
                ':images'      => json_encode($data['images'], JSON_UNESCAPED_UNICODE),
                ':prices'      => json_encode($data['prices'], JSON_UNESCAPED_UNICODE),
                ':policies'    => json_encode($data['policies'], JSON_UNESCAPED_UNICODE),
                ':suppliers'   => json_encode($data['suppliers'], JSON_UNESCAPED_UNICODE),
                ':status'      => $data['status']
            ]);

        } catch (PDOException $e) {
            error_log("Lỗi update(): " . $e->getMessage());
            return false;
        }
    }

    // =============================
    // XÓA TOUR
    // =============================
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
    // Lấy tour có lọc theo tên và category
public function getFiltered($search = '', $categoryId = null) {
    $pdo = getDB();
    $where = [];
    $params = [];

    if ($search) {
        $where[] = "name LIKE :search";
        $params[':search'] = "%$search%";
    }

    if ($categoryId) {
        $where[] = "category_id = :category_id";
        $params[':category_id'] = $categoryId;
    }

    $sql = "SELECT * FROM tours";
    if ($where) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY id DESC";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        foreach ($rows as &$t) {
            $t = $this->mapJsonFields($t);
        }
        return $rows;

    } catch (PDOException $e) {
        error_log("Lỗi getFiltered(): " . $e->getMessage());
        return [];
    }
}

}

