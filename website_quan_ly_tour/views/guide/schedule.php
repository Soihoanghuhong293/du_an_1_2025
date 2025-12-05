<?php ob_start(); ?>

<div class="container mt-4">
    <h3>Lịch làm việc của hướng dẫn viên</h3>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Tour</th>
                <th>Ngày bắt đầu</th>
                <th>Số khách</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= $item['tour_name'] ?></td>
                    <td><?= $item['start_date'] ?></td>
                    <td><?= $item['customer_count'] ?? 0 ?></td>
                    <td><a href="<?= url('guide-diary', ['id' => $item['id']]) ?>">Xem nhật ký</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/AdminLayout.php';
?>
