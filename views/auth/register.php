<?php
// Sử dụng layout auth và truyền nội dung vào
ob_start();
?>
<!--begin::Register Content-->
<div class="login-wrapper">
    <div class="col-12 col-md-8 col-lg-5 col-xl-4">
        <div class="card login-card shadow-lg border-0">
            <div class="login-header text-center text-white">
                <a href="<?= BASE_URL ?>" class="text-white text-decoration-none">
                    <div class="brand-icon mb-2">
                        <i class="bi bi-airplane-fill"></i>
                    </div>
                    <h2><strong>Quản Lý Tour FPOLY</strong></h2>
                </a>
                <div class="mt-2 fw-light fst-italic" style="font-size: 1rem;">
                    Tạo tài khoản để tiếp tục
                </div>
            </div>

            <div class="card-body">
                <h4 class="card-title text-center mb-4 fw-bold card-title-login">Đăng ký tài khoản</h4>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger fade show" role="alert">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                        <strong>Lỗi đăng ký</strong>
                    </div>
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>handle-register" method="post" autocomplete="on" novalidate>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ và tên</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="fullname" class="form-control" placeholder="Nhập họ tên" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="Nhập email" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu" required>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-login btn-lg">
                            <i class="bi bi-person-plus-fill me-2"></i>
                            Tạo tài khoản
                        </button>

                        <p class="text-center mt-3">
                            Bạn đã có tài khoản?
                            <a href="<?= BASE_URL ?>login">Đăng nhập ngay</a>
                        </p>
                    </div>
                </form>

                <div class="login-divider"></div>

                <div class="text-center">
                    <a href="<?= BASE_URL ?>" class="text-decoration-none text-fpt-orange fw-semibold">
                        <i class="bi bi-arrow-left me-2"></i>
                        Quay về trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Register Content-->

<?php
$content = ob_get_clean();

view('layouts.AuthLayout', [
    'title' => 'Đăng ký',
    'content' => $content
]);
?>
