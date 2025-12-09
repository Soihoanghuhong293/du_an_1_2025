<?php
// 1. DATA PREPARATION (Controller passes $tour)
$t = is_array($tour) ? $tour : [];

if (empty($t)) {
    echo '<div class="alert alert-danger m-4">Không tìm thấy thông tin tour. <a href="index.php?act=tours">Quay lại</a></div>';
    return;
}

// Helper to safely decode JSON for view display
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

// Decode JSON Fields
$images      = safeJsonView($t['images'] ?? []);
$schedule    = safeJsonView($t['schedule'] ?? []); // Structure: {'days': [...]}
$prices      = safeJsonView($t['prices'] ?? []);   // Structure: {'adult': ..., 'child': ...}
$policies    = safeJsonView($t['policies'] ?? []); 
$suppliers   = safeJsonView($t['suppliers'] ?? []); 

// Image Handling (Fix Path)
// If image path doesn't start with http/https, prepend BASE_URL . 'public/uploads/tours/'
// BUT check if it already has 'uploads/' in it.
$firstImage = !empty($images) ? $images[0] : null;
$mainImage = 'public/assets/img/no-image.png'; // Default

if ($firstImage) {
    if (strpos($firstImage, 'http') === 0) {
        $mainImage = $firstImage;
    } else {
        // If stored as just filename, add full path
        // If stored as 'uploads/tours/file.jpg', prepend 'public/' if missing
        if (strpos($firstImage, 'uploads/') !== false) {
             $mainImage = 'public/' . $firstImage;
        } else {
             $mainImage = 'public/uploads/tours/' . $firstImage;
        }
    }
    // Ensure BASE_URL is prepended for local relative paths
    if (defined('BASE_URL') && strpos($mainImage, 'http') !== 0) {
        $mainImage = BASE_URL . $mainImage;
    }
}

// Policy Text
$policyText = $policies['booking'] ?? $policies['text'] ?? '';
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
                <div class="card-body ps-4">
                    <?php if (!empty($schedule['days'])): ?>
                        <div class="mt-2">
                            <?php foreach ($schedule['days'] as $idx => $day): ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <h6 class="fw-bold text-dark mb-2">
                                        <span class="text-primary me-2">Ngày <?= $idx + 1 ?>:</span> 
                                        <?= htmlspecialchars($day['date'] ?? 'Lịch trình') ?>
                                    </h6>
                                    
                                    <?php if(!empty($day['activities']) && is_array($day['activities'])): ?>
                                        <ul class="list-unstyled mb-0 bg-light p-3 rounded border border-light">
                                            <?php foreach ($day['activities'] as $act): ?>
                                                <li class="mb-2 d-flex align-items-start text-secondary">
                                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1" style="font-size: 0.8rem;"></i>
                                                    <span><?= htmlspecialchars($act) ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <div class="text-muted fst-italic small">Đang cập nhật hoạt động...</div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-secondary">
                            <?= !empty($schedule['text']) ? nl2br(htmlspecialchars($schedule['text'])) : '<div class="text-center py-4 text-muted">Chưa cập nhật lịch trình.</div>' ?>
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