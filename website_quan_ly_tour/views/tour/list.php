<?php ob_start(); 
// Lấy từ khóa tìm kiếm (nếu có)
$currentKeyword = isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/index.css">

<div class="row">
    <div class="col-12">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 fw-bold text-dark">Quản lý Tour</h3>
                <p class="text-muted mb-0">Danh sách các tour du lịch hiện có</p>
            </div>
            <div>
                <a href="index.php?act=tour-create" class="btn btn-primary px-4 rounded-pill shadow-sm fw-bold">
                    <i class="bi bi-plus-lg me-2"></i> Thêm Tour Mới
                </a>
            </div>
        </div>

        <div class="card card-modern">
            
            <div class="card-header-modern">
                <form action="index.php" method="GET" class="d-flex gap-2 align-items-center w-100">
                    <input type="hidden" name="act" value="tours">
                    
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" 
                               name="keyword" 
                               class="form-control bg-light border-start-0" 
                               placeholder="Tìm theo tên tour, mã tour..." 
                               value="<?= $currentKeyword ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-light border fw-bold text-primary">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>

                    <?php if(!empty($currentKeyword)): ?>
                        <a href="index.php?act=tours" class="btn btn-outline-danger border" title="Xóa tìm kiếm">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    <?php endif; ?>
                </form>

              
            </div>

            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Thông tin Tour</th>
                            <th>Thời lượng</th>
                            <th>Giá niêm yết</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tours)): ?>
                            <?php foreach ($tours as $tour): ?>
                                <?php
                                    // Xử lý ảnh
                                    $images = is_array($tour['images']) ? $tour['images'] : json_decode($tour['images'], true);
                                    $firstImage = !empty($images) ? $images[0] : null;
                                    
                                    $imgSrc = 'public/assets/img/no-image.png'; // Ảnh mặc định
                                    if ($firstImage) {
                                        if (strpos($firstImage, 'http') === 0) {
                                            $imgSrc = $firstImage;
                                        } else {
                                            $imgSrc = (strpos($firstImage, 'uploads/') !== false) ? 'public/' . $firstImage : 'public/uploads/tours/' . $firstImage;
                                        }
                                        if (defined('BASE_URL')) $imgSrc = BASE_URL . $imgSrc;
                                    }
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="rounded overflow-hidden border shadow-sm" style="width: 50px; height: 50px;">
                                                    <img src="<?= $imgSrc ?>" alt="Tour" class="w-100 h-100 object-fit-cover">
                                                </div>
                                            </div>
                                            <div>
                                                <span class="tour-name fw-bold text-dark"><?= htmlspecialchars($tour['name']) ?></span>
                                                <div class="small text-muted">
                                                    ID: <span class="fw-bold">#<?= $tour['id'] ?></span> &bull; 
                                                    <span class="text-info"><?= htmlspecialchars($tour['category_name'] ?? 'Chung') ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="bi bi-clock-history me-2"></i>
                                            <span class="fw-medium text-dark"><?= $tour['duration_days'] ?> Ngày</span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="fw-bold text-success">
                                            <?= number_format($tour['price'], 0, ',', '.') ?> ₫
                                        </div>
                                        <small class="text-muted" style="font-size: 11px;">/ Khách</small>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($tour['status'] == 1): ?>
                                            <span class="badge badge-soft-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge badge-soft-secondary">Tạm ẩn</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="index.php?act=tour-show&id=<?= $tour['id'] ?>" class="btn-icon btn-icon-view me-1" title="Xem chi tiết">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="index.php?act=tour-edit&id=<?= $tour['id'] ?>" class="btn-icon btn-icon-edit me-1" title="Chỉnh sửa">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="index.php?act=tour-delete&id=<?= $tour['id'] ?>" 
                                           class="btn-icon btn-icon-delete" 
                                           onclick="return confirm('Bạn chắc chắn muốn xóa tour này?')"
                                           title="Xóa tour">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-geo-alt fs-1 d-block mb-3"></i>
                                        <?php if(!empty($currentKeyword)): ?>
                                            Không tìm thấy tour nào với từ khóa: "<strong><?= $currentKeyword ?></strong>"
                                            <br><a href="index.php?act=tours" class="fw-bold text-primary">Xóa bộ lọc</a>
                                        <?php else: ?>
                                            Chưa có dữ liệu tour. <br>
                                            <a href="index.php?act=tour-create" class="fw-bold text-primary">Thêm tour đầu tiên</a>
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
                    <small class="text-muted">Hiển thị <?= count($tours) ?> kết quả</small>
                </div>
            </div>

        </div>
    </div>
</div>

