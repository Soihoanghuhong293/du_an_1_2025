<?php ob_start(); ?>

<div class="container mt-4">

    <h3>Nhật ký tour</h3>

    <!-- Form thêm nhật ký -->
    <form action="<?= url('guide-diary-store') ?>" method="POST" class="mb-4">
        <input type="hidden" name="booking_id" value="<?= $booking_id ?>">

        <div class="mb-3">
            <label>Ghi chú hôm nay:</label>
            <textarea name="entry" class="form-control" rows="3" required></textarea>
        </div>

        <button class="btn btn-primary">Lưu nhật ký</button>
    </form>

    <!-- Danh sách nhật ký -->
    <h5>Danh sách nhật ký:</h5>
    <ul class="list-group">
        <?php foreach ($diary['entries'] as $e): ?>
            <li class="list-group-item">
                <?= htmlspecialchars($e) ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <!-- Nút quay lại đặt dưới bảng -->
    <a href="<?= BASE_URL ?>website_quan_ly_tour/?act=guide-tours" class="btn btn-primary mt-3">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>

</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/AdminLayout.php';
?>
