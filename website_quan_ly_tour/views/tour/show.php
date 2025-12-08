<?php
// Hiển thị chi tiết tour
// Ensure $t is an array and not null
$t = is_array($tour) ? $tour : [];
if (empty($t)) {
    // controller may already handle not-found; fallback to 404 view
    view('not_found');
    return;
}

// Helper to safely get string values
function sstr($v) {
    if (is_scalar($v)) return (string)$v;
    return '';
}

// Compose fields with fallbacks
$name = htmlspecialchars(sstr($t['name'] ?? $t['ten_tour'] ?? ''));
$description = nl2br(htmlspecialchars(sstr($t['description'] ?? $t['mo_ta'] ?? '')));
$days = $t['lich_trinh']['days'] ?? $t['schedule']['days'] ?? [];
$suppliers = $t['nha_cung_cap'] ?? $t['suppliers'] ?? [];
$images = $t['hinh_anh'] ?? $t['images'] ?? [];
$price = (int)($t['price'] ?? $t['gia'] ?? 0);
$pricesDetail = $t['gia_chi_tiet'] ?? $t['prices'] ?? [];
$policy = '';
if (is_array($t['chinh_sach'] ?? null)) {
    $policy = sstr($t['chinh_sach']['booking'] ?? $t['chinh_sach']['text'] ?? '');
} else {
    $policy = sstr($t['chinh_sach'] ?? $t['policies'] ?? '');
}

?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Chi tiết Tour: <?= $name ?></h3>
        <div class="card-tools">
            <a href="<?= BASE_URL . 'index.php?act=tour' ?>" class="btn btn-secondary btn-sm">Quay lại</a>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4>Mô tả</h4>
                <p><?= $description ?></p>

                <h4>Lịch trình</h4>
                <?php if (!empty($days) && is_array($days)): ?>
                    <?php foreach ($days as $day): ?>
                        <div class="mb-2">
                            <strong>Ngày:</strong> <?= htmlspecialchars(sstr($day['date'] ?? '')) ?><br>
                            <strong>Hoạt động:</strong>
                            <ul>
                                <?php foreach ((array)($day['activities'] ?? []) as $act): ?>
                                    <li><?= htmlspecialchars(sstr($act)) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <em>Không có lịch trình</em>
                <?php endif; ?>

                <h4>Chính sách</h4>
                <p><?= nl2br(htmlspecialchars(sstr($policy ?: 'Không có chính sách'))) ?></p>

                <h4>Nhà cung cấp</h4>
                <?php if (!empty($suppliers) && is_array($suppliers)): ?>
                    <?php foreach ($suppliers as $s): ?>
                        <span class="badge bg-secondary mb-1"><?= htmlspecialchars(sstr($s)) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <em>Không có nhà cung cấp</em>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <h4>Hình ảnh</h4>
                <?php if (!empty($images) && is_array($images)): ?>
                    <?php foreach ($images as $img): ?>
                        <?php $imgSrc = sstr($img); ?>
                        <?php if ($imgSrc !== ''): ?>
                            <?php
                                // If path looks like a bare filename, try to prefix uploads path
                                $src = $imgSrc;
                                if (!preg_match('#^(https?:)?//#i', $imgSrc) && !str_starts_with($imgSrc, '/')) {
                                    $src = rtrim(BASE_URL, '/') . '/' . ltrim($imgSrc, '/');
                                }
                            ?>
                            <div class="mb-2">
                                <img src="<?= htmlspecialchars($src) ?>" alt="" class="img-fluid img-thumbnail" />
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <em>Không có ảnh</em>
                <?php endif; ?>

                <h4>Giá</h4>
                <p><strong><?= number_format($price) ?> VNĐ</strong></p>

                <h5>Giá chi tiết</h5>
                <p>Người lớn: <strong><?= number_format((int)($pricesDetail['adult'] ?? 0)) ?> VNĐ</strong></p>
                <p>Trẻ em: <strong><?= number_format((int)($pricesDetail['child'] ?? 0)) ?> VNĐ</strong></p>

                <div class="mt-3">
                    <a href="<?= BASE_URL . 'index.php?act=tour-edit&id=' . ($t['id'] ?? '') ?>" class="btn btn-warning">Sửa</a>
                    <a href="<?= BASE_URL . 'index.php?act=tour-delete&id=' . ($t['id'] ?? '') ?>" onclick="return confirm('Bạn có chắc muốn xóa?')" class="btn btn-danger">Xóa</a>
                </div>
            </div>
        </div>
    </div>
</div>
