
<div class="row">
  <div class="col-12">
    <!-- Card Danh mục -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Danh sách Booking</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="bi bi-dash-lg"></i>
          </button>
        </div>
      </div>
<div class="card-body">


    <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tour</th>
                <th>Created By</th>
                <th>Guide</th>
                <th>Status</th>
                <th>Start</th>
                <th>End</th>
                <th>Notes</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($bookings)) : ?>
                <?php foreach ($bookings as $bk): ?>
                    <tr>
                        <td><?= htmlspecialchars($bk['id']) ?></td>
                        <td><?= htmlspecialchars($bk['tour_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($bk['created_name'] ?? 'N/A') ?></td>
                        
                        <td>
                            <?php if (!empty($bk['guide_name'])): ?>
                                <span class="text-primary"><?= htmlspecialchars($bk['guide_name']) ?></span>
                            <?php else: ?>
                                <span class="text-muted fst-italic">Chưa phân công</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if (!empty($bk['status'])): ?>
                                <span class="badge bg-success">Đang chạy</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Draft</span>
                            <?php endif; ?>
                        </td>

                        <td><?= !empty($bk['start_date']) ? date('d/m/Y', strtotime($bk['start_date'])) : '-' ?></td>
                        <td><?= !empty($bk['end_date']) ? date('d/m/Y', strtotime($bk['end_date'])) : '-' ?></td>

                        <td>
                            <?php if ($bk['status'] == 1): ?>
                                <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                            <?php elseif ($bk['status'] == 2): ?>
                                <span class="badge bg-info">Đã cọc</span>
                            <?php elseif ($bk['status'] == 3): ?>
                                <span class="badge bg-success">Hoàn tất</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Khác</span>
                            <?php endif; ?>
                        </td>

                        <td><?= !empty($bk['created_at']) ? date('d/m/Y H:i', strtotime($bk['created_at'])) : '-' ?></td>
                        <td><?= !empty($bk['updated_at']) ? date('d/m/Y H:i', strtotime($bk['updated_at'])) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center text-muted">Không có booking nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Lấy toàn bộ nội dung vừa tạo
$content = ob_get_clean();

// Hiển thị layout Admin
view('layouts.AdminLayout', [
    'title' => 'Danh mục - Website Quản Lý Tour',
    'pageTitle' => 'Booking',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Danh mục', 'url' => BASE_URL . 'home', 'active' => true],
    ],
]);
?>
