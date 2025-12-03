<?php
$pageTitle = 'Danh sách Người dùng';
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><?= $pageTitle ?></h3>
        <a href="<?= BASE_URL ?>users/create" class="btn btn-primary">Thêm người dùng</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><?= $u->id ?></td>
                    <td><?= htmlspecialchars($u->name) ?></td>
                    <td><?= htmlspecialchars($u->email) ?></td>
                    <td><?= htmlspecialchars($u->role) ?></td>
                    <td>
                        <span class="<?= $u->status ? 'text-success' : 'text-danger' ?>">
                            <?= $u->status ? 'Hoạt động' : 'Khóa' ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>users/show?id=<?= $u->id ?>" class="btn btn-sm btn-info">Xem</a>
                        <a href="<?= BASE_URL ?>users/edit?id=<?= $u->id ?>" class="btn btn-sm btn-warning">Sửa</a>
                        <a href="<?= BASE_URL ?>users/delete?id=<?= $u->id ?>" 
                           onclick="return confirm('Xác nhận xóa?')" class="btn btn-sm btn-danger">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($users)): ?>
                <tr>
                    <td colspan="6" class="text-center">Không có người dùng nào.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../views/layouts/AdminLayout.php';
