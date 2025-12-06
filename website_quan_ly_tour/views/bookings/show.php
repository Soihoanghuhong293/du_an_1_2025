<div class="row">
    <div class="col-md-8">
        
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> Thông tin chung</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tour:</strong> <?= htmlspecialchars($booking['tour_name']) ?></p>
                        <p><strong>Ngày đi:</strong> <?= date('d/m/Y', strtotime($booking['start_date'])) ?></p>
                        <p><strong>Ngày về:</strong> <?= date('d/m/Y', strtotime($booking['end_date'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Người tạo:</strong> <?= htmlspecialchars($booking['creator_name']) ?></p>
                        <p><strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></p>
                        <p><strong>Trạng thái:</strong> 
                            <span class="badge bg-warning text-dark"><?= htmlspecialchars($booking['status_name']) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs card-header-tabs m-0" id="bookingTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active py-3 text-dark fw-bold" data-bs-toggle="tab" data-bs-target="#schedule"><i class="bi bi-map"></i> Lịch trình</button></li>
                    <li class="nav-item"><button class="nav-link py-3 text-dark fw-bold" data-bs-toggle="tab" data-bs-target="#service"><i class="bi bi-bus-front"></i> Dịch vụ</button></li>
                    <li class="nav-item"><button class="nav-link py-3 text-dark fw-bold" data-bs-toggle="tab" data-bs-target="#diary"><i class="bi bi-journal-text"></i> Nhật ký</button></li>
                    <li class="nav-item"><button class="nav-link py-3 text-dark fw-bold" data-bs-toggle="tab" data-bs-target="#files"><i class="bi bi-paperclip"></i> Files</button></li>
                </ul>
            </div>
            <div class="card-body bg-light">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="schedule">
                        <div class="bg-white p-3 border rounded">
                            <?php $schedule = json_decode($booking['schedule_detail'], true); ?>
                            <?php if (json_last_error() === JSON_ERROR_NONE && is_array($schedule)): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($schedule as $key => $val): ?>
                                        <li class="list-group-item"><strong><?= ucfirst($key) ?>:</strong> <?= htmlspecialchars(is_array($val) ? json_encode($val) : $val) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <?= nl2br(htmlspecialchars($booking['schedule_detail'] ?? 'Chưa cập nhật lịch trình.')) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="service">
                        <?= nl2br(htmlspecialchars($booking['service_detail'] ?? 'Chưa cập nhật dịch vụ.')) ?>
                     </div>
                     <div class="tab-pane fade" id="diary">
                        <?= nl2br(htmlspecialchars($booking['diary'] ?? 'Chưa có nhật ký.')) ?>
                     </div>
                     <div class="tab-pane fade" id="files">
                        <?= nl2br(htmlspecialchars($booking['lists_file'] ?? 'Không có file.')) ?>
                     </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
             <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text"></i> Ghi chú</h5>
            </div>
            <div class="card-body">
                 <div class="alert alert-light border">
                    <?= nl2br(htmlspecialchars($booking['notes'] ?? 'Không có ghi chú')) ?>
                </div>
            </div>
        </div>

        <div class="card mb-4" id="printable-guest-list">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-people-fill"></i> Danh sách Khách & Phân phòng</h5>
                
                <button type="button" class="btn btn-light btn-sm text-success fw-bold d-print-none" data-bs-toggle="modal" data-bs-target="#addGuestModal">
                    <i class="bi bi-plus-circle"></i> Thêm khách
                </button>
            </div>
            
            <div class="card-body">
                <div class="d-none d-print-block mb-3">
                    <h3>Danh sách đoàn: <?= htmlspecialchars($booking['tour_name']) ?></h3>
                    <p>Ngày đi: <?= date('d/m/Y', strtotime($booking['start_date'])) ?> | HDV: <?= htmlspecialchars($booking['guide_name'] ?? 'Chưa phân công') ?></p>
                </div>

                <form action="index.php?act=guest-update-rooms" method="POST">
                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">STT</th>
                                    <th>Họ và Tên</th>
                                    <th>Thông tin</th>
                                    <th style="width: 25%">Phân Phòng</th>
                                    <th>Ghi chú</th>
                                    <th style="width: 10%" class="d-print-none">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($guests)): ?>
                                    <?php foreach ($guests as $index => $guest): ?>
                                        <tr>
                                            <td class="text-center"><?= $index + 1 ?></td>
                                            <td class="fw-bold">
                                                <?= htmlspecialchars($guest['full_name']) ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($guest['gender']) ?></small>
                                            </td>
                                            <td>
                                                <small>
                                                    NS: <?= $guest['birthdate'] ? date('d/m/Y', strtotime($guest['birthdate'])) : '-' ?><br>
                                                    SĐT: <?= $guest['phone'] ?? '-' ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="d-none d-print-block"><?= htmlspecialchars($guest['room_name']) ?></span>
                                                <input type="text" class="form-control form-control-sm d-print-none" 
                                                       name="rooms[<?= $guest['id'] ?>]" 
                                                       value="<?= htmlspecialchars($guest['room_name']) ?>" 
                                                       placeholder="Số phòng...">
                                            </td>
                                            <td><?= htmlspecialchars($guest['note']) ?></td>
                                            <td class="text-center d-print-none">
                                                <a href="index.php?act=guest-delete&guest_id=<?= $guest['id'] ?>&booking_id=<?= $booking['id'] ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Xóa khách này?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center text-muted">Chưa có khách nào.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (!empty($guests)): ?>
                    <div class="text-end d-print-none">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Lưu sơ đồ phòng
                        </button>
                        <button type="button" class="btn btn-primary" onclick="printGuestList()">
                        <i class="bi bi-printer-fill"></i> In Danh Sách Khách
                    </button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

   <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Lịch sử xử lý</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Người xử lý</th>
                            <th>Nội dung</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td>
                                        <small><?= date('d/m H:i', strtotime($log['changed_at'])) ?></small>
                                    </td>
                                    <td><small><?= htmlspecialchars($log['changer_name']) ?></small></td>
                                    <td>
                                        <small>
                                            <?= htmlspecialchars($log['old_status_name']) ?> 
                                            <i class="bi bi-arrow-right"></i> 
                                            <strong><?= htmlspecialchars($log['new_status_name']) ?></strong>
                                            <br>
                                            <span class="text-muted fst-italic">"<?= htmlspecialchars($log['note']) ?>"</span>
                                        </small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center">Chưa có lịch sử.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-3 text-center">
            <a href="index.php?act=bookings" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <a href="#" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Cập nhật Booking
            </a>
        </div>
    </div>
</div>

<div class="modal fade" id="addGuestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?act=guest-add" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm thành viên đoàn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select class="form-select" name="gender">
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" name="birthdate">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú (Ăn chay, dị ứng...)</label>
                        <textarea class="form-control" name="note" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm khách</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function printGuestList() {
    // 1. Lấy nội dung phần danh sách khách
    var printContents = document.getElementById('printable-guest-list').innerHTML;
    
    // 2. Lưu nội dung trang web hiện tại
    var originalContents = document.body.innerHTML;

    // 3. Thay thế nội dung trang web bằng nội dung cần in
    document.body.innerHTML = printContents;

    // 4. Gọi lệnh in
    window.print();

    // 5. Khôi phục lại trang web như cũ sau khi in xong
    document.body.innerHTML = originalContents;
    
    // 6. Reload lại trang để đảm bảo các event JS (như modal) hoạt động lại bình thường
    location.reload(); 
}
</script>

<style>
@media print {
    /* Ẩn các nút bấm khi in */
    .d-print-none { display: none !important; }
    /* Hiển thị các phần tử chỉ dành cho in */
    .d-print-block { display: block !important; }
    /* Form input khi in sẽ mất khung, trông như văn bản */
    .form-control { border: none; padding: 0; }
}
</style>