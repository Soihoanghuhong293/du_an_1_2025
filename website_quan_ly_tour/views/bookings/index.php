
<div class="content-wrapper p-4">

    <h3 class="mb-4">Danh sách Booking</h3>

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

                        <!-- Tour ID hoặc tên Tour -->
                        <td><?= htmlspecialchars($bk['tour_id'] ?? 'N/A') ?></td>

                        <!-- Người tạo -->
                        <td><?= htmlspecialchars($bk['created_by'] ?? 'N/A') ?></td>

                        <!-- Hướng dẫn viên -->
                        <td><?= htmlspecialchars($bk['assigned_guide_id'] ?? 'Chưa phân công') ?></td>

                        <!-- Status -->
                        <td>
                            <?php if (!empty($bk['status'])): ?>
                                <span class="badge bg-success">Đang chạy</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Draft</span>
                            <?php endif; ?>
                        </td>

                        <!-- Ngày bắt đầu - kết thúc -->
                        <td><?= !empty($bk['start_date']) ? date('d/m/Y', strtotime($bk['start_date'])) : '-' ?></td>
                        <td><?= !empty($bk['end_date']) ? date('d/m/Y', strtotime($bk['end_date'])) : '-' ?></td>

                        <!-- Notes -->
                        <td><?= nl2br(htmlspecialchars($bk['notes'] ?? '')) ?></td>

                        <!-- Timestamps -->
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
