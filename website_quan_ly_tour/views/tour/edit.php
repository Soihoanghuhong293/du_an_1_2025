<?php
// 1. DATA PREPARATION
$t = (isset($tour) && is_array($tour)) ? $tour : [];

if (empty($t)) {
    echo '<div class="alert alert-danger m-4">Không tìm thấy tour cần sửa. <a href="index.php?act=tours">Quay lại</a></div>';
    return;
}

// --- HELPER FUNCTIONS ---

// Lấy giá trị an toàn
function val($data, $key, $default = '') {
    return isset($data[$key]) ? $data[$key] : $default;
}

// Decode JSON an toàn (xử lý cả trường hợp decode 2 lần)
function safeJsonView($json) {
    if (is_array($json)) return $json;
    $decoded = json_decode($json ?? '', true);
    if (is_string($decoded)) {
        $decoded = json_decode($decoded, true);
    }
    return is_array($decoded) ? $decoded : [];
}

/**
 * --- HÀM MỚI: BÓC TÁCH DỮ LIỆU ĐỂ SỬA (QUAN TRỌNG) ---
 * Hàm này dùng logic đệ quy giống file SHOW để lấy text sạch
 * nhưng định dạng thêm dấu gạch đầu dòng để dễ nhìn trong Textarea
 */
function recursiveTextForEdit($data) {
    // 1. Nếu là chuỗi JSON, cố gắng decode sâu đến cùng
    if (is_string($data)) {
        $data = trim($data);
        if ((strpos($data, '{') === 0) || (strpos($data, '[') === 0)) {
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return recursiveTextForEdit($decoded);
            }
        }
        return $data; // Trả về chuỗi thuần
    }

    // 2. Nếu là mảng
    if (is_array($data)) {
        // Ưu tiên key chứa nội dung văn bản
        if (isset($data['text'])) return recursiveTextForEdit($data['text']);
        if (isset($data['description'])) return recursiveTextForEdit($data['description']);

        $lines = [];

        // Trường hợp A: Cấu trúc Lịch trình chuẩn (có 'days')
        if (isset($data['days']) && is_array($data['days'])) {
            foreach ($data['days'] as $i => $day) {
                $lines[] = "Ngày " . ($i + 1) . ":"; // Tiêu đề ngày
                
                // Xử lý các hoạt động trong ngày
                if (!empty($day['activities'])) {
                    // Gọi đệ quy để lấy nội dung activities
                    $actContent = recursiveTextForEdit($day['activities']);
                    
                    // Tách dòng để thêm gạch đầu dòng cho đẹp
                    $actLines = explode("\n", $actContent);
                    foreach ($actLines as $al) {
                        $cleanLine = trim($al);
                        if ($cleanLine) {
                            // Nếu chưa có gạch đầu dòng thì thêm vào
                            if (strpos($cleanLine, '-') !== 0 && strpos($cleanLine, '+') !== 0) {
                                $lines[] = "- " . $cleanLine;
                            } else {
                                $lines[] = $cleanLine;
                            }
                        }
                    }
                }
                $lines[] = ""; // Dòng trống ngăn cách các ngày
            }
            return implode("\n", $lines);
        }

        // Trường hợp B: Mảng danh sách thông thường (activities, policies)
        foreach ($data as $item) {
            $cleanItem = recursiveTextForEdit($item); // Đệ quy lấy text con
            
            // Tách dòng nếu kết quả con trả về nhiều dòng
            $subLines = explode("\n", $cleanItem);
            foreach ($subLines as $sl) {
                $sl = trim($sl);
                if (!empty($sl)) {
                    // Thêm gạch đầu dòng nếu là list item
                    $lines[] = (strpos($sl, '-') === 0) ? $sl : "- " . $sl;
                }
            }
        }
        return implode("\n", $lines);
    }

    return '';
}

// Extract Data from DB Row
$id          = $t['id'];
$name        = val($t, 'name');
$categoryId  = val($t, 'category_id');
$price       = val($t, 'price', 0);
$desc        = val($t, 'description');
$status      = val($t, 'status', 1);
$duration    = val($t, 'duration_days', 1);

// JSON Fields (Decode to Arrays for Logic)
$rawSchedule = val($t, 'schedule');
$rawPolicies = val($t, 'policies');
$images      = safeJsonView(val($t, 'images'));
$prices      = safeJsonView(val($t, 'prices'));   
$suppliers   = safeJsonView(val($t, 'suppliers'));

// --- FORM VALUES ---

// 1. SỬ DỤNG HÀM MỚI recursiveTextForEdit ĐỂ HIỂN THỊ
$vScheduleText = $_POST['schedule_text'] ?? recursiveTextForEdit($rawSchedule);
$vPolicyText   = $_POST['policy_text'] ?? recursiveTextForEdit($rawPolicies);

// 2. Các trường cơ bản
$vName       = $_POST['name'] ?? $name;
$vCat        = $_POST['category_id'] ?? $categoryId;
$vPrice      = $_POST['price'] ?? $price;
$vDesc       = $_POST['description'] ?? $desc;
$vDuration   = $_POST['duration_days'] ?? $duration;
$vStatus     = $_POST['status'] ?? $status;

// 3. Suppliers: Array to Comma-separated string
$supplierString = '';
if (is_array($suppliers)) {
    $supplierString = implode(', ', $suppliers);
} elseif (is_string($suppliers)) {
    $supplierString = $suppliers;
}
$vSuppliers  = $_POST['suppliers_text'] ?? $supplierString;

// 4. Prices
$vPriceAdult = $_POST['prices']['adult'] ?? ($prices['adult'] ?? $price);
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
                                    <textarea class="form-control" name="schedule_text" rows="12" style="font-family: monospace; font-size: 14px; line-height: 1.6;" placeholder="Ngày 1: Đón khách...&#10;- Hoạt động A&#10;- Hoạt động B&#10;&#10;Ngày 2: ..."><?= htmlspecialchars($vScheduleText) ?></textarea>
                                    <small class="text-muted"><i class="bi bi-info-circle"></i> Nhập từng dòng. Hệ thống sẽ tự động chuyển đổi thành dữ liệu cấu trúc khi lưu.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Chính sách & Điều khoản</label>
                                    <textarea class="form-control" name="policy_text" rows="6" placeholder="- Chính sách hoàn hủy...&#10;- Bao gồm/Không bao gồm..."><?= htmlspecialchars($vPolicyText) ?></textarea>
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
                                            <?php foreach ($images as $key => $img): 
                                                // Xử lý hiển thị đường dẫn ảnh
                                                $imgSrc = $img;
                                                if (strpos($img, 'http') !== 0) {
                                                    // Nếu không có http, giả định là file local
                                                    if (strpos($img, 'uploads/') !== false) {
                                                        $imgSrc = 'public/' . $img;
                                                    } else {
                                                        $imgSrc = 'public/uploads/tours/' . $img;
                                                    }
                                                    if (defined('BASE_URL')) $imgSrc = BASE_URL . $imgSrc;
                                                }
                                            ?>
                                                <div class="position-relative" style="width: 80px; height: 80px;">
                                                    
                                                    <input type="hidden" name="current_images[]" value="<?= htmlspecialchars($img) ?>">
                                                    
                                                    <img src="<?= $imgSrc ?>" 
                                                         class="rounded border w-100 h-100" 
                                                         style="object-fit: cover;" 
                                                         onerror="this.src='public/assets/img/no-image.png'">

                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0 d-flex justify-content-center align-items-center shadow-sm"
                                                            style="width: 22px; height: 22px; border-radius: 50%; transform: translate(30%, -30%);"
                                                            onclick="this.parentElement.remove()"
                                                            title="Xóa ảnh này"> 
                                                            <i class="bi bi-x" style="font-size: 16px;"></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <small class="text-muted fst-italic mt-1">* Nhấn vào dấu X đỏ để xóa ảnh cũ.</small>
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