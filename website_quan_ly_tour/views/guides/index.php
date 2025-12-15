

<?php if (function_exists('extend')) extend('layouts/AdminLayout.php'); ?>


<?php ob_start(); ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/index.css">

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 fw-bold text-dark">Quản lý Hướng dẫn viên</h3>
                <p class="text-muted mb-0">Danh sách hướng dẫn viên trong hệ thống</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>guides/create" class="btn btn-primary px-4 rounded-pill shadow-sm fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i> Thêm hướng dẫn viên
                </a>
            </div>
        </div>

        <div class="card card-modern">
            <div class="card-header-modern">
                <form action="<?= BASE_URL ?>guides" method="GET" class="d-flex gap-2 align-items-center w-100">
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control bg-light border-start-0" placeholder="Tìm theo tên, email..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-light border fw-bold text-primary">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Thông tin thành viên</th>
                            <th>Thông tin HDV</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($guides)): ?>
                            <?php foreach ($guides as $g): ?>
                                <?php 
                                    $firstLetter = strtoupper(substr($g['name'] ?? ($g['user_id'] ?? 'U'), 0, 1));
                                    $bgAvatar = 'bg-soft-success';
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="avatar-circle <?= $bgAvatar ?> d-flex align-items-center justify-content-center rounded-circle" style="width: 45px; height: 45px; font-weight: bold; font-size: 18px;">
                                                    <?= $firstLetter ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($g['name'] ?? '—') ?></div>
                                                <div class="small text-muted"><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($g['email'] ?? '-') ?></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="small text-muted">ID profile: #<?= htmlspecialchars($g['id']) ?></div>
                                        <div class="mt-1">Kinh nghiệm: <?= htmlspecialchars($g['experience'] ?? '-') ?></div>
                                        <div class="small text-muted">Ngôn ngữ: <?= htmlspecialchars($g['languages'] ?? '-') ?></div>
                                    </td>

                                    <td class="text-center">
                                        <?php if (!empty($g['status']) && $g['status'] == 1): ?>
                                            <span class="badge badge-soft-success"><i class="bi bi-check-circle me-1"></i> Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge badge-soft-secondary"><i class="bi bi-lock-fill me-1"></i> Không hoạt động</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="<?= BASE_URL ?>guides/show?id=<?= $g['id'] ?>" class="btn-icon btn-icon-view me-1" title="Xem chi tiết"><i class="bi bi-eye-fill"></i></a>
                                        <a href="<?= BASE_URL ?>guides/edit?id=<?= $g['id'] ?>" class="btn-icon btn-icon-edit me-1" title="Chỉnh sửa"><i class="bi bi-pencil-fill"></i></a>
                                        <a href="<?= BASE_URL ?>guides/delete?id=<?= $g['id'] ?>" class="btn-icon btn-icon-delete" onclick="return confirm('Xác nhận xóa hồ sơ HDV này?')" title="Xóa"><i class="bi bi-trash-fill"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Chưa có hồ sơ hướng dẫn viên nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Hiển thị <?= count($guides) ?> hồ sơ</small>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

view('layouts.AdminLayout', [
    'title' => 'Quản lý Hướng dẫn viên',
    'pageTitle' => 'Guides',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Dashboard', 'url' => BASE_URL . 'home'],
        ['label' => 'Guides', 'active' => true],
    ],
]);
