<?php
// views/guide/show.php

function formatTextContent($text) {
    if (empty($text)) return '';
    $decoded = json_decode($text);
    return (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) ? $decoded : $text;
}
?>

<div class="container-fluid pt-3">
    <div class="card shadow-sm mb-3 border-0 border-start border-5 border-primary">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title fw-bold text-primary mb-1">
                    <?= htmlspecialchars($booking['tour_name']) ?>
                </h4>
                <div class="text-muted">
                    <i class="bi bi-calendar-event me-1"></i> <?= date('d/m/Y', strtotime($booking['start_date'])) ?> 
                    <i class="bi bi-arrow-right mx-2"></i> 
                    <i class="bi bi-calendar-check me-1"></i> <?= date('d/m/Y', strtotime($booking['end_date'])) ?>
                </div>
            </div>
           <div class="d-flex gap-2">
    <?php if ($booking['status'] == 1): ?>
        <button type="button" class="btn btn-success fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#confirmTourModal">
            <i class="bi bi-check-lg"></i> Nhận Tour
        </button>
        <a href="index.php?act=guide-reject&id=<?= $booking['id'] ?>" class="btn btn-outline-danger fw-bold shadow-sm" onclick="return confirm('Bạn từ chối tour này?')">
            <i class="bi bi-x-circle"></i> Từ chối
        </a>

    <?php elseif ($booking['status'] == 2): ?>
       
        <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#finishTourModal">
            <i class="bi bi-flag-fill"></i> Kết thúc Tour
        </button>

    <?php elseif ($booking['status'] == 3): ?>
        <span class="badge bg-secondary fs-6 px-3 py-2">
            <i class="bi bi-check-all"></i> Đã hoàn tất
        </span>
    <?php endif; ?>
    
    <a href="index.php?act=guide-tours" class="btn btn-secondary ms-2">Thoát</a>
</div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#schedule"><i class="bi bi-list-task me-1"></i> Lịch trình</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#guests"><i class="bi bi-people me-1"></i> Danh sách đoàn</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#service"><i class="bi bi-briefcase me-1"></i> Dịch vụ</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#diary"><i class="bi bi-journal-text me-1"></i> Nhật ký</button></li>
                        <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#files">
                           <i class="bi bi-folder2-open me-1"></i> Tài liệu
                           </button>
                         </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade" id="files">
    <?php 
        $files = json_decode($booking['lists_file'] ?? '[]', true); 
        $uploadPath = 'uploads/bookings/'; 
    ?>
    
    <h6 class="fw-bold text-uppercase text-muted mb-3 small">Tài liệu tour & Vé máy bay</h6>

    <?php if (!empty($files) && is_array($files)): ?>
        <div class="row g-3">
            <?php foreach($files as $file): 
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $fullPath = $uploadPath . $file;
                
                // Logic chọn icon
                $iconClass = 'bi-file-earmark';
                $iconColor = 'bg-light text-secondary';
                
                if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $iconClass = 'bi-image';
                    $iconColor = 'bg-primary bg-opacity-10 text-primary';
                } elseif ($ext == 'pdf') {
                    $iconClass = 'bi-file-earmark-pdf';
                    $iconColor = 'bg-danger bg-opacity-10 text-danger';
                } elseif (in_array($ext, ['doc', 'docx'])) {
                    $iconClass = 'bi-file-earmark-word';
                    $iconColor = 'bg-primary bg-opacity-10 text-primary';
                } elseif (in_array($ext, ['xls', 'xlsx'])) {
                    $iconClass = 'bi-file-earmark-excel';
                    $iconColor = 'bg-success bg-opacity-10 text-success';
                }
            ?>
                <div class="col-md-6">
                    <div class="border rounded p-2 d-flex align-items-center bg-white shadow-sm h-100">
                        <?php if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                            <div class="me-3">
                                <img src="<?= $fullPath ?>" alt="img" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 <?= $iconColor ?>" style="width: 40px; height: 40px;">
                                <i class="bi <?= $iconClass ?> fs-5"></i>
                            </div>
                        <?php endif; ?>

                        <div class="flex-grow-1 text-truncate" style="max-width: 150px;">
                            <div class="fw-medium text-dark text-truncate small" title="<?= htmlspecialchars($file) ?>">
                                <?= htmlspecialchars($file) ?>
                            </div>
                            <small class="text-muted text-uppercase" style="font-size: 10px;"><?= $ext ?></small>
                        </div>

                        <a href="<?= $fullPath ?>" target="_blank" class="btn btn-sm btn-light border ms-2" title="Xem / Tải về">
                            <i class="bi bi-download text-secondary"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-folder-x text-muted" style="font-size: 2rem;"></i>
            <p class="text-muted mt-2 small">Không có tài liệu nào được đính kèm.</p>
        </div>
    <?php endif; ?>
</div>
                        <div class="tab-pane fade show active" id="schedule">
                            <div class="bg-light p-3 rounded text-dark" style="white-space: pre-line;">
                                <?= formatTextContent($booking['schedule_detail'] ?? 'Chưa có lịch trình chi tiết.') ?>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="guests">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold m-0 text-primary">
                                    Đã check-in: <span id="checkin-count" class="text-success fw-bold">0</span> / <?= count($guests) ?> khách
                                </h6>
                                <div class="progress" style="width: 200px; height: 10px;">
                                    <div class="progress-bar bg-primary" id="checkin-progress" style="width: 0%"></div>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th class="text-center">Check-in</th>
                                            <th>Họ tên</th>
                                            <th>SĐT</th>
                                            <th>Phòng</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($guests as $i => $g): ?>
                                        <tr class="<?= $g['is_checkin'] ? 'table-success' : '' ?>">
                                            <td><?= $i + 1 ?></td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input checkin-toggle" type="checkbox" role="switch" 
                                                           id="guest_<?= $g['id'] ?>"
                                                           data-id="<?= $g['id'] ?>" 
                                                           <?= $g['is_checkin'] ? 'checked' : '' ?>
                                                           style="cursor: pointer; transform: scale(1.3);">
                                                </div>
                                                <small class="text-muted d-block mt-1 checkin-time" style="font-size: 10px;">
                                                    <?= ($g['is_checkin'] && !empty($g['checkin_at'])) ? date('H:i d/m', strtotime($g['checkin_at'])) : '' ?>
                                                </small>
                                            </td>
                                            <td class="fw-bold"><?= htmlspecialchars($g['full_name']) ?></td>
                                            <td><?= htmlspecialchars($g['phone']) ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($g['room_name']) ?></span></td>
                                            <td class="small text-muted"><?= htmlspecialchars($g['note']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="service">
                            <h6 class="fw-bold text-uppercase text-muted mb-3">Dịch vụ đã đặt</h6>
                            <ul class="list-group list-group-flush">
                                <?php if(!empty($services)): ?>
                                    <?php foreach($services as $s): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <strong><?= htmlspecialchars($s['provider_name']) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?= date('d/m', strtotime($s['use_date'])) ?> - <?= $s['quantity'] ?> suất
                                            </small>
                                        </div>
                                        <span class="badge bg-light text-dark border"><?= $s['note'] ?></span>
                                    </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-center text-muted">Chưa có dịch vụ nào.</li>
                                <?php endif; ?>
                            </ul>
                            
                            <hr>
                            <h6 class="fw-bold text-uppercase text-muted mt-4 mb-2">Chi tiết Dịch vụ Tour</h6>
                            <div class="bg-light p-3 rounded">
                                <?= nl2br(formatTextContent($booking['service_detail'] ?? '')) ?>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="diary">
                            <form action="index.php?act=guide-diary-save" method="POST">
                                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ghi chép hành trình:</label>
                                    <textarea name="diary_content" class="form-control" rows="10" placeholder="HDV ghi chú tình hình đoàn tại đây..."><?= htmlspecialchars(formatTextContent($booking['diary'] ?? '')) ?></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Cập nhật Nhật ký</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-warning bg-opacity-10 border-warning">
                    <h6 class="m-0 fw-bold text-warning-emphasis"><i class="bi bi-exclamation-triangle me-2"></i>Lưu ý quan trọng</h6>
                </div>
                <div class="card-body bg-warning bg-opacity-10">
                    <?= nl2br(htmlspecialchars($booking['notes'] ?? 'Không có ghi chú đặc biệt.')) ?>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="m-0 fw-bold"><i class="bi bi-person-lines-fill me-2"></i>Liên hệ điều hành</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Điều hành:</strong> <?= $booking['creator_name'] ?></p>
                    <p class="mb-0"><strong>Hotline:</strong> 0987.654.321</p>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmTourModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="index.php?act=guide-confirm" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Xác nhận nhận tour</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                    
                    <p>Bạn có chắc chắn muốn nhận tour <strong><?= htmlspecialchars($booking['tour_name']) ?></strong>?</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ghi chú xác nhận (Log):</label>
                        <textarea name="confirm_note" class="form-control" rows="3" placeholder="Ví dụ: Đã nhận, sẵn sàng khởi hành..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success fw-bold">Xác nhận ngay</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="finishTourModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="index.php?act=guide-finish" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Xác nhận kết thúc Tour</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                    
                    <div class="alert alert-info border-0 d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                        <div>
                            Bạn xác nhận đã hoàn thành tour <strong><?= htmlspecialchars($booking['tour_name']) ?></strong>?
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Báo cáo kết thúc (Log):</label>
                        <textarea name="finish_note" class="form-control" rows="3" placeholder="VD: Tour kết thúc tốt đẹp, khách hài lòng..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold">Xác nhận Hoàn tất</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
    const TOTAL_GUESTS = <?= count($guests) ?>;
</script>
<script src="public/js/show.js"></script>
<?php
// Lấy toàn bộ nội dung vừa tạo từ bộ đệm
$content = ob_get_clean();

view('layouts.AdminLayout', [
    'title' => 'Dashboard - Website Quản Lý Tour',
    'pageTitle' => 'Chi tiết Booking', 
    'content' => $content, 
    'breadcrumb' => [
        ['label' => 'Trang chủ', 'url' => BASE_URL . 'home', 'active' => false],
        ['label' => 'Thống kê', 'url' => BASE_URL . 'index.php?act=dashboard', 'active' => true],
    ],
]);
?>