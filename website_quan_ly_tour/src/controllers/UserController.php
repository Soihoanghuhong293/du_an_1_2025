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

    // Hiển thị hồ sơ người dùng hiện tại
    public function profile()
    {
        // Kiểm tra người dùng đã đăng nhập hay chưa
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $user = User::findById($_SESSION['user_id']);
        if (!$user) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        // Lấy thông tin hồ sơ (guide_profiles) nếu có
        $guideProfile = GuideProfile::findByUserId($user->id);

        view('User.profile', [
            'title' => 'Hồ sơ cá nhân',
            'user' => $user,
            'guideProfile' => $guideProfile
        ]);
    }

    // Cập nhật hồ sơ người dùng
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('profile');
        }

        $id = $_POST['id'] ?? null;
        if (!$id || $id != $_SESSION['user_id']) {
            redirect('profile');
        }

        $pdo = getDB();
        
        // Chuẩn bị dữ liệu cập nhật users
        $updateData = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'role' => $_POST['role'],
            'id' => $id
        ];

        // Nếu có mật khẩu mới
        if (!empty($_POST['password'])) {
            $updateData['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET name=:name, email=:email, role=:role, password=:password WHERE id=:id");
            $stmt->execute([
                'name' => $updateData['name'],
                'email' => $updateData['email'],
                'role' => $updateData['role'],
                'password' => $updateData['password'],
                'id' => $updateData['id']
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name=:name, email=:email, role=:role WHERE id=:id");
            $stmt->execute([
                'name' => $updateData['name'],
                'email' => $updateData['email'],
                'role' => $updateData['role'],
                'id' => $updateData['id']
            ]);
        }

        // Cập nhật thông tin guide profile nếu là hướng dẫn viên
        if ($_POST['role'] === 'guide') {
            $guideData = [
                'birthdate' => $_POST['birthdate'] ?? '',
                'avatar' => $_POST['avatar'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'certificate' => $_POST['certificate'] ?? '',
                'languages' => $_POST['languages'] ?? '',
                'experience' => $_POST['experience'] ?? '',
                'history' => $_POST['history'] ?? '',
                'health_status' => $_POST['health_status'] ?? '',
                'group_type' => $_POST['group_type'] ?? '',
                'specialty' => $_POST['specialty'] ?? '',
            ];
            GuideProfile::createOrUpdate($id, $guideData);
        }

        // Cập nhật session với tên mới
        $_SESSION['user_name'] = $_POST['name'];

        header('Location: ' . BASE_URL . 'profile?updated=1');
        exit;
    }
        
}
