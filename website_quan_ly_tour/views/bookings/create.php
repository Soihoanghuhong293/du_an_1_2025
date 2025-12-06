<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Tạo Booking</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="bi bi-dash-lg"></i>
          </button>
        </div>
      </div>

      <div class="card-body">
        <form action="index.php?act=booking-store" method="POST">
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Chọn Tour <span class="text-danger">*</span></label>
              <select name="tour_id" class="form-control" required>
                <option value="">-- Chọn Tour --</option>
                <?php foreach ($tours as $tour): ?>
                  <option value="<?= $tour['id'] ?>"><?= $tour['name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
              <input type="date" name="start_date" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
              <input type="date" name="end_date" class="form-control" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Phân công HDV</label>
              <select name="guide_id" class="form-control">
                <option value="">-- Chưa phân công --</option>
                <?php foreach ($guides as $guide): ?>
                  <option value="<?= $guide['id'] ?>"><?= $guide['name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <hr>
          <h5 class="mb-3 text-primary">Thông tin chi tiết (JSON/Text)</h5>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Chi tiết lịch trình (Schedule Detail)</label>
              <textarea name="schedule_detail" class="form-control" rows="4" placeholder="Nhập chi tiết lịch trình..."></textarea>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Chi tiết dịch vụ (Service Detail)</label>
              <textarea name="service_detail" class="form-control" rows="4" placeholder="Nhập chi tiết dịch vụ..."></textarea>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nhật ký tour (Diary)</label>
              <textarea name="diary" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Danh sách File (Lists File)</label>
              <textarea name="lists_file" class="form-control" rows="3" placeholder="Nhập đường dẫn file hoặc ghi chú file..."></textarea>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Ghi chú chung</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
          </div>

          <button type="submit" class="btn btn-primary">Lưu Booking</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
// Lấy toàn bộ nội dung vừa tạo
$content = ob_get_clean();

// Hiển thị layout Admin
view('layouts.AdminLayout', [
    'title' => 'Danh mục - Website Quản Lý Tour',
    'pageTitle' => 'Booking',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Danh mục', 'url' => BASE_URL . 'home', 'active' => true],
    ],
]);
?>
