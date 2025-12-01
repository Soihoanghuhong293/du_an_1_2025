<?php
// File: src/models/Tour.php

// Hàm getDB() phải được định nghĩa trong src/helpers/database.php
// và đã được require_once trong index.php

class Tour {
    
    // Bạn không cần private $db nữa vì bạn gọi getDB() trực tiếp

    public function __construct() {
        // Constructor rỗng (hoặc nếu cần, gọi getDB() lần đầu để kiểm tra kết nối)
    }

    // =============================================================
    // ⭐ 1. Hiển thị: Lấy tất cả tour (fetchAll)
    // =============================================================
    public function getAll() {
        $pdo = getDB(); // Lấy đối tượng PDO
        if (!$pdo) return []; // Trả về mảng rỗng nếu kết nối thất bại
        
        try {
            $sql = "SELECT * FROM tours ORDER BY id DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            // Trả về tất cả các dòng dữ liệu
            return $stmt->fetchAll(); 
        } catch (PDOException $e) {
            // Xử lý lỗi (Nên log lỗi thay vì die)
            error_log("Lỗi truy vấn getAll: " . $e->getMessage());
            return [];
        }
    }

    // Lấy tour theo ID (dùng cho chức năng Sửa - fetch)
    public function getById($id) {
        $pdo = getDB(); // Lấy đối tượng PDO
        if (!$pdo) return null;
        
        try {
            $sql = "SELECT * FROM tours WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            // Trả về một dòng dữ liệu
            return $stmt->fetch(); 
        } catch (PDOException $e) {
            error_log("Lỗi truy vấn getById: " . $e->getMessage());
            return null;
        }
    }

    // =============================================================
    // ⭐ 2. Thêm: Thêm tour mới (execute)
    // =============================================================
    public function create($data) {
        $pdo = getDB();
        if (!$pdo) return false;
        
        try {
            $sql = "INSERT INTO tours (ten_tour, mo_ta, gia, ngay_khoi_hanh, diem_den) 
                    VALUES (:ten_tour, :mo_ta, :gia, :ngay_khoi_hanh, :diem_den)";
            $stmt = $pdo->prepare($sql);
            
            // ⭐ LOG: Bổ sung ghi log trước khi execute
            error_log("Executing CREATE SQL: " . $sql . " with data: " . print_r($data, true));
            
            return $stmt->execute($data); 
        } catch (PDOException $e) {
            // Ghi lại lỗi PDO chi tiết
            error_log("❌ PDO ERROR in create(): " . $e->getMessage() . " | SQLSTATE: " . $e->getCode());
            return false;
        }
    }

    // =============================================================
    // ⭐ 3. Sửa: Cập nhật tour (UPDATE)
    public function update($id, $data) {
        $pdo = getDB();
        if (!$pdo) return false;
        
        try {
            $sql = "UPDATE tours SET 
                        ten_tour = :ten_tour, mo_ta = :mo_ta, 
                        gia = :gia, ngay_khoi_hanh = :ngay_khoi_hanh, diem_den = :diem_den 
                    WHERE id = :id";
            
            $data['id'] = $id; 

            $stmt = $pdo->prepare($sql);
            
            // ⭐ LOG: Bổ sung ghi log trước khi execute
            error_log("Executing UPDATE SQL: " . $sql . " with data: " . print_r($data, true));
            
            return $stmt->execute($data);
        } catch (PDOException $e) {
            // Ghi lại lỗi PDO chi tiết
            error_log("❌ PDO ERROR in update(): " . $e->getMessage() . " | SQLSTATE: " . $e->getCode());
            return false;
        }
    }

    // =============================================================
    // ⭐ 4. Xóa: Xóa tour (DELETE)
    public function delete($id) {
        $pdo = getDB();
        if (!$pdo) return false;
        
        try {
            $sql = "DELETE FROM tours WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            
            // ⭐ LOG: Bổ sung ghi log trước khi execute
            error_log("Executing DELETE SQL: " . $sql . " with ID: " . $id);
            
            return $stmt->execute(['id' => $id]); 
        } catch (PDOException $e) {
            // Ghi lại lỗi PDO chi tiết
            error_log("❌ PDO ERROR in delete(): " . $e->getMessage() . " | SQLSTATE: " . $e->getCode());
            return false;
        }
    }
}