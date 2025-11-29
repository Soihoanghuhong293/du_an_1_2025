<?php
class User
{
    public $id;
    public $name;
    public $email;
    public $role;
    public $status;

    public function __construct($data = [])
    {
        if (is_array($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->role = $data['role'] ?? 'user'; // admin | guide | user
            $this->status = $data['status'] ?? 1;
        }
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuide(): bool
    {
        return $this->role === 'guide';
    }

    public static function findByEmail($email)
    {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new self($data) : null;
    }

    public static function findById($id)
    {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new self($data) : null;
    }

    public static function all()
    {
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($rows as $row) {
            $users[] = new self($row);
        }
        return $users;
    }

    public static function create($data)
    {
        $pdo = getDB();
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role, status)
            VALUES (:name, :email, :password, :role, :status)
        ");
        return $stmt->execute([
            'name' => $data['name'], // sửa từ fullname -> name
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => $data['role'] ?? 'user',
            'status' => $data['status'] ?? 1,
        ]);
    }

    public function update($data)
    {
        $pdo = getDB();
        $sql = "UPDATE users SET name=:name, email=:email, role=:role, status=:status";
        $params = [
            'name' => $data['name'], 
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status'],
            'id' => $this->id
        ];

        if (!empty($data['password'])) {
            $sql .= ", password=:password";
            $params['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete()
    {
        $pdo = getDB();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=:id");
        return $stmt->execute(['id' => $this->id]);
    }
}
