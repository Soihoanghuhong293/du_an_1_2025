<?php
// views/guides/show.php

// 1. HELPER FUNCTIONS: Xử lý logic tại đầu file để View sạch sẽ hơn
function getTourNamesByIds($ids) {
    if (empty($ids) || !function_exists('getDB')) return [];
    
    // Lọc ID an toàn
    $idsClean = array_values(array_filter(array_map('intval', $ids)));
    if (empty($idsClean)) return [];

    try {
        $pdo = getDB();
        $placeholders = implode(',', array_fill(0, count($idsClean), '?'));
        $stmt = $pdo->prepare("SELECT id, name FROM tours WHERE id IN ($placeholders)");
        $stmt->execute($idsClean);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // Trả về mảng [id => name]
    } catch (Exception $e) {
        return [];
    }
}

// Hàm render lịch sử đệ quy
function renderHistoryItem($item) {
    if (is_string($item) || is_numeric($item)) {
        return nl2br(htmlspecialchars((string)$item));
    }

    if (is_array($item)) {
        // Trường hợp đặc biệt: Danh sách Tours
        if (isset($item['tours']) && is_array($item['tours'])) {
            $tours = getTourNamesByIds($item['tours']);
            if (empty($tours)) return '<span class="text-muted fst-italic">Không tìm thấy dữ liệu tour.</span>';
            
            $html = '<div class="d-flex flex-wrap gap-2">';
            foreach ($tours as $tid => $tname) {
                $html .= '<a href="index.php?act=tour-show&id=' . $tid . '" class="badge bg-success text-decoration-none p-2"><i class="bi bi-geo-alt-fill me-1"></i>' . htmlspecialchars($tname) . '</a>';
            }
            $html .= '</div>';
            return $html;
        }

        // Trường hợp mảng thường
        $html = '<ul class="list-unstyled mb-0 border-start border-2 ps-3 border-secondary border-opacity-25">';
        foreach ($item as $k => $v) {
            $label = is_string($k) ? '<span class="fw-bold text-secondary">' . htmlspecialchars($k) . ':</span> ' : '';
            $html .= '<li class="mb-1">' . $label . renderHistoryItem($v) . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
    return '';
}

// Xử lý dữ liệu History JSON trước khi hiển thị
$historyData = [];
if (!empty($guide['history'])) {
    $decoded = json_decode($guide['history'], true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($decoded['tours'])) {
            $historyData[] = ['title' => 'Lịch sử dẫn tour', 'content' => ['tours' => $decoded['tours']]];
        } else {
            // Nếu là mảng phẳng hoặc mảng key-value
            $historyData[] = ['title' => 'Ghi nhận chi tiết', 'content' => $decoded];
        }
    } else {
        $historyData[] = ['title' => 'Ghi chú văn bản', 'content' => $guide['history']];
    }
}

ob_start(); 
?>

<style>
    .card-modern { border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); border-radius: 0.75rem; margin-bottom: 1.5rem; }
    .card-header-modern { background: #fff; border-bottom: 1px solid #e3e6f0; padding: 1rem 1.5rem; font-weight: 700; color: #4e73df; border-radius: 0.75rem 0.75rem 0 0; display: flex; justify-content: space-between; align-items: center; }
    .nav-tabs-modern { border-bottom: 2px solid #e3e6f0; }
    .nav-tabs-modern .nav-link { border: none; color: #858796; font-weight: 600; padding: 1rem 1.5rem; transition: all 0.2s; border-bottom: 2px solid transparent; margin-bottom: -2px; }
    .nav-tabs-modern .nav-link:hover { color: #4e73df; }
    .nav-tabs-modern .nav-link.active { color: #4e73df; border-bottom: 2px solid #4e73df; background: transparent; }
    .info-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #b7b9cc; letter-spacing: 0.05em; margin-bottom: 0.2rem; }
    .info-value { font-weight: 600; color: #5a5c69; font-size: 0.95rem; }
    .avatar-placeholder { width: 120px; height: 120px; font-size: 40px; font-weight: bold; display: flex; align-items: center; justify-content: center; }
</style>

<div class="container-fluid pt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Hồ sơ Hướng dẫn viên</h4>
            <div class="text-muted d-flex align-items-center gap-2">
                <span><i class="bi bi-hash"></i> ID: <strong><?= $guide['id'] ?></strong></span>
                <span class="mx-2">|</span>
                <span class="badge bg-light text-secondary border">
                    <i class="bi bi-person-badge"></i> <?= htmlspecialchars($guide['user_id'] ?? 'N/A') ?>
                </span>
            </div>
        </div>
        <div>
            <a href="index.php?act=guides" class="btn btn-secondary btn-sm me-2">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <div class="btn-group">
                <a href="index.php?act=guides/edit&id=<?= $guide['id'] ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil-square"></i> Cập nhật
                </a>
                <button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item text-danger" href="index.php?act=guides/delete&id=<?= $guide['id'] ?>" onclick="return confirm('Xác nhận xóa hồ sơ HDV này?')"><i class="bi bi-trash me-2"></i>Xóa hồ sơ</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card card-modern text-center p-4">
                <div class="d-flex justify-content-center mb-3">
                    <?php if (!empty($guide['avatar'])): ?>
                        <img src="<?= BASE_URL ?>public/uploads/guides/<?= htmlspecialchars($guide['avatar']) ?>" class="rounded-circle shadow-sm" style="width:120px;height:120px;object-fit:cover;">
                    <?php else: ?>
                        <div class="avatar-placeholder rounded-circle bg-primary bg-opacity-10 text-primary">
                            <?= htmlspecialchars(strtoupper(substr($guide['name'] ?? ($guide['user_id'] ?? 'U'),0,1))) ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($guide['name'] ?? 'Chưa cập nhật tên') ?></h5>
                <p class="text-muted mb-3"><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($guide['email'] ?? 'No Email') ?></p>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <span class="badge bg-warning text-dark"><i class="bi bi-star-fill text-white"></i> <?= htmlspecialchars($guide['rating'] ?? '0.0') ?> Rating</span>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25"><?= htmlspecialchars($guide['group_type'] ?? 'General') ?></span>
                </div>

                <div class="row text-start border-top pt-3 g-3">
                    <div class="col-12">
                        <div class="info-label"><i class="bi bi-telephone me-1"></i> Điện thoại</div>
                        <div class="info-value"><?= htmlspecialchars($guide['phone'] ?? '---') ?></div>
                    </div>
                    <div class="col-12">
                        <div class="info-label"><i class="bi bi-cake2 me-1"></i> Ngày sinh</div>
                        <div class="info-value"><?= !empty($guide['birthdate']) ? date('d/m/Y', strtotime($guide['birthdate'])) : '---' ?></div>
                    </div>
                     <div class="col-12">
                        <div class="info-label"><i class="bi bi-heart-pulse me-1"></i> Sức khỏe</div>
                        <div class="info-value"><?= htmlspecialchars($guide['health_status'] ?? 'Bình thường') ?></div>
                    </div>
                </div>
            </div>

            <div class="card card-modern">
                <div class="card-header-modern">
                    <span><i class="bi bi-award text-warning me-2"></i> Kỹ năng & Ngôn ngữ</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="info-label mb-1">Ngôn ngữ</div>
                        <div class="d-flex flex-wrap gap-1">
                            <?php if(!empty($guide['languages'])): 
                                $langs = explode(',', $guide['languages']);
                                foreach($langs as $lang): ?>
                                <span class="badge bg-secondary"><?= trim(htmlspecialchars($lang)) ?></span>
                            <?php endforeach; else: ?>
                                <span class="text-muted small">Chưa cập nhật</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <div class="info-label mb-1">Chuyên môn</div>
                        <div class="d-flex flex-wrap gap-1">
                             <?php if (!empty($guide['specialty'])): 
                                $items = preg_split('/[,;]+/', $guide['specialty']);
                                $items = array_filter(array_map('trim', $items));
                                foreach ($items as $it): ?>
                                    <span class="badge bg-light text-primary border border-primary border-opacity-25"><?= htmlspecialchars($it) ?></span>
                                <?php endforeach; 
                            else: ?>
                                <span class="text-muted small">---</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-modern">
                <div class="card-header bg-white border-0 pb-0">
                    <ul class="nav nav-tabs nav-tabs-modern" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview">
                                <i class="bi bi-info-circle me-1"></i> Tổng quan
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#history">
                                <i class="bi bi-clock-history me-1"></i> Lịch sử hoạt động
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content pt-3">
                        
                        <div class="tab-pane fade show active" id="overview">
                            <h6 class="fw-bold text-primary mb-3 text-uppercase small">Chứng chỉ & Kinh nghiệm</h6>
                            
                            <div class="mb-4">
                                <div class="info-label mb-2">Chứng chỉ hành nghề</div>
                                <div class="bg-light p-3 rounded border border-light-subtle d-flex align-items-start">
                                    <i class="bi bi-card-heading text-warning fs-4 me-3"></i>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($guide['certificate'] ?? 'Chưa có thông tin chứng chỉ') ?></div>
                                        <small class="text-muted">Cần xác minh định kỳ</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="info-label mb-2">Kinh nghiệm làm việc</div>
                                <div class="p-3 border rounded text-dark" style="background-color: #f8f9fa;">
                                    <?= nl2br(htmlspecialchars($guide['experience'] ?? 'Chưa có mô tả kinh nghiệm.')) ?>
                                </div>
                            </div>

                            <hr class="text-muted opacity-25">
                            
                            <div class="d-flex justify-content-between text-muted small">
                                <span><i class="bi bi-calendar-plus me-1"></i> Ngày tạo hồ sơ: <?= htmlspecialchars($guide['created_at']) ?></span>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="history">
                            <?php if (!empty($historyData)): ?>
                                <div class="timeline">
                                    <?php foreach ($historyData as $idx => $block): ?>
                                        <div class="mb-4">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 12px;">
                                                    <?= $idx + 1 ?>
                                                </div>
                                                <h6 class="fw-bold text-dark m-0"><?= $block['title'] ?></h6>
                                            </div>
                                            <div class="ms-4 ps-2 border-start border-2">
                                                <div class="bg-white p-3 border rounded shadow-sm">
                                                    <?= renderHistoryItem($block['content']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">Chưa có lịch sử hoạt động nào được ghi nhận.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

view('layouts.AdminLayout', [
    'title' => 'Hồ sơ: ' . htmlspecialchars($guide['name'] ?? 'Guide Detail'),
    'pageTitle' => 'Chi tiết Hướng dẫn viên',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Dashboard', 'url' => BASE_URL . 'home'],
        ['label' => 'Danh sách HDV', 'url' => BASE_URL . 'guides'],
        ['label' => 'Hồ sơ chi tiết', 'active' => true],
    ],
]);
?>