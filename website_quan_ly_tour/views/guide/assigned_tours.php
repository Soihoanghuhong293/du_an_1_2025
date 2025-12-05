<?php ob_start(); ?>

<div class="container mt-4">

    <h3 class="mb-4 fw-bold">
        <i class="bi bi-briefcase-fill text-primary"></i> Tour được phân công
    </h3>

    <?php if (!empty($bookings)): ?>
        <?php foreach ($bookings as $b): ?>

            <?php
                // Tính số ngày
                $days_total = (strtotime($b['end_date']) - strtotime($b['start_date'])) / 86400;
                $days_passed = max(0, (time() - strtotime($b['start_date'])) / 86400);
                $progress = $days_total > 0 ? min(100, round(($days_passed / $days_total) * 100)) : 0;

                $statusColors = [
                    1 => "warning",
                    2 => "info",
                    3 => "success",
                    4 => "danger"
                ];
                $color = $statusColors[$b['status']] ?? "secondary";
            ?>

            <!-- CARD TOUR -->
            <div class="card shadow-sm mb-3 border-0 rounded-3">
                <div class="card-body d-flex align-items-center">

                    <!-- IMAGE -->
                    <div style="width: 120px; height: 80px; overflow:hidden; border-radius:8px;" class="me-3">
                        <img src="<?= asset('dist/assets/img/tours/placeholder.jpg') ?>" 
                             class="img-fluid" style="object-fit:cover; width:100%; height:100%;">
                    </div>

                    <!-- INFO -->
                    <div class="flex-grow-1">

                        <h5 class="fw-bold mb-1 text-primary">
                            <?= htmlspecialchars($b['tour_name']) ?>
                        </h5>

                        <div class="small text-muted">
                            <i class="bi bi-calendar-event"></i>
                            Bắt đầu: <?= date("d/m/Y", strtotime($b['start_date'])) ?>
                            &nbsp;•&nbsp;
                            <i class="bi bi-calendar-check"></i>
                            Kết thúc: <?= date("d/m/Y", strtotime($b['end_date'])) ?>
                        </div>

                        <!-- Trang thái -->
                        <span class="badge bg-<?= $color ?> mt-2 px-3 py-2">
                            <?= htmlspecialchars($b['status_name'] ?? "Không rõ") ?>
                        </span>

                        <!-- Progress -->
                        <div class="progress mt-3" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: <?= $progress ?>%"></div>
                        </div>
                        <small class="text-muted">Tiến độ tour: <?= $progress ?>%</small>
                    </div>

                    <!-- ACTIONS -->
                    <div class="ms-3 text-end">

                        <a href="<?= url('guide-customers', ['id' => $b['id']]) ?>"
                           class="btn btn-light btn-sm border"
                           data-bs-toggle="tooltip" title="Danh sách khách">
                            <i class="bi bi-people"></i>
                        </a>

                        <a href="<?= url('guide-diary', ['id' => $b['id']]) ?>"
                           class="btn btn-warning btn-sm"
                           data-bs-toggle="tooltip" title="Nhật ký tour">
                            <i class="bi bi-journal-text"></i>
                        </a>

                        <a href="<?= url('guide-download', ['id' => $b['id']]) ?>"
                           class="btn btn-success btn-sm"
                           data-bs-toggle="tooltip" title="Tải file phân công">
                            <i class="bi bi-download"></i>
                        </a>

                    </div>
                </div>
            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="alert alert-info text-center py-4">
            <i class="bi bi-info-circle"></i> Không có tour nào được phân công.
        </div>

    <?php endif; ?>

</div>

<?php 
$content = ob_get_clean();
include __DIR__ . '/../layouts/AdminLayout.php';
?>
