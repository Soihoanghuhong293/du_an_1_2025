<div class="table-responsive">
    <h3 style="margin-bottom: 15px;">Danh Sách Tour Hiện Tại</h3>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Tên Tour</th>
                <th>Mô tả</th>
                <th>Danh mục</th>
                <th>Lịch trình</th>
                <th>Hình ảnh</th>
                <th>Giá chi tiết</th>
                <th>Chính sách</th>
                <th>Nhà cung cấp</th>
                <th>Giá</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Ngày cập nhật</th>
                <th>Thao tác</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($tours as $tour): ?>

            <?php
                $schedule = json_decode($tour['schedule'], true);
                $images = json_decode($tour['images'], true);
                $prices = json_decode($tour['prices'], true);
                $policies = json_decode($tour['policies'], true);
                $suppliers = json_decode($tour['suppliers'], true);
            ?>

            <tr>
                <td><?= $tour['id'] ?></td>
                <td><?= htmlspecialchars($tour['name']) ?></td>
                <td><?= htmlspecialchars($tour['description']) ?></td>
                <td><?= $tour['category_id'] ?></td>

                <!-- Lịch trình -->
                <td>
                    <?php if (!empty($schedule['days'])): ?>
                        <?php foreach ($schedule['days'] as $day): ?>
                            <strong>Ngày:</strong> <?= $day['date'] ?><br>
                            <strong>Hoạt động:</strong>
                            <ul>
                                <?php foreach ($day['activities'] as $act): ?>
                                    <li><?= htmlspecialchars($act) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </td>

                <!-- Hình ảnh -->
                <td>
                    <?php foreach ($images as $img): ?>
                        <span class="badge bg-info text-dark"><?= $img ?></span><br>
                    <?php endforeach; ?>
                </td>

                <!-- Giá chi tiết -->
                <td>
                    Người lớn: <strong><?= number_format($prices['adult']) ?> VNĐ</strong><br>
                    Trẻ em: <strong><?= number_format($prices['child']) ?> VNĐ</strong>
                </td>

                <!-- Chính sách -->
                <td>
                    <?= htmlspecialchars($policies['booking'] ?? '') ?>
                </td>

                <!-- Nhà cung cấp -->
                <td>
                    <?php foreach ($suppliers as $s): ?>
                        <span class="badge bg-secondary"><?= htmlspecialchars($s) ?></span><br>
                    <?php endforeach; ?>
                </td>

                <td><?= number_format($tour['price']) ?> VNĐ</td>
                <td><?= $tour['status'] ?></td>
                <td><?= $tour['created_at'] ?></td>
                <td><?= $tour['updated_at'] ?></td>

                <td>
                    <a href="<?= BASE_URL . 'tour-edit&id=' . $tour['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                    <a href="<?= BASE_URL . 'tour-delete&id=' . $tour['id'] ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa?')" 
                       class="btn btn-danger btn-sm">
                        Xóa
                    </a>
                </td>
            </tr>

            <?php endforeach; ?>
        </tbody>
    </table>
</div>
