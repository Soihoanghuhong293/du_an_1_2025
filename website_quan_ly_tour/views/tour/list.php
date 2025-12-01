<?php 
// File: src/views/tour/list.php (Chỉ hiển thị bảng dữ liệu)

// Đã loại bỏ block('header') và các thẻ wrapper AdminLTE.
// Mã này chỉ hiển thị bảng dữ liệu thuần.
?>

<div class="table-responsive">
    <h3 style="margin-bottom: 15px;">Danh Sách Tour Hiện Tại</h3>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Tour</th>
                <th>Giá</th>
                <th>Ngày Khởi Hành</th>
                <th style="width: 150px;">Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tours)): ?>
                <?php foreach ($tours as $tour): ?>
                <tr>
                    <td><?php echo htmlspecialchars($tour['id'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($tour['ten_tour'] ?? ''); ?></td>
                    <td><?php echo number_format($tour['gia'] ?? 0); ?> VNĐ</td>
                    <td><?php echo htmlspecialchars($tour['ngay_khoi_hanh'] ?? ''); ?></td>
                    
                    <td>
                        <a href="<?= BASE_URL . 'tour-edit&id=' . ($tour['id'] ?? ''); ?>" class="btn btn-warning btn-sm mr-1">
                            Sửa
                        </a>
                        <a href="<?= BASE_URL . 'tour-delete&id=' . ($tour['id'] ?? ''); ?>" 
                           onclick="return confirm('Xóa tour <?php echo htmlspecialchars($tour['ten_tour'] ?? ''); ?>?');"
                           class="btn btn-danger btn-sm">
                            Xóa
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center py-4">Chưa có tour nào được thêm.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php 
// Đã loại bỏ block('footer')
?>