<form role="form" method="POST" action="" enctype="multipart/form-data">

    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="mb-3">
            <label class="form-label">Tên Tour (*)</label>
            <input type="text" class="form-control" name="ten_tour" required value="<?= htmlspecialchars($_POST['ten_tour'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Danh Mục Tour (*)</label>
            <select name="category_id" class="form-control" required>
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($categories as $cate): ?>
                    <option value="<?= $cate['id'] ?>" <?= (($_POST['category_id'] ?? '') == $cate['id']) ? 'selected' : '' ?>>
                        <?= $cate['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá (VNĐ) (*)</label>
            <input type="number" class="form-control" name="gia" required min="0" value="<?= htmlspecialchars($_POST['gia'] ?? 0) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Giá chi tiết</label>
            <div class="row g-2">
                <div class="col-sm-6">
                    <label class="form-label small">Người lớn</label>
                    <input type="number" class="form-control" name="prices[adult]" min="0" value="<?= htmlspecialchars($_POST['prices']['adult'] ?? '') ?>">
                </div>
                <div class="col-sm-6">
                    <label class="form-label small">Trẻ em</label>
                    <input type="number" class="form-control" name="prices[child]" min="0" value="<?= htmlspecialchars($_POST['prices']['child'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Nhà cung cấp</label>
            <input type="text" class="form-control" name="suppliers_text" placeholder="Nhập tên nhà cung cấp, phân tách bằng dấu phẩy" value="<?= htmlspecialchars($_POST['suppliers_text'] ?? '') ?>">
            <small class="text-muted">Ví dụ: Công ty A, Công ty B — hoặc bỏ trống nếu muốn chọn theo ID (không hiện tại)</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô Tả</label>
            <textarea class="form-control" name="mo_ta" rows="4"><?= htmlspecialchars($_POST['mo_ta'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Lịch trình</label>
            <textarea class="form-control" name="lich_trinh" rows="4"><?= htmlspecialchars($_POST['lich_trinh'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Chính sách</label>
            <textarea class="form-control" name="chinh_sach" rows="4"><?= htmlspecialchars($_POST['chinh_sach'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh Tour</label>
            <input type="file" class="form-control" name="hinh_anh[]" accept="image/*" multiple>
        </div>
    </div>

    <div class="card-footer d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Thêm Tour
        </button>
        <a href="<?= BASE_URL . 'tour' ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay Lại
        </a>
    </div>
</form>