<?php

require_once __DIR__ . '/../helpers/database.php'; // file chứa hàm getDB()

class Category
{
    public static function all()
    {
        $db = getDB(); // lấy PDO từ helper
        $stmt = $db->prepare("SELECT * FROM categories ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
