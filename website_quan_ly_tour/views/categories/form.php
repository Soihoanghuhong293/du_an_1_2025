<form method="POST" class="p-4">

    <div class="mb-3">
        <label class="form-label">Tên danh mục</label>
        <input type="text" name="name" class="form-control"
               value="<?= $category['name'] ?? '' ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control" rows="4"><?= $category['description'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Trạng thái</label>
        <select name="status" class="form-select">
            <option value="1" <?= isset($category) && $category['status'] == 1 ? 'selected' : '' ?>>Hiển thị</option>
            <option value="0" <?= isset($category) && $category['status'] == 0 ? 'selected' : '' ?>>Ẩn</option>
        </select>
    </div>

    <button class="btn btn-primary">Lưu</button>
    <a href="index.php?act=categories" class="btn btn-secondary">Quay lại</a>

</form>
