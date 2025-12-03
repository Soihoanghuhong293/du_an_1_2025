<?php

// Hàm xác định đường dẫn tuyệt đối tới file view
function view_path(string $view): string
{
    $normalized = str_replace('.', DIRECTORY_SEPARATOR, $view);
    return BASE_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $normalized . '.php';
}

// Hàm xác định đường dẫn tuyệt đối tới file block (layouts)
function block_path(string $block): string
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'blocks' . DIRECTORY_SEPARATOR . $block . '.php';
}


// Hiển thị view với dữ liệu truyền vào
function view(string $view, array $data = []): void
{
    $file = view_path($view);

    if (!file_exists($file)) {
        throw new RuntimeException("View '{$view}' not found at {$file}");
    }

    extract($data, EXTR_OVERWRITE); // Biến hóa mảng $data thành biến riêng lẻ
    include $file;
}

// Hiển thị block layout với dữ liệu truyền vào
function block(string $block, array $data = []): void
{
    $file = block_path($block);

    if (!file_exists($file)) {
        throw new RuntimeException("Block '{$block}' not found at {$file}");
    }

    extract($data, EXTR_OVERWRITE);
    include $file;
}

// Tạo đường dẫn tới asset trong thư mục public
function asset(string $path): string
{
    $trimmed = ltrim($path, '/');
    return rtrim(BASE_URL, '/') . '/public/' . $trimmed;
}

// Khởi động session nếu chưa khởi động
function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Lưu thông tin user vào session sau khi đăng nhập
function loginUser(User $user): void
{
    startSession();
    $_SESSION['user_id']    = $user->id;
    $_SESSION['user_name']  = $user->name;
    $_SESSION['user_email'] = $user->email;
    $_SESSION['user_role']  = $user->role;
}

// Đăng xuất: xóa toàn bộ thông tin user khỏi session
function logoutUser(): void
{
    startSession();
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_role']);
    session_destroy();
}

// Kiểm tra xem user đã đăng nhập chưa
function isLoggedIn(): bool
{
    startSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Lấy thông tin user hiện tại từ session
function getCurrentUser(): ?User
{
    if (!isLoggedIn()) return null;

    startSession();
    return new User([
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role'  => $_SESSION['user_role'],
    ]);
}

// Kiểm tra xem user hiện tại có phải admin
function isAdmin(): bool
{
    $user = getCurrentUser();
    return $user ? $user->isAdmin() : false;
}

// Kiểm tra xem user hiện tại có phải hướng dẫn viên
function isGuide(): bool
{
    $user = getCurrentUser();
    return $user ? $user->isGuide() : false;
}

// Yêu cầu đăng nhập: nếu chưa login thì chuyển hướng về trang login
function requireLogin(string $redirectUrl = null): void
{
    if (!isLoggedIn()) {
        $redirect = $redirectUrl ?: $_SERVER['REQUEST_URI'];
        header('Location: ' . BASE_URL . '?act=login&redirect=' . urlencode($redirect));
        exit;
    }
}

// Yêu cầu quyền admin: nếu không phải admin thì chuyển về trang chủ
function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . BASE_URL);
        exit;
    }
}

// Yêu cầu quyền hướng dẫn viên hoặc admin
function requireGuideOrAdmin(): void
{
    requireLogin();
    if (!isGuide() && !isAdmin()) {
        header('Location: ' . BASE_URL);
        exit;
    }
}
