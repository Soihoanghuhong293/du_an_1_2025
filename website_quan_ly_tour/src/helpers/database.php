<?php

// Hàm kết nối tới cơ sở dữ liệu MySQL
// Sử dụng cấu hình từ config/config.php
// Trả về đối tượng PDO nếu kết nối thành công, null nếu thất bại
function getDB()
{
    static $pdo = null;
    static $dbConfig = null;

    // Nếu đã kết nối rồi thì trả về kết nối cũ (singleton pattern)
    if ($pdo !== null) {
        return $pdo;
    }

    // Lấy cấu hình database (chỉ load một lần)
    if ($dbConfig === null) {
        $config = require BASE_PATH . '/config/config.php';
        $dbConfig = $config['db'];
    }

    try {
        // Tạo chuỗi DSN (Data Source Name) cho PDO
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $dbConfig['host'],
            $dbConfig['name'],
            $dbConfig['charset']
        );

        // Tạo kết nối PDO với các tùy chọn
        $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Báo lỗi khi có exception
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Mặc định trả về mảng kết hợp
            PDO::ATTR_EMULATE_PREPARES => false, // Sử dụng prepared statements thật
        ]);

        return $pdo;
    } catch (PDOException $e) {
        // Ghi log lỗi (trong môi trường production nên log vào file)
        error_log('Database connection failed: ' . $e->getMessage());
        return null;
    }
}
class DB {
    private $pdo;

    public function __construct() {
        // Lấy đối tượng PDO từ hàm helper getDB()
        $this->pdo = getDB();
        if (!$this->pdo) {
            die("Không thể kết nối đến cơ sở dữ liệu.");
        }
    }

    /**
     * Thực thi truy vấn SELECT và trả về PDOStatement đã thực thi.
     * Dùng cho Tour.php: $this->db->query($sql)->fetchAll();
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt; // Trả về PDOStatement
        } catch (PDOException $e) {
            die("Lỗi truy vấn: " . $e->getMessage() . " | SQL: " . $sql);
        }
    }

    /**
     * Thực thi truy vấn INSERT/UPDATE/DELETE.
     * Dùng cho Tour.php: $this->db->execute($sql, $data);
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params); // Trả về true/false
        } catch (PDOException $e) {
            die("Lỗi thực thi: " . $e->getMessage() . " | SQL: " . $sql);
        }
    }
}

