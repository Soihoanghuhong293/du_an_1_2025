<?php
$pageTitle = isset($user) ? 'Chỉnh sửa Người dùng' : 'Thêm Người dùng';
ob_start();
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><?= $pageTitle ?></h4>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>users/store" method="post">
                <!-- Nếu sửa người dùng, gửi id -->
                <?php if(isset($user)): ?>
                    <input type="hidden" name="id" value="<?= $user->id ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Họ tên:</label>
                    <input type="text" name="name" class="form-control" placeholder="Tên đầy đủ" value="<?= $user->name ?? '' ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" placeholder="Email" value="<?= $user->email ?? '' ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mật khẩu:</label>
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu" <?= isset($user) ? '' : 'required' ?>>
                    <?php if(isset($user)): ?>
                        <small class="text-muted">Để trống nếu không muốn đổi mật khẩu</small>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role:</label>
                    <select name="role" class="form-select">
                        <option value="user" <?= isset($user) && $user->role=='user' ? 'selected' : '' ?>>User</option>
                        <option value="guide" <?= isset($user) && $user->role=='guide' ? 'selected' : '' ?>>Guide</option>
                        <option value="admin" <?= isset($user) && $user->role=='admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status:</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= isset($user) && $user->status ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= isset($user) && isset($user->status) && !$user->status ? 'selected' : '' ?>>Khóa</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Lưu</button>
            </form>
        </div>
        <a href="<?= BASE_URL ?>users" class="btn btn-secondary btn-sm">Quay lại</a>

    </div>
    
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../views/layouts/AdminLayout.php';
