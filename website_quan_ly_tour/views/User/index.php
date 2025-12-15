<?php ob_start(); 
// Lấy từ khóa tìm kiếm (nếu có)
$currentKeyword = isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/index.css">

<div class="row">
    <div class="col-12">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 fw-bold text-dark">Quản lý Tài khoản</h3>
                <p class="text-muted mb-0">Danh sách quản trị viên, hướng dẫn viên và nhân viên</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>users/create" class="btn btn-primary px-4 rounded-pill shadow-sm fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i> Thêm người dùng
                </a>
            </div>
        </div>

        <div class="card card-modern">
            
            <div class="card-header-modern">
                <form action="<?= BASE_URL ?>users" method="GET" class="d-flex gap-2 align-items-center w-100">
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" 
                               name="keyword" 
                               class="form-control bg-light border-start-0" 
                               placeholder="Tìm theo tên, email..." 
                               value="<?= $currentKeyword ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-light border fw-bold text-primary">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>

                    <?php if(!empty($currentKeyword)): ?>
                        <a href="<?= BASE_URL ?>users" class="btn btn-outline-danger border" title="Xóa tìm kiếm">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Thông tin thành viên</th>
                            <th>Vai trò (Role)</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $u): ?>
                                <?php 
                                    // Tạo avatar chữ cái đầu
                                    $firstLetter = strtoupper(substr($u->name ?? 'U', 0, 1));
                                    $bgAvatar = 'bg-soft-primary';
                                    if ($u->role == 'admin') $bgAvatar = 'bg-soft-danger';
                                    if ($u->role == 'guide') $bgAvatar = 'bg-soft-success';
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
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($u->name) ?></div>
                                                <div class="small text-muted">
                                                    <i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($u->email) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <?php if ($u->role == 'admin'): ?>
                                            <span class="badge badge-soft-danger text-uppercase">Admin</span>
                                        <?php elseif ($u->role == 'guide'): ?>
                                            <span class="badge badge-soft-success text-uppercase">Hướng dẫn viên</span>
                                        <?php else: ?>
                                            <span class="badge badge-soft-secondary text-uppercase"><?= htmlspecialchars($u->role) ?></span>
                                        <?php endif; ?>
                                        <div class="small text-muted mt-1">ID: #<?= $u->id ?></div>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($u->status == 1): ?>
                                            <span class="badge badge-soft-success">
                                                <i class="bi bi-check-circle me-1"></i> Hoạt động
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-soft-secondary">
                                                <i class="bi bi-lock-fill me-1"></i> Đã khóa
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="<?= BASE_URL ?>users/show?id=<?= $u->id ?>" class="btn-icon btn-icon-view me-1" title="Xem chi tiết">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <a href="<?= BASE_URL ?>users/edit?id=<?= $u->id ?>" class="btn-icon btn-icon-edit me-1" title="Chỉnh sửa">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        <?php if ($u->status == 1): ?>
                                            <a href="<?= BASE_URL ?>users/toggleStatus?id=<?= $u->id ?>" 
                                               onclick="return confirm('Bạn có chắc muốn KHÓA tài khoản này?')"
                                               class="btn-icon btn-light text-warning me-1 border" 
                                               title="Khóa tài khoản">
                                                <i class="bi bi-lock-fill"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= BASE_URL ?>users/toggleStatus?id=<?= $u->id ?>" 
                                               onclick="return confirm('Bạn muốn MỞ KHÓA tài khoản này?')"
                                               class="btn-icon btn-light text-success me-1 border" 
                                               title="Mở khóa tài khoản">
                                                <i class="bi bi-unlock-fill"></i>
                                            </a>
                                        <?php endif; ?>

                                        <a href="<?= BASE_URL ?>users/delete?id=<?= $u->id ?>" 
                                           class="btn-icon btn-icon-delete" 
                                           onclick="return confirm('Xác nhận xóa tài khoản này? Hành động này không thể hoàn tác!')"
                                           title="Xóa người dùng">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-3"></i>
                                        <?php if(!empty($currentKeyword)): ?>
                                            Không tìm thấy người dùng nào với từ khóa: "<strong><?= $currentKeyword ?></strong>"
                                            <br><a href="<?= BASE_URL ?>users" class="fw-bold text-primary">Xóa bộ lọc</a>
                                        <?php else: ?>
                                            Chưa có tài khoản nào trong hệ thống.
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Hiển thị <?= count($users) ?> tài khoản</small>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

view('layouts.AdminLayout', [
    'title' => 'Quản lý Tài khoản',
    'pageTitle' => 'Account',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Dashboard', 'url' => BASE_URL . 'home'],
        ['label' => 'Account', 'active' => true],
    ],
]);
?>