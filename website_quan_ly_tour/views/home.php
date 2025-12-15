
<?php
ob_start();
?>

<!-- HERO -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm text-white"
             style="background: linear-gradient(135deg, #0d6efd, #20c997);">
            <div class="card-body py-5 px-4">
                <h1 class="fw-bold mb-2">
                    <i class="bi bi-globe-asia-australia me-2"></i>
                    Khám phá & Quản lý Tour Du Lịch
                </h1>
                <p class="fs-5 mb-0">
                    Hệ thống quản lý tour chuyên nghiệp – nhanh chóng – hiệu quả
                </p>
            </div>
        </div>
    </div>
</div>

<!-- STATS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body">
                <i class="bi bi-map fs-1 text-primary"></i>
                <h5 class="mt-2 mb-0">Tour du lịch</h5>
                <p class="text-muted">Lịch trình & điểm đến</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body">
                <i class="bi bi-people fs-1 text-success"></i>
                <h5 class="mt-2 mb-0">Khách hàng</h5>
                <p class="text-muted">Theo dõi booking</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body">
                <i class="bi bi-person-workspace fs-1 text-warning"></i>
                <h5 class="mt-2 mb-0">Hướng dẫn viên</h5>
                <p class="text-muted">Phân công tour</p>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <h3 class="card-title mb-0">
                    <i class="bi bi-house-door-fill me-2 text-primary"></i>
                    Trang chủ hệ thống
                </h3>
            </div>

            <div class="card-body">

                <!-- CHÀO MỪNG -->
                <div class="alert alert-success border-0 shadow-sm">
                    <h4 class="alert-heading">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Chào mừng bạn quay lại!
                    </h4>
                    <p class="mb-0">
                        Xin chào <strong><?= htmlspecialchars($user->name) ?></strong>,
                        bạn đang đăng nhập với quyền
                        <strong><?= $user->isAdmin() ? 'Quản trị viên' : 'Hướng dẫn viên' ?></strong>.
                    </p>
                </div>

                <div class="row mt-4">
                    <!-- USER INFO -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">
                                    <i class="bi bi-person-circle me-2 text-primary"></i>
                                    Thông tin tài khoản
                                </h5>

                                <p>
                                    <i class="bi bi-envelope me-2"></i>
                                    <strong>Email:</strong>
                                    <?= htmlspecialchars($user->email) ?>
                                </p>

                                <p class="mb-0">
                                    <i class="bi bi-shield-lock me-2"></i>
                                    <strong>Vai trò:</strong>
                                    <?= $user->isAdmin() ? 'Quản trị viên' : 'Hướng dẫn viên' ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- QUICK VIEW -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">
                                    <i class="bi bi-lightning-fill me-2 text-warning"></i>
                                    Thông tin nhanh
                                </h5>

                                <?php if ($user->isAdmin()): ?>
                                    <!-- ADMIN -->
                                    <a href="<?= BASE_URL ?>?act=tours"
                                       class="btn btn-outline-primary w-100 mb-2">
                                        <i class="bi bi-map me-2"></i>
                                        Danh sách tour
                                    </a>

                                    <a href="<?= BASE_URL ?>?act=bookings"
                                       class="btn btn-outline-success w-100 mb-2">
                                        <i class="bi bi-journal-text me-2"></i>
                                        Đặt tour
                                    </a>

                                    <a href="<?= BASE_URL ?>?act=guides"
                                       class="btn btn-outline-warning w-100">
                                        <i class="bi bi-person-workspace me-2"></i>
                                        Hướng dẫn viên
                                    </a>

                                <?php else: ?>
                                    <!-- HƯỚNG DẪN VIÊN: CHỈ HIỂN THỊ -->
                                    <div class="alert alert-info border-0 shadow-sm">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Bạn đang xem giao diện dành cho
                                        <strong>Hướng dẫn viên</strong>
                                    </div>

                                    <div class="card border-0 shadow-sm mb-2">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="bi bi-geo-alt fs-3 text-primary me-3"></i>
                                            <div>
                                                <h6 class="mb-0">Tour được phân công</h6>
                                                <small class="text-muted">
                                                    Danh sách tour bạn tham gia
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="bi bi-calendar-check fs-3 text-success me-3"></i>
                                            <div>
                                                <h6 class="mb-0">Lịch làm việc</h6>
                                                <small class="text-muted">
                                                    Lịch trình & nhiệm vụ
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

view('layouts.AdminLayout', [
    'title' => $title ?? 'Trang chủ - Website Quản Lý Tour',
    'pageTitle' => 'Trang chủ',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Trang chủ', 'url' => BASE_URL . 'home', 'active' => true],
    ],
]);
