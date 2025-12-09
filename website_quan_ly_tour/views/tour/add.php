<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/index.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/show.css">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Thêm Tour Mới</h3>
        <p class="text-muted">Nhập đầy đủ thông tin tour trước khi lưu</p>
    </div>
    <a href="index.php?act=tours" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<form role="form" method="POST" action="index.php?act=tour-add" enctype="multipart/form-data">

    <div class="card card-modern">
        <div class="card-header-modern">
            <ul class="nav nav-tabs nav-tabs-modern card-header-tabs">
            <li class="nav-item">
                <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#general">Thông tin chung</button>
            </li>

            <li class="nav-item">
                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#schedule">Lịch trình</button>
            </li>

            <li class="nav-item">
                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#policy">Chính sách</button>
            </li>

            <li class="nav-item">
                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#images">Hình ảnh</button>
            </li>

            <li class="nav-item">
                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#price">Giá</button>
            </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content pt-2">

                <!--  TAB 1 : GENERAL  -->
                <div class="tab-pane fade show active" id="general">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Tour (*)</label>
                        <input type="text" name="ten_tour" class="form-control"
                            value="<?= htmlspecialchars($_POST['ten_tour'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Danh mục (*)</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"
                                    <?= (($_POST['category_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                                    <?= $c['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Giá (VNĐ) (*)</label>
                        <input type="number" name="gia" min="0" class="form-control"
                            value="<?= htmlspecialchars($_POST['gia'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả ngắn</label>
                        <textarea name="mo_ta" rows="4" class="form-control"><?= htmlspecialchars($_POST['mo_ta'] ?? '') ?></textarea>
                    </div>

                </div>

                <!--  TAB 2: LỊCH TRÌNH -->
                <div class="tab-pane fade" id="schedule">
                    <label class="form-label fw-bold">Lịch trình (JSON)</label>
                    <textarea name="lich_trinh" rows="12" class="form-control json-editor"><?= htmlspecialchars($_POST['lich_trinh'] ?? '') ?></textarea>
                    <small class="text-muted">Ví dụ: [{"day":1,"title":"Hà Nội - Sapa"}]</small>
                </div>

                <!-- TAB 3: CHÍNH SÁCH -->
                <div class="tab-pane fade" id="policy">
                    <label class="form-label fw-bold">Chính sách (JSON)</label>
                    <textarea name="chinh_sach" rows="12" class="form-control json-editor"><?= htmlspecialchars($_POST['chinh_sach'] ?? '') ?></textarea>
                </div>

                <!-- TAB 4 — HÌNH ẢNH -->
                <div class="tab-pane fade" id="images">
                    <label class="form-label fw-bold">Chọn ảnh Tour</label>
                    <input type="file" name="hinh_anh[]" class="form-control" accept="image/*" multiple>

                    <div class="mt-3" id="preview" style="display:flex; gap:10px; flex-wrap:wrap;"></div>
                    <small class="text-muted">Hỗ trợ nhiều ảnh • Tự động preview</small>
                </div>

                <!-- TAB 5 — GIÁ CHI TIẾT -->
                <div class="tab-pane fade" id="price">
                    <label class="form-label fw-bold">Giá chi tiết (JSON)</label>
                    <textarea name="gia_chi_tiet" rows="10" class="form-control json-editor">
                        <?= htmlspecialchars($_POST['gia_chi_tiet'] ?? '') ?>
                    </textarea>
                    <small class="text-muted">Ví dụ: {"người_lớn":3500000,"trẻ_em":2800000}</small>
                </div>

            </div>
        </div>
    </div>

    <div class="text-end mt-4">
        <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">
            <i class="bi bi-save"></i> Lưu Tour
        </button>
    </div>

</form>


<script>
// Preview hình ảnh
document.querySelector('input[name="hinh_anh[]"]').addEventListener('change', function(e) {
    const preview = document.getElementById("preview");
    preview.innerHTML = "";

    [...e.target.files].forEach(file => {
        const reader = new FileReader();
        reader.onload = ev => {
            const img = document.createElement("img");
            img.src = ev.target.result;
            img.style.width = "120px";
            img.className = "rounded shadow-sm";
            preview.appendChild(img);
        }
        reader.readAsDataURL(file);
    });
});

// Tự động format JSON input
document.querySelectorAll(".json-editor").forEach(textarea => {
    textarea.addEventListener("blur", () => {
        try {
            let json = textarea.value.trim();
            if (json) {
                textarea.value = JSON.stringify(JSON.parse(json), null, 4);
                textarea.style.borderColor = "#198754";
            }
        } catch {
            textarea.style.borderColor = "#dc3545";
        }
    });
});
</script>
