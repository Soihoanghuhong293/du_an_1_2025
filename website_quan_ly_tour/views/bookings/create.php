<?php 
// Start output buffering
ob_start(); 
?>

<style>
    /* Card Styles */
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        background: #fff;
        margin-bottom: 24px;
        transition: transform 0.2s;
    }
    
    .card-header-modern {
        background: #fff;
        padding: 15px 20px;
        border-bottom: 1px solid #edf2f9;
        border-radius: 12px 12px 0 0;
        font-weight: 700;
        color: #2c3e50;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Form Styles */
    .form-label {
        font-weight: 600;
        color: #555;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 10px 12px;
        font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
    }

    /* Section Separators */
    .section-divider {
        border-top: 1px dashed #e3e6f0;
        margin: 20px 0;
    }

    /* Price Section Specifics */
    .price-display-box {
        background: #f8f9fc;
        border-radius: 10px;
        padding: 15px;
        border: 1px solid #e3e6f0;
    }

    .total-price-input {
        background-color: #e8f5e9 !important; /* Light green background */
        color: #198754;
        font-weight: 800;
        font-size: 1.25rem;
        text-align: right;
        border: 2px solid #198754;
    }

    .btn-submit {
        padding: 12px 20px;
        font-weight: 700;
        font-size: 1rem;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: all 0.15s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
    }
</style>

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
                            <div class="mt-2 small text-muted d-flex align-items-start">
                                <i class="bi bi-info-circle me-1 mt-1"></i>
                                <span>Hệ thống sẽ tự động ẩn các HDV bị trùng lịch sau khi bạn chọn ngày.</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                             <label class="form-label">Trạng thái ban đầu</label>
                             <select name="status" class="form-select bg-light">
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= $status['id'] ?>" <?= $status['id'] == 1 ? 'selected' : '' ?>><?= $status['name'] ?></option>
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