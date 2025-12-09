<?php 
$isEdit = isset($tour);
$title = $isEdit ? "Cập nhật Tour" : "Thêm Tour Mới";
$action = $isEdit ? "index.php?act=tour-update&id={$tour['id']}" : "index.php?act=tour-store";
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/index.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/show.css">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><?= $title ?></h3>
        <p class="text-muted"><?= $isEdit ? "Chỉnh sửa thông tin tour" : "Tạo tour mới" ?></p>
    </div>
    <a href="index.php?act=tours" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<form action="<?= $action ?>" method="post" enctype="multipart/form-data">

    <div class="card card-modern">
        <div class="card-header-modern">
            <ul class="nav nav-tabs nav-tabs-modern card-header-tabs">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#general">Thông tin chung</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#images">Hình ảnh</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#schedule">Lịch trình</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#price">Giá chi tiết</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#policy">Chính sách</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#supplier">Nhà cung cấp</button></li>
            </ul>
        </div>

        <div class="card-body">

            <div class="tab-content pt-2">

                <!-- TAB 1 — GENERAL -->
                <div class="tab-pane fade show active" id="general">

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Tên Tour</label>
                            <input type="text" name="name" class="form-control" 
                                value="<?= $tour['name'] ?? '' ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Danh mục</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c['id'] ?>"
                                        <?= isset($tour['category_id']) && $tour['category_id'] == $c['id'] ? 'selected' : '' ?>>
                                        <?= $c['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả ngắn</label>
                        <textarea name="description" rows="3" class="form-control"><?= $tour['description'] ?? '' ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Giá Tour (VNĐ)</label>
                        <input type="number" name="price" class="form-control" 
                               value="<?= $tour['price'] ?? '' ?>" required>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="status" value="1"
                            <?= isset($tour['status']) && $tour['status'] ? 'checked' : '' ?>>
                        <label class="form-check-label">Hiển thị tour</label>
                    </div>
                </div>

                <!-- TAB 2 — IMAGES -->
                <div class="tab-pane fade" id="images">
                    <label class="form-label fw-bold">Upload hình ảnh</label>
                    <input type="file" name="images[]" multiple class="form-control">

                    <div class="mt-3" id="image-preview" style="display:flex; gap:10px; flex-wrap:wrap;"></div>

                    <?php if ($isEdit && !empty($tour['images'])): ?>
                        <hr>
                        <p class="fw-bold">Ảnh hiện tại:</p>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($tour['images'] as $img): ?>
                                <img src="<?= BASE_URL ?>uploads/tours/<?= $img ?>" 
                                    width="120" class="rounded shadow">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- TAB 3 — SCHEDULE -->
                <div class="tab-pane fade" id="schedule">
                    <label class="form-label fw-bold">Lịch trình (JSON)</label>
                    <textarea name="schedule" rows="10" class="form-control json-input"><?= 
                        isset($tour['schedule']) ? json_encode($tour['schedule'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '' 
                    ?></textarea>
                </div>

                <!-- TAB 4 — PRICE -->
                <div class="tab-pane fade" id="price">
                    <label class="form-label fw-bold">Giá chi tiết (JSON)</label>
                    <textarea name="prices" rows="10" class="form-control json-input"><?= 
                        isset($tour['prices']) ? json_encode($tour['prices'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '' 
                    ?></textarea>
                </div>

                <!-- TAB 5 — POLICY -->
                <div class="tab-pane fade" id="policy">
                    <label class="form-label fw-bold">Chính sách (JSON)</label>
                    <textarea name="policies" rows="10" class="form-control json-input"><?= 
                        isset($tour['policies']) ? json_encode($tour['policies'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '' 
                    ?></textarea>
                </div>

                <!-- TAB 6 — SUPPLIER -->
                <div class="tab-pane fade" id="supplier">
                    <label class="form-label fw-bold">Nhà cung cấp (JSON)</label>
                    <textarea name="suppliers" rows="10" class="form-control json-input"><?= 
                        isset($tour['suppliers']) ? json_encode($tour['suppliers'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '' 
                    ?></textarea>
                </div>

            </div>
        </div>
    </div>

    <div class="text-end mt-4">
        <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">
            Lưu Tour
        </button>
    </div>
</form>

<script>
document.querySelector('input[name="images[]"]').addEventListener('change', function(e) {
    const preview = document.getElementById("image-preview");
    preview.innerHTML = "";

    [...e.target.files].forEach(file => {
        let reader = new FileReader();
        reader.onload = event => {
            let img = document.createElement("img");
            img.src = event.target.result;
            img.className = "rounded shadow";
            img.style.width = "120px";
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});
</script>
