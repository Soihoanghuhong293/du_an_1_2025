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
// File: commons/function.php (hoặc helper.php)

function view($view, $data = [])
{
    $viewPath = './views/' . str_replace('.', '/', $view) . '.php';

    if (file_exists($viewPath)) {
        // 1. Giải nén biến
        extract($data);

        // 2. Bắt đầu bộ nhớ đệm (Tạo môi trường để ob_get_clean ở View con hoạt động)
        ob_start();

        // 3. Nhúng file View
        // (Lúc này file View sẽ chạy, và dòng ob_get_clean() của bạn sẽ hút hết nội dung trong này)
        require $viewPath;

        // 4. Đẩy dữ liệu ra màn hình
        // Nếu file view con ĐÃ dùng ob_get_clean() thì buffer rỗng -> lệnh này không làm gì (đúng ý bạn).
        // Nếu file view là Layout (được gọi đệ quy) -> lệnh này sẽ in toàn bộ web ra trình duyệt.
        if (ob_get_level() > 0) {
            ob_end_flush();
        }
    } else {
        echo "Lỗi: Không tìm thấy view $viewPath";
    }
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
function requireLogin(?string $redirectUrl = null): void
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
function url(string $act = '', array $params = []): string
{
    // BASE_URL luôn kết thúc bằng /
    $base = rtrim(BASE_URL, '/') . '/?act=' . $act;

    if (!empty($params)) {
        $base .= '&' . http_build_query($params);
    }

    return $base;
}
function redirect(string $act, array $params = []): void
{
    $url = url($act, $params);
    header("Location: " . $url);
    exit;
}

