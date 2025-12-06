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
                        <p><strong>Trạng thái hiện tại:</strong> 
                            <span class="badge bg-warning text-dark"><?= htmlspecialchars($booking['status_name']) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs card-header-tabs m-0" id="bookingTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-3 text-dark fw-bold" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab">
                            <i class="bi bi-map"></i> Lịch trình
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 text-dark fw-bold" id="service-tab" data-bs-toggle="tab" data-bs-target="#service" type="button" role="tab">
                            <i class="bi bi-bus-front"></i> Dịch vụ
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 text-dark fw-bold" id="diary-tab" data-bs-toggle="tab" data-bs-target="#diary" type="button" role="tab">
                            <i class="bi bi-journal-text"></i> Nhật ký
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 text-dark fw-bold" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab">
                            <i class="bi bi-paperclip"></i> Files
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body bg-light">
                <div class="tab-content" id="bookingTabsContent">
                    
                    <div class="tab-pane fade show active" id="schedule" role="tabpanel">
                        <h6 class="text-primary border-bottom pb-2">Chi tiết lịch trình (Schedule Detail)</h6>
                        <div class="bg-white p-3 border rounded">
                            <?php 
                                $schedule = json_decode($booking['schedule_detail'], true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($schedule)): 
                            ?>
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

                    <div class="tab-pane fade" id="service" role="tabpanel">
                         <h6 class="text-success border-bottom pb-2">Chi tiết dịch vụ (Service Detail)</h6>
                         <div class="bg-white p-3 border rounded">
                            <?php 
                                $services = json_decode($booking['service_detail'], true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($services)): 
                            ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <?php foreach ($services as $key => $val): ?>
                                            <tr>
                                                <th width="30%" class="bg-light"><?= ucfirst($key) ?></th>
                                                <td><?= htmlspecialchars(is_array($val) ? json_encode($val) : $val) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                            <?php else: ?>
                                <?= nl2br(htmlspecialchars($booking['service_detail'] ?? 'Chưa cập nhật dịch vụ.')) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="diary" role="tabpanel">
                        <h6 class="text-warning border-bottom pb-2 text-dark">Nhật ký Tour (Diary)</h6>
                        <div class="bg-white p-3 border rounded" style="min-height: 150px;">
                             <?= nl2br(htmlspecialchars($booking['diary'] ?? 'Chưa có nhật ký.')) ?>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="files" role="tabpanel">
                        <h6 class="text-info border-bottom pb-2 text-dark">Danh sách tệp đính kèm</h6>
                        <div class="bg-white p-3 border rounded">
                             <?php if (!empty($booking['lists_file'])): ?>
                                <div class="alert alert-secondary">
                                    <i class="bi bi-file-earmark"></i> 
                                    <?= nl2br(htmlspecialchars($booking['lists_file'])) ?>
                                </div>
                             <?php else: ?>
                                <p class="text-muted fst-italic">Không có tệp đính kèm nào.</p>
                             <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

       

        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text"></i> Ghi chú & Danh sách</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Ghi chú đặc biệt:</strong>
                    <div class="alert alert-light border">
                        <?= nl2br(htmlspecialchars($booking['notes'] ?? 'Không có ghi chú')) ?>
                    </div>
                </div>
                
                <div class="col-12 mt-4">
    <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bi bi-people-fill"></i> Danh sách Khách & Phân phòng</h5>
            
            <button type="button" class="btn btn-light btn-sm text-success fw-bold" data-bs-toggle="modal" data-bs-target="#addGuestModal">
                <i class="bi bi-plus-circle"></i> Thêm khách
            </button>
        </div>
        
        <div class="card-body">
            <form action="index.php?act=guest-update-rooms" method="POST">
                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">STT</th>
                            <th>Họ và Tên</th>
                            <th>Giới tính</th>
                            <th>Ngày sinh/SĐT</th>
                            <th style="width: 25%">Phân Phòng (Nhập tên/số phòng)</th>
                            <th>Ghi chú</th>
                            <th style="width: 10%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($guests)): ?>
                            <?php foreach ($guests as $index => $guest): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($guest['full_name']) ?></td>
                                    <td><?= htmlspecialchars($guest['gender']) ?></td>
                                    <td>
                                        <small>
                                            <i class="bi bi-calendar"></i> <?= $guest['birthdate'] ? date('d/m/Y', strtotime($guest['birthdate'])) : '-' ?><br>
                                            <i class="bi bi-telephone"></i> <?= $guest['phone'] ?? '-' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="rooms[<?= $guest['id'] ?>]" 
                                               value="<?= htmlspecialchars($guest['room_name']) ?>" 
                                               placeholder="Ví dụ: 101, 202...">
                                    </td>
                                    <td><?= htmlspecialchars($guest['note']) ?></td>
                                    <td class="text-center">
                                        <a href="index.php?act=guest-delete&guest_id=<?= $guest['id'] ?>&booking_id=<?= $booking['id'] ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Xóa khách này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center text-muted">Chưa có khách nào trong đoàn.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if (!empty($guests)): ?>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu sơ đồ phòng
                    </button>
                </div>
                <?php endif; ?>
            </form>
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