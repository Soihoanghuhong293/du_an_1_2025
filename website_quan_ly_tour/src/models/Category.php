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
    public static function deleteById($id)
{
    $db = getDB();

    $sql = "DELETE FROM categories WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    return $stmt->execute();
}
   
public static function create($name, $description, $status)
{
    $db = getDB();
    $stmt = $db->prepare("
    INSERT INTO categories(name, description, status)
    VALUES (:name, :description, :status) ");
    return $stmt -> execute([
        ':name'=>$name,
        ':description'=> $description,
        ':status'=> $status
    ]);
     
    
}
  public static function find($id)
  {
    $db = getDB();
    $stmt = $db-> prepare("SELECT * FROM categories WHERE id = :id");
    $stmt-> execute([':id' => $id]);
    return $stmt -> fetch(PDO::FETCH_ASSOC);
  }
  public static function updateById($id, $name, $description, $status)
{
    $db = getDB();
    $stmt = $db->prepare("
        UPDATE categories 
        SET name = :name, description = :description, status = :status 
        WHERE id = :id
    ");
    return $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':status' => $status,
        ':id' => $id
    ]);
}

}
