<form role="form" method="POST" action="" enctype="multipart/form-data">

    <div class="card-body">
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