<div class="row">
    <div class="col-12">
        <div class="card card-modern">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">Danh sách Tours</h3>
                <div class="card-tools">
                    <a href="index.php?act=tour-create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Thêm mới</a>
                </div>
            </div>
            
            <div class="card-body">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th style="width: 100px;">Hình ảnh</th>
                            <th>Tên Tour</th>
                            <th style="width: 150px;" class="text-center">Giá</th>
                            <th style="width: 120px;" class="text-center">Trạng thái</th>
                            <th style="width: 180px;" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tours)): ?>
                            <?php foreach ($tours as $tour): ?>
                                <?php
                                    // 1. Lấy ảnh đầu tiên (Model đã decode sẵn thành mảng)
                                    $images = is_array($tour['images']) ? $tour['images'] : [];
                                    $firstImage = !empty($images) ? $images[0] : null;
                                    
                                    // 2. Tạo đường dẫn ảnh
                                    // BASE_URL + 'uploads/tours/' + tên file
                                    $imgSrc = '';
                                    if ($firstImage) {
                                        // Kiểm tra nếu trong DB lỡ lưu full path thì dùng luôn, không thì nối chuỗi
$imgSrc = (strpos($firstImage, 'uploads/') !== false) ? $firstImage : 'public/uploads/tours/' . $firstImage;                                    }
                                ?>
                                <tr>
                                    <td class="text-center text-muted">#<?= $tour['id'] ?></td>
                                    <td class="text-center">
                                        <?php if ($firstImage): ?>
                                            <img src="<?= BASE_URL . $imgSrc ?>" 
                                                 alt="Tour" 
                                                 class="rounded border shadow-sm"
                                                 style="width: 60px; height: 40px; object-fit: cover;"
                                                 onerror="this.src='public/assets/img/no-image.png'">
                                        <?php else: ?>
                                            <span class="badge bg-light text-secondary border">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary"><?= htmlspecialchars($tour['name']) ?></div>
                                        <small class="text-muted"><?= $tour['duration_days'] ?> ngày</small>
                                    </td>
                                    <td class="text-center fw-bold text-success">
                                        <?= number_format($tour['price'], 0, ',', '.') ?> ₫
                                    </td>
                                    <td class="text-center">
                                        <?php if ($tour['status'] == 1): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Tạm ẩn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
<a href="index.php?act=tour-show&id=<?= $tour['id'] ?>" 
   class="btn btn-outline-info btn-sm" 
   title="Xem chi tiết">
    <i class="bi bi-eye"></i>
</a>                                        <a href="index.php?act=tour-edit&id=<?= $tour['id'] ?>" class="btn btn-outline-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                        <a href="index.php?act=tour-delete&id=<?= $tour['id'] ?>" onclick="return confirm('Xóa?')" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4">Chưa có dữ liệu.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>f
    </div>
</div>