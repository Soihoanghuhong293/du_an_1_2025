<?php
// 1. DATA PREPARATION
$t = (isset($tour) && is_array($tour)) ? $tour : [];

if (empty($t)) {
    echo '<div class="alert alert-danger m-4">Không tìm thấy thông tin tour. <a href="index.php?act=tours">Quay lại</a></div>';
    return;
}

// --- HELPER FUNCTION: "BÓC TÁCH" DỮ LIỆU ĐỆ QUY (QUAN TRỌNG) ---
// Hàm này sẽ đào sâu vào mảng/json để tìm text cuối cùng
// --- HELPER FUNCTION: BÓC TÁCH DỮ LIỆU THÔNG MINH ---
function recursiveClean($data) {
    // 1. Nếu là chuỗi, thử decode
    if (is_string($data)) {
        $data = trim($data);
        if ((strpos($data, '{') === 0) || (strpos($data, '[') === 0)) {
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return recursiveClean($decoded);
            }
        }
        return $data;
    }

    // 2. Nếu là mảng
    if (is_array($data)) {
        // Ưu tiên các key chứa nội dung văn bản cụ thể
        if (isset($data['text'])) return recursiveClean($data['text']);
        if (isset($data['booking'])) return recursiveClean($data['booking']);
        if (isset($data['description'])) return recursiveClean($data['description']);
        
        // Nếu là mảng danh sách (activities, days...), nối chúng lại
        $lines = [];
        foreach ($data as $item) {
            $cleanItem = recursiveClean($item);
            if (!empty($cleanItem)) {
                $lines[] = $cleanItem;
            }
        }
        // Trả về chuỗi xuống dòng
        return implode("\n", $lines);
    }

    return '';
}

// Helper cơ bản để view ảnh/giá
function safeJsonView($json) {
    if (is_array($json)) return $json;
    $decoded = json_decode($json ?? '', true);
    return is_array($decoded) ? $decoded : [];
}

// Extract Data
$id          = $t['id'];
$name        = htmlspecialchars($t['name'] ?? 'Chưa đặt tên');
$desc        = nl2br(htmlspecialchars($t['description'] ?? ''));
$status      = $t['status'] ?? 0;
$createdAt   = $t['created_at'] ?? '';
$basePrice   = $t['price'] ?? 0;
$duration    = $t['duration_days'] ?? 1;

// Lấy dữ liệu thô từ Model
$rawImages   = $t['images'] ?? [];
$rawSchedule = $t['schedule'] ?? [];
$rawPrices   = $t['prices'] ?? [];
$rawPolicies = $t['policies'] ?? [];
$rawSuppliers= $t['suppliers'] ?? [];

// Xử lý dữ liệu cho View
$images      = safeJsonView($rawImages);
$schedule    = safeJsonView($rawSchedule);
$prices      = safeJsonView($rawPrices);
$suppliers   = safeJsonView($rawSuppliers);

// --- SỬ DỤNG HÀM RECURSIVE ĐỂ LẤY TEXT SẠCH ---
$scheduleText = recursiveClean($rawSchedule);
$policyText   = recursiveClean($rawPolicies);

// Image Handling (Fix Path)
$firstImage = !empty($images) ? $images[0] : null;
$mainImage = 'public/assets/img/no-image.png';

if ($firstImage) {
    if (strpos($firstImage, 'http') === 0) {
        $mainImage = $firstImage;
    } else {
        if (strpos($firstImage, 'uploads/') !== false) {
             $mainImage = 'public/' . $firstImage;
        } else {
             $mainImage = 'public/uploads/tours/' . $firstImage;
        }
    }
    if (defined('BASE_URL') && strpos($mainImage, 'http') !== 0) {
        $mainImage = BASE_URL . $mainImage;
    }
}
?>
<style>
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        background: #fff;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .card-header-modern {
        background: #fff;
        padding: 15px 25px;
        border-bottom: 1px solid #f0f2f5;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 25px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 5px;
        bottom: -25px;
        width: 2px;
        background-color: #e9ecef;
    }
    .timeline-item:last-child::before { display: none; }
    .timeline-dot {
        position: absolute;
        left: -6px;
        top: 5px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: #4e73df;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #4e73df;
    }
    .gallery-thumb {
        cursor: pointer;
        transition: transform 0.2s;
        border-radius: 8px;
        height: 70px;
        width: 100%;
        object-fit: cover;
    }
    .gallery-thumb:hover { transform: scale(1.05); border-color: #4e73df !important; }
    
    /* Soft Badges */
    .badge-soft-success { background-color: #e8f5e9; color: #2e7d32; }
    .badge-soft-secondary { background-color: #f5f5f5; color: #616161; }
    .badge-soft-primary { background-color: #e3f2fd; color: #0d6efd; }
</style>

<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 class="fw-bold text-dark m-0"><?= $name ?></h3>
                <?php if($status == 1): ?>
                    <span class="badge badge-soft-success px-3 py-2 rounded-pill">Đang hoạt động</span>
                <?php else: ?>
                    <span class="badge badge-soft-secondary px-3 py-2 rounded-pill">Tạm ẩn</span>
                <?php endif; ?>
            </div>
            <div class="text-muted small">
                <i class="bi bi-hash"></i> ID: <?= $id ?> &bull; 
                <i class="bi bi-clock"></i> Cập nhật: <?= !empty($createdAt) ? date('d/m/Y H:i', strtotime($createdAt)) : 'N/A' ?>
            </div>
        </div>
        <div class="btn-group">
            <a href="index.php?act=tours" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <a href="index.php?act=tour-edit&id=<?= $id ?>" class="btn btn-warning text-white">
                <i class="bi bi-pencil-square"></i> Sửa
            </a>
            <a href="index.php?act=tour-delete&id=<?= $id ?>" class="btn btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa tour này?')">
                <i class="bi bi-trash"></i> Xóa
            </a>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-8">
            
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="rounded overflow-hidden mb-3 shadow-sm position-relative bg-light" style="height: 400px;">
                        <img src="<?= $mainImage ?>" 
                             id="mainTourImage"
                             class="w-100 h-100 object-fit-cover" 
                             alt="Main Image" 
                             onerror="this.src='public/assets/img/no-image.png'">
                    </div>
                    
                    <?php if (count($images) > 1): ?>
                    <div class="row g-2">
                        <?php foreach ($images as $index => $img): 
                            // Path Logic for Thumbnails
                            $thumbSrc = 'public/assets/img/no-image.png';
                            if (strpos($img, 'http') === 0) {
                                $thumbSrc = $img;
                            } else {
                                if (strpos($img, 'uploads/') !== false) {
                                     $thumbSrc = 'public/' . $img;
                                } else {
                                     $thumbSrc = 'public/uploads/tours/' . $img;
                                }
                            }
                            if (defined('BASE_URL') && strpos($thumbSrc, 'http') !== 0) {
                                $thumbSrc = BASE_URL . $thumbSrc;
                            }
                        ?>
                            <div class="col-2">
                                <img src="<?= $thumbSrc ?>" 
                                     class="gallery-thumb border" 
                                     alt="Thumb" 
                                     onmouseover="document.getElementById('mainTourImage').src = this.src"
                                     onerror="this.src='public/assets/img/no-image.png'">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card card-modern">
                <div class="card-header-modern">
                    <span class="text-primary"><i class="bi bi-file-text-fill me-2"></i> Giới thiệu Tour</span>
                </div>
                <div class="card-body">
                    <div class="text-secondary" style="line-height: 1.7; text-align: justify;">
                        <?= $desc ?: '<em class="text-muted">Chưa có mô tả chi tiết.</em>' ?>
                    </div>
                </div>
            </div>

   <div class="card card-modern">
    <div class="card-header-modern">
        <span class="text-info"><i class="bi bi-map-fill me-2"></i> Lịch trình chi tiết</span>
    </div>
    <div class="card-body"> 
        <?php 
        // Kiểm tra xem có dữ liệu timeline dạng mảng không
        $hasTimeline = !empty($schedule['days']) && is_array($schedule['days']);
        ?>

        <?php if(!empty($day['activities']) && is_array($day['activities'])): ?>
                            <ul class="list-unstyled mb-0 bg-light p-3 rounded border border-light">
                                <?php foreach ($day['activities'] as $act): 
                                    // --- XỬ LÝ DỮ LIỆU LỒNG (FIX LỖI DB) ---
                                    // Nếu $act lại là một chuỗi JSON (do lỗi lưu DB 2 lần), decode nó ra
                                    $finalActs = [];
                                    if (is_string($act) && (strpos(trim($act), '[') === 0 || strpos(trim($act), '{') === 0)) {
                                         $decoded = json_decode($act, true);
                                         if (is_array($decoded)) {
                                             // Đệ quy lấy text sạch từ mảng con này
                                             $cleanText = recursiveClean($decoded);
                                             // Tách theo dòng để hiển thị từng mục
                                             $finalActs = explode("\n", $cleanText);
                                         } else {
                                             $finalActs[] = $act;
                                         }
                                    } else {
                                        $finalActs[] = $act;
                                    }
                                ?>
                                    
                                    <?php foreach ($finalActs as $singleAct): ?>
                                        <?php if(trim($singleAct) !== ''): ?>
                                            <li class="mb-2 d-flex align-items-start text-secondary">
                                                <i class="bi bi-check-circle-fill text-success me-2 mt-1" style="font-size: 0.8rem;"></i>
                                                <span><?= htmlspecialchars($singleAct) ?></span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
            
            <div class="bg-light rounded p-3 border border-light">
                <div class="small text-secondary" style="max-height: 400px; overflow-y: auto; text-align: justify; line-height: 1.6;">
                    <?php if (!empty($scheduleText)): ?>
                        <?= nl2br(htmlspecialchars($scheduleText)) ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x me-1"></i> Chưa cập nhật lịch trình chi tiết.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>
        </div>

        <div class="col-lg-4">
            
            <div class="card card-modern">
                <div class="card-header-modern bg-light">
                    <span class="text-success"><i class="bi bi-cash-coin me-2"></i> Bảng giá niêm yết</span>
                </div>
                <div class="card-body">
                    <div class="text-center py-3">
                        <small class="text-muted text-uppercase fw-bold">Giá cơ bản</small>
                        <h2 class="text-success fw-bold m-0"><?= number_format($basePrice) ?> <small class="fs-6 text-muted">VND</small></h2>
                    </div>
                    <hr class="my-2">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-person-fill text-secondary me-2"></i> Người lớn</span>
                            <span class="fw-bold"><?= number_format($prices['adult'] ?? $basePrice) ?> ₫</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-emoji-smile-fill text-secondary me-2"></i> Trẻ em</span>
                            <span class="fw-bold"><?= number_format($prices['child'] ?? 0) ?> ₫</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card card-modern">
                <div class="card-header-modern">
                    <span class="text-dark"><i class="bi bi-info-circle-fill me-2"></i> Thông tin bổ sung</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small text-muted fw-bold text-uppercase">Danh mục</label>
                        <div>
                            <span class="badge badge-soft-primary fs-6 fw-normal">
                                <?= !empty($t['category_id']) ? "Danh mục #" . $t['category_id'] : 'Tour chung' ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small text-muted fw-bold text-uppercase">Thời lượng</label>
                        <div class="fw-bold text-dark"><i class="bi bi-clock-history me-1"></i> <?= $duration ?> Ngày</div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted fw-bold text-uppercase">Đối tác / Nhà cung cấp</label>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            <?php if (!empty($suppliers)): ?>
                                <?php foreach ($suppliers as $sup): ?>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-building me-1"></i> <?= htmlspecialchars($sup) ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <em class="small text-muted">Chưa cập nhật</em>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-modern">
                <div class="card-header-modern">
                    <span class="text-warning"><i class="bi bi-shield-check me-2"></i> Chính sách</span>
                </div>
                <div class="card-body bg-light rounded-bottom">
                    <div class="small text-secondary" style="max-height: 300px; overflow-y: auto;">
                        <?= $policyText ? nl2br(htmlspecialchars($policyText)) : '<em>Chưa có thông tin điều khoản.</em>' ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>