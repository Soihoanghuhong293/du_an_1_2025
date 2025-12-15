<?php 
ob_start(); 
// Lấy từ khóa tìm kiếm (nếu có)
$currentKeyword = isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/index.css">

<div class="row">
    <div class="col-12">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 fw-bold text-dark">Quản lý Danh mục</h3>
                <p class="text-muted mb-0">Phân loại tour du lịch</p>
            </div>
            <div>
                <a href="index.php?act=category-create" class="btn btn-primary px-4 rounded-pill shadow-sm fw-bold">
                    <i class="bi bi-plus-lg me-2"></i> Thêm danh mục
                </a>
            </div>
        </div>

        <div class="card card-modern">
            
            <div class="card-header-modern">
                <form action="index.php" method="GET" class="d-flex gap-2 align-items-center w-100">
                    <input type="hidden" name="act" value="categories"> <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" 
                               name="keyword" 
                               class="form-control bg-light border-start-0" 
                               placeholder="Tìm tên danh mục..." 
                               value="<?= $currentKeyword ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-light border fw-bold text-primary">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>

                    <?php if(!empty($currentKeyword)): ?>
                        <a href="index.php?act=categories" class="btn btn-outline-danger border" title="Xóa tìm kiếm">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Tên danh mục</th>
                            <th>Mô tả</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Ngày tạo</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $item): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($item['name']) ?></div>
                                        <small class="text-muted">ID: #<?= $item['id'] ?></small>
                                    </td>

                                    <td style="max-width: 300px;">
                                        <div class="text-truncate text-secondary" title="<?= htmlspecialchars($item['description']) ?>">
                                            <?= htmlspecialchars($item['description']) ?: '<em class="text-muted small">Không có mô tả</em>' ?>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($item['status'] == 1): ?>
                                            <span class="badge badge-soft-success">
                                                <i class="bi bi-check-circle me-1"></i> Hoạt động
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-soft-secondary">
                                                <i class="bi bi-eye-slash me-1"></i> Tạm ẩn
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center text-muted small">
                                        <?= !empty($item['created_at']) ? date('d/m/Y', strtotime($item['created_at'])) : '-' ?>
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="index.php?act=category-edit&id=<?= $item['id'] ?>" class="btn-icon btn-icon-edit me-1" title="Chỉnh sửa">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        <a href="index.php?act=category-delete&id=<?= $item['id'] ?>" 
                                           class="btn-icon btn-icon-delete" 
                                           onclick="return confirm('Bạn chắc chắn muốn xóa danh mục này? Các tour thuộc danh mục này có thể bị ảnh hưởng!')"
                                           title="Xóa danh mục">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-folder2-open fs-1 d-block mb-3"></i>
                                        <?php if(!empty($currentKeyword)): ?>
                                            Không tìm thấy danh mục nào với từ khóa: "<strong><?= $currentKeyword ?></strong>"
                                            <br><a href="index.php?act=categories" class="fw-bold text-primary">Xóa bộ lọc</a>
                                        <?php else: ?>
                                            Chưa có danh mục nào. <br>
                                            <a href="index.php?act=category-form" class="btn btn-primary">
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
                    <small class="text-muted">Hiển thị <?= count($categories) ?> danh mục</small>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
// Lấy toàn bộ nội dung vừa tạo
$content = ob_get_clean();

// Hiển thị layout Admin
view('layouts.AdminLayout', [
    'title' => 'Quản lý Danh mục',
    'pageTitle' => 'Categories',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Dashboard', 'url' => BASE_URL . 'home'],
        ['label' => 'Danh mục', 'active' => true],
    ],
]);
?>