<?php 
// Start output buffering
ob_start(); 
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/create.css">


<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Tạo Booking Mới</h4>
            <div class="text-muted small">Nhập thông tin để tạo phiếu đặt tour mới</div>
        </div>
        <a href="index.php?act=bookings" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <form action="index.php?act=booking-store" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-8">
                
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <span><i class="bi bi-geo-alt-fill text-primary me-2"></i> Thông tin Tour & Thời gian</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Chọn Tour <span class="text-danger">*</span></label>
                                <select name="tour_id" id="select_tour" class="form-select" required>
                                    <option value="">-- Vui lòng chọn Tour --</option>
                                    <?php foreach ($tours as $tour): ?>
                                        <option value="<?= $tour['id'] ?>"><?= $tour['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Ngày khởi hành <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="start_date" class="form-control border-start-0" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="end_date" class="form-control border-start-0" required readonly style="background-color: #f8f9fc;">
                                </div>
                                <small class="text-muted fst-italic ms-1">Tự động tính theo thời lượng tour</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-modern">
    <div class="card-header-modern">
        <span><i class="bi bi-person-vcard-fill text-primary me-2"></i> Khách hàng đại diện (Trưởng đoàn)</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                    <input type="text" name="customer_name" class="form-control" placeholder="Nguyễn Văn A" required>
                </div>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                    <input type="text" name="customer_phone" class="form-control" placeholder="09xxxxxxxx" required>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email / Liên hệ khác</label>
                 <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                    <input type="text" name="customer_email" class="form-control" placeholder="email@example.com">
                </div>
            </div>

            <div class="col-md-6">
                 <label class="form-label">Địa chỉ / Ghi chú khách</label>
                 <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" name="customer_address" class="form-control" placeholder="Hà Nội...">
                </div>
            </div>
        </div>
    </div>
</div>

                <div class="card card-modern">
                    <div class="card-header-modern">
                        <span><i class="bi bi-list-task text-info me-2"></i> Chi tiết nội dung</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Lịch trình chi tiết</label>
                                <textarea name="schedule_detail" id="schedule_detail" class="form-control" rows="8" placeholder="Nội dung sẽ tự động tải khi chọn tour..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dịch vụ bao gồm</label>
                                <textarea name="service_detail" id="service_detail" class="form-control" rows="8" placeholder="Chi tiết dịch vụ..."></textarea>
                            </div>
                            
                            <div class="col-12">
                                <div class="section-divider"></div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Ghi chú chung</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Ghi chú nội bộ cho booking này..."></textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">File đính kèm (Danh sách đoàn, Vé...)</label>
                                <input type="file" name="files[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                                <small class="text-muted">Hỗ trợ: PDF, Word, Excel, Ảnh</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <span><i class="bi bi-calculator text-success me-2"></i> Chi phí & Số lượng</span>
                    </div>
                    <div class="card-body">
                        <div class="price-display-box mb-3">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label m-0">Người lớn</label>
                                    <span id="price_adult_display" class="badge bg-white text-dark border">0 đ</span>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                                    <input type="number" name="number_of_adults" id="num_adults" class="form-control text-center fw-bold" value="1" min="1" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label m-0">Trẻ em</label>
                                    <span id="price_child_display" class="badge bg-white text-dark border">0 đ</span>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-emoji-smile"></i></span>
                                    <input type="number" name="number_of_children" id="num_children" class="form-control text-center fw-bold" value="0" min="0">
                                </div>
                            </div>
                            
                            <hr class="my-3">

                            <div class="mb-0">
                                <label class="form-label text-success text-uppercase" style="font-size: 0.8rem;">Tổng thành tiền (VND)</label>
                                <input type="number" name="total_price" id="total_price" class="form-control total-price-input" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-modern">
                    <div class="card-header-modern">
                        <span><i class="bi bi-person-badge text-warning me-2"></i> Điều hành</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Phân công HDV</label>
                            <select name="guide_id" class="form-select">
                                <option value="">-- Hệ thống tự lọc --</option>
                                <?php foreach ($guides as $guide): ?>
                                    <option value="<?= $guide['id'] ?>"><?= $guide['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                        </div>
                        
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-submit py-3 mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i> TẠO BOOKING
                </button>

            </div>
        </div>
    </form>
</div>

<script src="public/js/create.js"></script>

<?php
$content = ob_get_clean();

view('layouts.AdminLayout', [
    'title' => 'Tạo Booking - Quản Lý Tour',
    'pageTitle' => 'Booking',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Quản lý Booking', 'url' => BASE_URL . 'bookings'],
        ['label' => 'Tạo mới', 'active' => true],
    ],
]);
?>