<?php ob_start(); ?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <!-- Header -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-calendar-check me-2"></i>
                Lịch làm việc của hướng dẫn viên
            </h5>
        </div>

        <!-- Body -->
        <div class="card-body p-0">
            <table class="table table-hover table-bordered mb-0 align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Tour</th>
                        <th>Ngày bắt đầu</th>
                        <th>Số khách</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="text-start fw-semibold">
                                    <?= htmlspecialchars($item['tour_name']) ?>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($item['start_date'])) ?>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= $item['customer_count'] ?? 0 ?> khách
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= url('guide-diary', ['id' => $item['id']]) ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-journal-text me-1"></i>
                                        Xem nhật ký
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                Chưa có lịch làm việc
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/AdminLayout.php';
?>
