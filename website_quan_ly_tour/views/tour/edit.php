<?php
// 1. DATA PREPARATION (Controller passes $tour, $categories, $errors)
$t = is_array($tour) ? $tour : [];

if (empty($t)) {
    echo '<div class="alert alert-danger m-4">Không tìm thấy tour cần sửa. <a href="index.php?act=tours">Quay lại</a></div>';
    return;
}

// Helper to safely get value from array
function val($data, $key, $default = '') {
    return isset($data[$key]) ? $data[$key] : $default;
}

// Helper to safely decode JSON for view display
function safeJsonView($json) {
    if (is_array($json)) return $json;
    $decoded = json_decode($json ?? '', true);
    return is_array($decoded) ? $decoded : [];
}

// Extract Data from DB Row
$id          = $t['id'];
// Basic Fields
$name        = val($t, 'name');
$categoryId  = val($t, 'category_id');
$price       = val($t, 'price', 0);
$desc        = val($t, 'description');
$status      = val($t, 'status', 1);
$duration    = val($t, 'duration_days', 1);

// JSON Fields (Decode to Arrays)
$images      = safeJsonView(val($t, 'images'));
$schedule    = safeJsonView(val($t, 'schedule')); 
$prices      = safeJsonView(val($t, 'prices'));   
$policies    = safeJsonView(val($t, 'policies')); 
$suppliers   = safeJsonView(val($t, 'suppliers'));

// --- FORM VALUES (Use POST if available (validation error), else DB data) ---
$vName       = $_POST['name'] ?? $name;
$vCat        = $_POST['category_id'] ?? $categoryId;
$vPrice      = $_POST['price'] ?? $price;
$vDesc       = $_POST['description'] ?? $desc;
$vDuration   = $_POST['duration_days'] ?? $duration;
$vStatus     = $_POST['status'] ?? $status;

// Complex Fields -> Convert to String for Textarea/Input
// Schedule: Prefer 'text' key if exists, else raw JSON
$vScheduleText = $_POST['schedule_text'] ?? ($schedule['text'] ?? json_encode($schedule['days'] ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
if($vScheduleText === '[]') $vScheduleText = ''; // Clean empty JSON

// Policies: Prefer 'text' key if exists, else raw JSON
$vPolicyText = $_POST['policy_text'] ?? ($policies['text'] ?? $policies['booking'] ?? '');

// Suppliers: Array to Comma-separated string
$vSuppliers  = $_POST['suppliers_text'] ?? implode(', ', $suppliers);

// Prices: Individual fields
$vPriceAdult = $_POST['prices']['adult'] ?? ($prices['adult'] ?? $price); // Default to base price if empty
$vPriceChild = $_POST['prices']['child'] ?? ($prices['child'] ?? 0);

?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="m-0 fw-bold">Sửa Tour: <span class="text-primary">#<?= $id ?></span></h1>
                <a href="index.php?act=tours" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger shadow-sm mb-4">
                    <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Đã có lỗi xảy ra:</h6>
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $error) echo "<li>$error</li>"; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="index.php?act=tour-edit&id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                <div class="row">
                    
                    <div class="col-lg-8">
                        <div class="card card-primary card-outline shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title fw-bold m-0 text-primary"><i class="bi bi-info-circle me-2"></i>Thông tin chung</h5>
                            </div>
                            <div class="card-body">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tên Tour <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="name" value="<?= htmlspecialchars($vName) ?>" required placeholder="Nhập tên tour...">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-select" required>
                                            <option value="">-- Chọn danh mục --</option>
                                            <?php if (!empty($categories)): foreach ($categories as $cate): ?>
                                                <option value="<?= $cate['id'] ?>" <?= ($vCat == $cate['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cate['name']) ?>
                                                </option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Thời lượng (Ngày)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="duration_days" value="<?= $vDuration ?>" min="1">
                                            <span class="input-group-text">Ngày</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mô tả / Giới thiệu</label>
                                    <textarea class="form-control" name="description" rows="5" placeholder="Mô tả ngắn về tour..."><?= htmlspecialchars($vDesc) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Lịch trình chi tiết</label>
                                    <textarea class="form-control" name="schedule_text" rows="8" placeholder="Ngày 1: ... &#10;Ngày 2: ..."><?= htmlspecialchars($vScheduleText) ?></textarea>
                                    <small class="text-muted"><i class="bi bi-info-circle"></i> Nhập nội dung chi tiết. Hệ thống sẽ tự động lưu.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Chính sách & Điều khoản</label>
                                    <textarea class="form-control" name="policy_text" rows="4" placeholder="Chính sách hoàn hủy, bao gồm/không bao gồm..."><?= htmlspecialchars($vPolicyText) ?></textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        
                        <div class="card card-success card-outline shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title fw-bold m-0 text-success"><i class="bi bi-gear-fill me-2"></i>Cấu hình & Giá</h5>
                            </div>
                            <div class="card-body">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Trạng thái</label>
                                    <select name="status" class="form-select">
                                        <option value="1" <?= $vStatus == 1 ? 'selected' : '' ?>>Hoạt động</option>
                                        <option value="0" <?= $vStatus == 0 ? 'selected' : '' ?>>Tạm ẩn</option>
                                    </select>
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-success">Giá Cơ Bản (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control fw-bold text-success border-success" name="price" value="<?= $vPrice ?>" required min="0">
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Giá Người lớn</label>
                                        <input type="number" class="form-control form-control-sm" name="prices[adult]" value="<?= $vPriceAdult ?>" min="0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Giá Trẻ em</label>
                                        <input type="number" class="form-control form-control-sm" name="prices[child]" value="<?= $vPriceChild ?>" min="0">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card card-info card-outline shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title fw-bold m-0 text-info"><i class="bi bi-images me-2"></i>Hình ảnh & NCC</h5>
                            </div>
                            <div class="card-body">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nhà cung cấp</label>
                                    <input type="text" class="form-control" name="suppliers_text" value="<?= htmlspecialchars($vSuppliers) ?>" placeholder="NCC A, NCC B...">
                                    <small class="text-muted d-block mt-1">Phân tách bằng dấu phẩy (,)</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Thêm ảnh mới</label>
                                    <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                                </div>

                               <?php if (!empty($images)): ?>
    <div class="mb-3">
        <label class="form-label fw-bold small text-uppercase text-muted">Ảnh hiện tại</label>
        <div class="d-flex flex-wrap gap-2 border rounded p-2 bg-white">
            <?php foreach ($images as $img): 
                // Xử lý đường dẫn ảnh (Giống trang list)
                $imgSrc = (strpos($img, 'uploads/') !== false) ? $img : 'public/uploads/tours/' . $img;
            ?>
                <div class="position-relative">
                    <img src="<?= BASE_URL . $imgSrc ?>" 
                         class="rounded border" 
                         style="width: 70px; height: 70px; object-fit: cover;" 
                         onerror="this.src='<?= BASE_URL ?>public/assets/img/no-image.png'">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-warning w-100 py-3 fw-bold text-dark shadow-sm">
                                <i class="bi bi-save-fill me-2"></i> LƯU THAY ĐỔI
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </section>
</div>