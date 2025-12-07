<!-- xử lí hiển thị lịch trình  -->
<?php

function formatTextContent($text) {
    if (empty($text)) return '';
    
    $decoded = json_decode($text);
    
    if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) {
        return $decoded;
    }
    
    return $text;
}
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/show.css">

<div class="d-flex justify-content-between align-items-center mb-4 pt-3">
    <div>
        <h4 class="mb-1 fw-bold text-dark"><?= htmlspecialchars($booking['tour_name']) ?></h4>
        <div class="text-muted d-flex align-items-center gap-2">
            <span><i class="bi bi-hash"></i> ID: <?= $booking['id'] ?></span>
            <span class="badge rounded-pill bg-warning text-dark border border-warning">
                <?= htmlspecialchars($booking['status_name']) ?>
            </span>
        </div>
    </div>
    <div class="d-flex gap-2 align-items-center d-print-none">
        <a href="index.php?act=bookings" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
        <a href="#" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil-square"></i> Cập nhật Booking
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        
        <div class="card card-modern d-print-none">
            <div class="card-header border-0 pb-0">
                <ul class="nav nav-tabs nav-tabs-modern card-header-tabs" id="bookingTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#schedule">Lịch trình</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#service">Dịch vụ</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#diary">Nhật ký</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#files">Files đính kèm</button></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content pt-2">
                    <div class="tab-pane fade show active" id="schedule">
    <div class="card-body p-0 pt-3">
        <form action="index.php?act=booking-update-schedule" method="POST">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Chi tiết lịch trình:</label>
                <textarea name="schedule_content" class="form-control" rows="12" placeholder="Nhập chi tiết lịch trình..."><?= htmlspecialchars(formatTextContent($booking['schedule_detail'] ?? '')) ?></textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu Lịch Trình
                </button>
            </div>
        </form>
    </div>
</div>
                    <div class="tab-pane fade" id="service">
    <div class="card-body p-0 pt-3">
        <form action="index.php?act=booking-update-service" method="POST">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Chi tiết dịch vụ:</label>
                <textarea name="service_content" class="form-control" rows="10" placeholder="Nhập chi tiết dịch vụ..."><?= htmlspecialchars(formatTextContent($booking['service_detail'] ?? '')) ?></textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu Dịch Vụ
                </button>
            </div>
        </form>
    </div>
</div>                    
                          <div class="tab-pane fade" id="diary">
    <div class="card-body p-0 pt-3">
        <form action="index.php?act=booking-update-diary" method="POST">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Nội dung nhật ký tour:</label>
                <textarea name="diary_content" class="form-control" rows="10" placeholder="Nhập nhật ký tour, ghi chú hành trình..."><?= htmlspecialchars(formatTextContent($booking['diary'] ?? '')) ?></textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu Nhật Ký
                </button>
            </div>
        </form>
    </div>
</div>
                    <div class="tab-pane fade" id="files">
                        <?php 
                            $files = json_decode($booking['lists_file'] ?? '[]', true); 
                            $uploadPath = 'uploads/bookings/'; 
                        ?>
                        
                        <?php if (!empty($files) && is_array($files)): ?>
                            <div class="row g-3">
                                <?php foreach($files as $file): 
                                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    $fullPath = $uploadPath . $file;
                                    
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
                                    <div class="col-md-6 col-lg-4">
                                        <div class="file-card p-2 d-flex align-items-center">
                                            <?php if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                                <div class="file-icon-box me-3">
                                                    <img src="<?= $fullPath ?>" alt="img" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                </div>
                                            <?php else: ?>
                                                <div class="file-icon-box <?= $iconColor ?> me-3">
                                                    <i class="bi <?= $iconClass ?>"></i>
                                                </div>
                                            <?php endif; ?>

                                            <div class="flex-grow-1 text-truncate" style="max-width: 150px;">
                                                <div class="fw-medium text-dark text-truncate" title="<?= htmlspecialchars($file) ?>">
                                                    <?= htmlspecialchars($file) ?>
                                                </div>
                                                <small class="text-muted text-uppercase" style="font-size: 10px;"><?= $ext ?></small>
                                            </div>

                                            <a href="<?= $fullPath ?>" target="_blank" class="btn btn-sm btn-light border ms-2" title="Tải xuống / Xem">
                                                <i class="bi bi-download text-secondary"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-folder2-open text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2">Chưa có tài liệu đính kèm nào.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-modern" id="printable-guest-list">
            <div class="card-header-modern">
                <span><i class="bi bi-people-fill text-primary me-2"></i> Danh sách Khách & Phân phòng</span>
                <button type="button" class="btn btn-light text-primary btn-sm fw-bold d-print-none" data-bs-toggle="modal" data-bs-target="#addGuestModal">
                    <i class="bi bi-plus-lg"></i> Thêm khách
                </button>
            </div>
            
            <div class="card-body">
                <div class="d-none d-print-block mb-4 text-center">
                    <h3>DANH SÁCH ĐOÀN: <?= mb_strtoupper($booking['tour_name']) ?></h3>
                    <p class="mb-1">Ngày đi: <strong><?= date('d/m/Y', strtotime($booking['start_date'])) ?></strong> | Ngày về: <strong><?= date('d/m/Y', strtotime($booking['end_date'])) ?></strong></p>
                    <p>HDV: <?= htmlspecialchars($booking['guide_name'] ?? 'Chưa phân công') ?></p>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3 bg-white p-3 rounded shadow-sm border d-print-none">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                            <i class="bi bi-qr-code-scan fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Tiến độ Check-in</h6>
                            <small class="text-muted">Đã có mặt: <strong class="text-success" id="checkin-count">0</strong> / <?= count($guests) ?> khách</small>
                        </div>
                    </div>
                    <div class="progress flex-grow-1 mx-3" style="height: 10px; max-width: 200px;">
                        <div class="progress-bar bg-success" id="checkin-progress" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <form action="index.php?act=guest-update-rooms" method="POST">
                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th class="border-top-0 ps-3">#</th>
                                    <th class="border-top-0 text-center" style="width: 100px;">Check-in</th>
                                    <th class="border-top-0">Họ và Tên</th>
                                    <th class="border-top-0">Thông tin</th>
                                    <th class="border-top-0" style="width: 20%">Phòng</th>
                                    <th class="border-top-0">Ghi chú</th>
                                    <th class="border-top-0 text-end d-print-none">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($guests)): ?>
                                    <?php foreach ($guests as $index => $guest): ?>
                                        <tr class="<?= ($guest['is_checkin'] ?? 0) == 1 ? 'table-success' : '' ?>">
                                            <td class="ps-3 text-muted"><?= $index + 1 ?></td>
                                            
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input checkin-toggle" type="checkbox" role="switch" 
                                                        id="guest_<?= $guest['id'] ?>" 
                                                        data-id="<?= $guest['id'] ?>" 
                                                        <?= (($guest['is_checkin'] ?? 0) == 1) ? 'checked' : '' ?>
                                                        style="cursor: pointer; transform: scale(1.3);">
                                                </div>
                                                <small class="text-muted d-block mt-1 checkin-time" style="font-size: 10px;">
                                                    <?= (($guest['is_checkin'] ?? 0) == 1 && ($guest['checkin_at'] ?? null)) ? date('H:i d/m', strtotime($guest['checkin_at'])) : '' ?>
                                                </small>
                                            </td>

                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($guest['full_name']) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($guest['gender']) ?></small>
                                            </td>
                                            <td>
                                                <small class="d-block text-muted">NS: <span class="text-dark fw-medium"><?= $guest['birthdate'] ? date('d/m/Y', strtotime($guest['birthdate'])) : '-' ?></span></small>
                                                <small class="d-block text-muted">SĐT: <span class="text-dark fw-medium"><?= $guest['phone'] ?? '-' ?></span></small>
                                            </td>
                                            <td>
                                                <span class="d-none d-print-block fw-bold"><?= htmlspecialchars($guest['room_name']) ?></span>
                                                <input type="text" class="form-control form-control-sm d-print-none bg-light border-0" 
                                                       name="rooms[<?= $guest['id'] ?>]" 
                                                       value="<?= htmlspecialchars($guest['room_name']) ?>" 
                                                       placeholder="Số phòng...">
                                            </td>
                                            <td class="text-muted small fst-italic"><?= htmlspecialchars($guest['note']) ?></td>
                                            <td class="text-end d-print-none">
                                                <a href="index.php?act=guest-delete&guest_id=<?= $guest['id'] ?>&booking_id=<?= $booking['id'] ?>" 
                                                   class="btn btn-link text-danger p-0"
                                                   onclick="return confirm('Bạn chắc chắn muốn xóa khách này?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center py-4 text-muted fst-italic">Chưa có khách nào trong đoàn này.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (!empty($guests)): ?>
                    <div class="mt-4 d-flex justify-content-end gap-2 d-print-none border-top pt-3">
                        <button type="button" class="btn btn-light border fw-bold text-secondary" onclick="printGuestList()">
                            <i class="bi bi-printer-fill"></i> In Danh Sách
                        </button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="bi bi-save"></i> Lưu Sơ Đồ Phòng
                        </button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        
        <div class="card card-modern">
            <div class="card-header-modern">
                <span><i class="bi bi-info-circle text-info me-2"></i> Thông tin chung</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="info-label">Ngày đi</div>
                        <div class="info-value"><i class="bi bi-calendar-event me-1"></i> <?= date('d/m/Y', strtotime($booking['start_date'])) ?></div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Ngày về</div>
                        <div class="info-value"><i class="bi bi-calendar-check me-1"></i> <?= date('d/m/Y', strtotime($booking['end_date'])) ?></div>
                    </div>

                    <div class="col-12 mt-2 pt-3 border-top">
                        <div class="info-label">Hướng dẫn viên</div>
                        <div class="d-flex align-items-center mb-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2 text-success border border-success border-opacity-25" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 1.05rem;">
                                    <?= htmlspecialchars($booking['guide_name'] ?? 'Chưa phân công') ?>
                                </div>
                                <small class="text-muted">Phụ trách đoàn</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-3 pt-3 border-top">
                        <div class="info-label">Người tạo</div>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle p-2 me-2 text-primary fw-bold border" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                                <?= substr($booking['creator_name'], 0, 1) ?>
                            </div>
                            <div>
                                <div class="fw-bold"><?= htmlspecialchars($booking['creator_name']) ?></div>
                                <small class="text-muted fw-normal"><?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-modern">
            <div class="card-header-modern">
                <span><i class="bi bi-journal-text text-warning me-2"></i> Ghi chú nội bộ</span>
            </div>
            <div class="card-body">
                <div class="bg-light p-3 rounded border-start border-4 border-warning text-dark">
                    <?= nl2br(htmlspecialchars($booking['notes'] ?? 'Không có ghi chú')) ?>
                </div>
            </div>
        </div>

        <div class="card card-modern">
            <div class="card-header-modern">
                <span><i class="bi bi-clock-history text-secondary me-2"></i> Lịch sử xử lý</span>
            </div>
            <div class="card-body">
                <div class="ms-1">
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <div class="timeline-item">
                                <small class="text-muted d-block mb-1"><?= date('d/m H:i', strtotime($log['changed_at'])) ?></small>
                                <div class="fw-bold text-dark text-sm"><?= htmlspecialchars($log['changer_name']) ?></div>
                                <small>
                                    Đã đổi: <?= htmlspecialchars($log['old_status_name']) ?> <i class="bi bi-arrow-right mx-1"></i> <strong><?= htmlspecialchars($log['new_status_name']) ?></strong>
                                </small>
                                <?php if($log['note']): ?>
                                    <div class="mt-1 text-muted fst-italic small bg-light p-1 rounded">"<?= htmlspecialchars($log['note']) ?>"</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted small">Chưa có lịch sử.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addGuestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="index.php?act=guest-add" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Thêm thành viên đoàn</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold text-uppercase">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="full_name" required placeholder="Nhập tên khách...">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary small fw-bold text-uppercase">Giới tính</label>
                            <select class="form-select" name="gender">
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary small fw-bold text-uppercase">Ngày sinh</label>
                            <input type="date" class="form-control" name="birthdate">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold text-uppercase">Số điện thoại</label>
                        <input type="text" class="form-control" name="phone" placeholder="09xxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold text-uppercase">Ghi chú</label>
                        <textarea class="form-control" name="note" rows="2" placeholder="Ăn chay, dị ứng, người già..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Thêm khách</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const TOTAL_GUESTS = <?= count($guests) ?>;
</script>
<script src="public/js/show.js"></script>