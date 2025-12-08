<?php ob_start(); ?>

<div class="container mt-4">
    <h3 class="mb-4">Danh sách tour được phân công</h3>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên tour</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['id']) ?></td>
                            <td><?= htmlspecialchars($b['tour_name']) ?></td>
                            <td><?= htmlspecialchars($b['start_date']) ?></td>
                            <td><?= htmlspecialchars($b['end_date']) ?></td>
                            <td>
                                <?php if ($b['status'] == 1): ?>
                                    <span class="badge text-bg-warning">Chờ xác nhận</span>
                                <?php elseif ($b['status'] == 2): ?>
                                    <span class="badge bg-info">Đã cọc (Guide nhận)</span>
                                    <?php elseif ($b['status'] == 3): ?>
                                    <span class="badge bg-success">hoàn tất</span>
                                <?php elseif ($b['status'] == -1): ?>
                                    <span class="badge text-bg-danger">Từ chối</span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">Không xác định</span>
                                <?php endif; ?>
                            </td>

                            <td>
                               
                                
                                   <a href="index.php?act=guide-show&id=<?= $b['id'] ?>" class="btn btn-info btn-sm">
    <i class="bi bi-eye"></i> Chi tiết
</a>

                               

                                <!-- xác nhận / từ chối -->
                               
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Chưa có tour nào được phân công.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean();
include __DIR__ . '/../layouts/AdminLayout.php';
?>
