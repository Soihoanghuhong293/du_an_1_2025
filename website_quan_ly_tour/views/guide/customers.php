<?php ob_start(); ?>

<div class="container mt-4">
    <h3 class="mb-4">Danh sách khách của tour</h3>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Họ tên</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>CCCD</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($customers)): ?>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['full_name']) ?></td>
                            <td><?= htmlspecialchars($c['phone']) ?></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= htmlspecialchars($c['cccd']) ?></td>
                            <td><?= htmlspecialchars($c['birthday']) ?></td>
                            <td><?= htmlspecialchars($c['gender']) ?></td>
                            <td><?= htmlspecialchars($c['note']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Không có khách nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Nút quay lại đặt dưới bảng -->
    <a href="<?= BASE_URL ?>website_quan_ly_tour/?act=guide-tours" class="btn btn-primary mt-3">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<?php 
$content = ob_get_clean();
include __DIR__ . '/../layouts/adminlayout.php';
?> 
