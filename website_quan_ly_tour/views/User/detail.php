<?php
$pageTitle = 'Chi tiết Người dùng';
ob_start();
?>

<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><?= htmlspecialchars($user->name) ?></h4>
    </div>
    <div class="card-body">
        <p><strong>Email:</strong> <?= htmlspecialchars($user->email) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user->role) ?></p>
        <p><strong>Status:</strong> 
            <span class="<?= $user->status ? 'text-success' : 'text-danger' ?>">
                <?= $user->status ? 'Hoạt động' : 'Khóa' ?>
            </span>
        </p>
    </div>
    <a href="<?= BASE_URL ?>users" class="btn btn-secondary btn-sm">Quay lại</a>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../views/layouts/AdminLayout.php';
