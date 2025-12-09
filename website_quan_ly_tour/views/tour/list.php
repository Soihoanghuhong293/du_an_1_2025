<?php ob_start(); ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/index.css">

<div class="row">
    <div class="col-12">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 fw-bold text-dark">Quản lý Tours</h3>
                <p class="text-muted mb-0">Danh sách các tour du lịch</p>
            </div>
            <div>
                <a href="index.php?act=tour-add" class="btn btn-primary px-4 rounded-pill shadow-sm fw-bold">
                    <i class="bi bi-plus-lg me-2"></i> Thêm Tour Mới
                </a>
            </div>
        </div>

        <!-- Card -->
        <div class="card card-modern">

            <!-- Card Header -->
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                
            <form method="GET" action="index.php" class="d-flex align-items-center gap-2 mb-3">
            <form method="GET" action="index.php" class="d-flex align-items-center gap-2 mb-3">
                <input type="hidden" name="act" value="tours">

                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tour..." 
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Tìm kiếm
                </button>

                <?php if (!empty($_GET['search'])): ?>
                    <!-- Nút chỉ hiện khi có từ khóa tìm kiếm -->
                    <a href="index.php?act=tours" class="btn btn-secondary">Hiển thị tất cả</a>
                <?php endif; ?>
            </form>
                <div>
                    <button class="btn btn-light btn-sm text-muted"><i class="bi bi-download"></i> Xuất Excel</button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Tên tour</th>
                            <th>Danh mục</th>
                            <th>Ảnh</th>
                            <th>Giá</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($tours)): ?>
                            <?php foreach ($tours as $tour): ?>
                                <?php 
                                    $images = $tour['images'] ?? [];
                                    $thumb = !empty($images) ? $images[0] : null;
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($tour['name']) ?></div>
                                        <div class="text-muted small">#<?= $tour['id'] ?></div>
                                    </td>

                                    <td><?= htmlspecialchars($tour['category_id']) ?></td>

                                    <td>
                                        <?php if ($thumb): ?>
                                            <img src="<?= BASE_URL ?>/public/dist/assets/img/<?= htmlspecialchars($thumb) ?>" 
                                                width="60" height="60" 
                                                class="rounded shadow-sm object-fit-cover">
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Không có ảnh</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="fw-bold"><?= number_format($tour['price']) ?> VNĐ</td>

                                    <td class="text-center">
                                        <?php 
                                            $statusLabel = $tour['status'] ? "Hiển thị" : "Ẩn";
                                            $statusClass = $tour['status'] ? "badge-soft-success" : "badge-soft-secondary";
                                        ?>
                                        <span class="badge badge-soft <?= $statusClass ?>"><?= $statusLabel ?></span>
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="index.php?act=tour-detail&id=<?= $tour['id'] ?>" class="btn-icon btn-icon-view me-1" title="Chi tiết">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <a href="index.php?act=tour-edit&id=<?= $tour['id'] ?>" class="btn-icon btn-icon-edit me-1" title="Sửa">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        <a href="index.php?act=tour-delete&id=<?= $tour['id'] ?>"
                                           onclick="return confirm('Bạn có chắc muốn xóa tour này?')"
                                           class="btn-icon btn-icon-delete" title="Xóa">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    Chưa có tour nào.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-0 py-3">
                <small class="text-muted">Hiển thị <?= count($tours) ?> tour</small>
            </div>
        </div>
    </div>
</div>
