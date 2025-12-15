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
                <h3 class="mb-1 fw-bold text-dark">Lịch dẫn tour</h3>
                <p class="text-muted mb-0">Danh sách các tour bạn được phân công phụ trách</p>
            </div>
            </div>

        <div class="card card-modern">
            
            <div class="card-header-modern">
                <form action="index.php" method="GET" class="d-flex gap-2 align-items-center w-100">
                    <input type="hidden" name="act" value="guide-index"> <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" 
                               name="keyword" 
                               class="form-control bg-light border-start-0" 
                               placeholder="Tìm tên tour..." 
                               value="<?= $currentKeyword ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-light border fw-bold text-primary">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>

                    <?php if(!empty($currentKeyword)): ?>
                        <a href="index.php?act=guide-index" class="btn btn-outline-danger border" title="Xóa tìm kiếm">
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
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($bookings)): ?>
                            <?php foreach ($bookings as $b): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="bg-light rounded p-2 text-success border">
                                                    <i class="bi bi-geo-alt-fill fs-4"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($b['tour_name']) ?></div>
                                                <small class="text-muted">Booking ID: <span class="fw-bold text-dark">#<?= $b['id'] ?></span></small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="date-box text-end">
                                                <strong class="text-primary"><?= date('d/m', strtotime($b['start_date'])) ?></strong>
                                                <span class="text-muted small"><?= date('Y', strtotime($b['start_date'])) ?></span>
                                            </div>
                                            <i class="bi bi-arrow-right text-muted small"></i>
                                            <div class="date-box text-start">
                                                <strong class="text-primary"><?= date('d/m', strtotime($b['end_date'])) ?></strong>
                                                <span class="text-muted small"><?= date('Y', strtotime($b['end_date'])) ?></span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <?php 
                                            $statusClass = 'badge-soft-secondary';
                                            $statusLabel = 'Không xác định';
                                            
                                            if ($b['status'] == 1) {
                                                $statusClass = 'badge-soft-warning';
                                                $statusLabel = 'Chờ xác nhận';
                                            } elseif ($b['status'] == 2) {
                                                $statusClass = 'badge-soft-info'; 
                                                $statusLabel = 'Đã nhận tour'; // Guide đã nhận
                                            } elseif ($b['status'] == 3) {
                                                $statusClass = 'badge-soft-success';
                                                $statusLabel = 'Hoàn tất';
                                            } elseif ($b['status'] == -1 || $b['status'] == 4) {
                                                $statusClass = 'badge-soft-danger';
                                                $statusLabel = 'Đã hủy / Từ chối';
                                            }
                                        ?>
                                        <span class="badge badge-soft <?= $statusClass ?>">
                                            <?= $statusLabel ?>
                                        </span>
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="index.php?act=guide-show&id=<?= $b['id'] ?>" 
                                           class="btn-icon btn-icon-view me-1" 
                                           title="Xem chi tiết & Quản lý">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                        <?php if(!empty($currentKeyword)): ?>
                                            Không tìm thấy tour nào với từ khóa: "<strong><?= $currentKeyword ?></strong>"
                                            <br><a href="index.php?act=guide-index" class="fw-bold text-primary">Xóa bộ lọc</a>
                                        <?php else: ?>
                                            Bạn chưa được phân công tour nào.
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
                    <small class="text-muted">Hiển thị <?= count($bookings) ?> tour</small>
                </div>
            </div>

        </div>
    </div>
</div>

<?php 
// Lấy toàn bộ nội dung vừa tạo
$content = ob_get_clean();

// Hiển thị layout (Sử dụng hàm view() giống các file khác để đồng bộ)
view('layouts.AdminLayout', [
    'title' => 'Lịch dẫn tour',
    'pageTitle' => '  ',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Dashboard', 'url' => BASE_URL . 'home'],
        ['label' => 'Lịch dẫn tour', 'active' => true],
    ],
]);
?>