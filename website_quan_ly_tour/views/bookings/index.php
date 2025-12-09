<?php ob_start(); 
// Lấy từ khóa tìm kiếm hiện tại (nếu có) để hiển thị lại trong ô input
$currentKeyword = isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/index.css">

<div class="row">
    <div class="col-12">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 fw-bold text-dark">Quản lý Booking</h3>
                <p class="text-muted mb-0">Danh sách các tour đã được đặt</p>
            </div>
            <div>
                <a href="index.php?act=booking-create" class="btn btn-primary px-4 rounded-pill shadow-sm fw-bold">
                    <i class="bi bi-plus-lg me-2"></i> Tạo Booking Mới
                </a>
            </div>
        </div>

        <div class="card card-modern">
            
            <div class="card-header-modern">
                <form action="index.php" method="GET" class="d-flex gap-2 align-items-center w-100">
                    <input type="hidden" name="act" value="bookings">
                    
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" 
                               name="keyword" 
                               class="form-control bg-light border-start-0" 
                               placeholder="Tìm theo tên tour, khách hàng..." 
                               value="<?= $currentKeyword ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-light border fw-bold text-primary">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>

                    <?php if(!empty($currentKeyword)): ?>
                        <a href="index.php?act=bookings" class="btn btn-outline-danger border" title="Xóa tìm kiếm">
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
                            <th>Lịch trình</th>
                            <th>Nhân sự</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Ngày tạo</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($bookings)) : ?>
                            <?php foreach ($bookings as $bk): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 d-none d-md-block">
                                                <div class="bg-light rounded p-2 text-primary">
                                                    <i class="bi bi-airplane-fill fs-4"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="tour-name"><?= htmlspecialchars($bk['tour_name'] ?? 'Chưa đặt tên') ?></span>
                                                <div class="small text-muted">
                                                    ID: <span class="booking-id fw-bold">#<?= htmlspecialchars($bk['id']) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="date-box text-end">
                                                <strong><?= !empty($bk['start_date']) ? date('d/m', strtotime($bk['start_date'])) : '--' ?></strong>
                                                <span><?= !empty($bk['start_date']) ? date('Y', strtotime($bk['start_date'])) : '' ?></span>
                                            </div>
                                            <i class="bi bi-arrow-right text-muted small"></i>
                                            <div class="date-box text-start">
                                                <strong><?= !empty($bk['end_date']) ? date('d/m', strtotime($bk['end_date'])) : '--' ?></strong>
                                                <span><?= !empty($bk['end_date']) ? date('Y', strtotime($bk['end_date'])) : '' ?></span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center mb-2" title="Người tạo đơn">
                                            <?php 
                                                $creatorInitial = substr($bk['creator_name'] ?? 'A', 0, 1);
                                            ?>
                                            <div class="avatar-circle bg-soft-secondary small" style="width: 25px; height: 25px;">
                                                <?= strtoupper($creatorInitial) ?>
                                            </div>
                                            <span class="text-muted small ms-2"><?= htmlspecialchars($bk['creator_name'] ?? 'N/A') ?></span>
                                        </div>

                                        <div class="d-flex align-items-center" title="Hướng dẫn viên">
                                            <?php if (!empty($bk['guide_name'])): 
                                                $guideInitial = substr($bk['guide_name'], 0, 1);
                                            ?>
                                                <div class="avatar-circle bg-soft-success small" style="width: 25px; height: 25px;">
                                                    <?= strtoupper($guideInitial) ?>
                                                </div>
                                                <span class="text-dark small fw-medium ms-2"><?= htmlspecialchars($bk['guide_name']) ?></span>
                                            <?php else: ?>
                                                <div class="avatar-circle bg-light border small" style="width: 25px; height: 25px;">
                                                    <i class="bi bi-person-dash text-muted" style="font-size: 10px;"></i>
                                                </div>
                                                <span class="text-muted small fst-italic ms-2">Chưa phân công</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <?php 
                                            $statusClass = 'badge-soft-secondary';
                                            $statusLabel = 'Khác';
                                            
                                            if ($bk['status'] == 1) {
                                                $statusClass = 'badge-soft-warning';
                                                $statusLabel = 'Chờ xác nhận';
                                            } elseif ($bk['status'] == 2) {
                                                $statusClass = 'badge-soft-info';
                                                $statusLabel = 'Đã cọc';
                                            } elseif ($bk['status'] == 3) {
                                                $statusClass = 'badge-soft-success';
                                                $statusLabel = 'Hoàn tất';
                                            } elseif ($bk['status'] == 4) {
                                                 $statusClass = 'badge-soft-danger';
                                                 $statusLabel = 'Đã hủy';
                                            }
                                        ?>
                                        <span class="badge badge-soft <?= $statusClass ?>">
                                            <?= $statusLabel ?>
                                        </span>
                                    </td>

                                    <td class="text-center text-muted small">
                                        <?= !empty($bk['created_at']) ? date('d/m/Y', strtotime($bk['created_at'])) : '-' ?>
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="index.php?act=booking-show&id=<?= $bk['id'] ?>" class="btn-icon btn-icon-view me-1" title="Xem chi tiết">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="index.php?act=booking-delete&id=<?= $bk['id'] ?>" 
                                           class="btn-icon btn-icon-delete" 
                                           onclick="return confirm('Bạn chắc chắn muốn xóa booking #<?= $bk['id'] ?> này?')"
                                           title="Xóa booking">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-search fs-1 d-block mb-3"></i>
                                        <?php if(!empty($currentKeyword)): ?>
                                            Không tìm thấy kết quả nào cho từ khóa: "<strong><?= $currentKeyword ?></strong>"
                                            <br><a href="index.php?act=bookings" class="fw-bold text-primary">Xóa bộ lọc</a>
                                        <?php else: ?>
                                            Hiện chưa có booking nào. <br>
                                            <a href="index.php?act=booking-create" class="fw-bold text-primary">Tạo booking mới ngay</a>
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
                    <small class="text-muted">Hiển thị <?= count($bookings) ?> kết quả</small>
                    </div>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

view('layouts.AdminLayout', [
    'title' => 'Danh sách Booking',
    'pageTitle' => '  Hệ thống Tour Fpoly  ',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Dashboard', 'url' => BASE_URL . 'home'],
        ['label' => 'Booking', 'active' => true],
    ],
]);
?>