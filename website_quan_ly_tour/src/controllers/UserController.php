<?php
require_once __DIR__ . '/../models/User.php';

class UserController
{
    // Hiển thị danh sách người dùng
    public function index()
    {
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM users");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Chuyển mảng kết quả thành object User
        $users = array_map(fn($row) => new User($row), $rows);
    
        view('User.index', [
            'title' => 'Danh sách người dùng',
            'users' => $users
        ]);
    }
    
    // Hiển thị form tạo mới
    public function create()
    {
        view('User.create', ['title' => 'Thêm người dùng mới']);
    }

    // Xử lý form tạo mới
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
                'role' => $_POST['role'] ?? 'huong_dan_vien',
                'status' => 1,
            ];
            User::create($data);
            header('Location: ' . BASE_URL . 'users');
            exit;
        }
    }

    // Hiển thị form edit
    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . 'users');
            exit;
        }
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id=:id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $user = new User($row);
    
        view('User.edit', [
            'title' => 'Chỉnh sửa người dùng',
            'user' => $user
        ]);
    }
        // Xử lý update
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $pdo = getDB();
            $stmt = $pdo->prepare("UPDATE users SET name=:name, email=:email, role=:role, status=:status WHERE id=:id");
            $stmt->execute([
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'role' => $_POST['role'],
                'status' => $_POST['status'],
                'id' => $id
            ]);
            header('Location: ' . BASE_URL . 'users');
            exit;
        }
    }
    

    // Xóa user
    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $pdo = getDB();
            $stmt = $pdo->prepare("DELETE FROM users WHERE id=:id");
            $stmt->execute(['id' => $id]);
        }
        header('Location: ' . BASE_URL . 'users');
        exit;
    }

    // Xem chi tiết user
    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . 'users');
            exit;
        }
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id=:id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $user = new User($row);

        view('User.detail', [
            'title' => 'Chi tiết người dùng',
            'user' => $user
        ]);
    }
    public function toggleStatus()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('users');
    
        $user = User::find($id);
        if (!$user) die("User không tồn tại!");
    
        // Sử dụng -> thay vì []
        $newStatus = $user->status == 1 ? 0 : 1;
    
        User::updateStatus($id, $newStatus);
    
        redirect('users');
    }
        
}
