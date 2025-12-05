
<div class="row">
  <div class="col-12">
    <!-- Card Danh mục -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Danh sách Tours</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="bi bi-dash-lg"></i>
          </button>
        </div>
      </div>
<div class="card-body">

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
                $schedule  = $tour['lich_trinh'] ?? [];
                $images    = $tour['hinh_anh'] ?? [];
                $prices    = $tour['gia_chi_tiet'] ?? [];
                $policies  = $tour['chinh_sach'] ?? [];
                $suppliers = $tour['nha_cung_cap'] ?? [];
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
                            <strong>Ngày:</strong> <?= htmlspecialchars($day['date']) ?><br>
                            <strong>Hoạt động:</strong>
                            <ul>
                                <?php foreach ($day['activities'] as $act): ?>
                                    <li><?= htmlspecialchars($act) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <em>Không có dữ liệu</em>
                    <?php endif; ?>
                </td>

                <!-- Hình ảnh -->
                <td>
                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $img): ?>
                            <span class="badge bg-info text-dark"><?= htmlspecialchars($img) ?></span><br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <em>Không có ảnh</em>
                    <?php endif; ?>
                </td>

                <!-- Giá chi tiết -->
                <td>
                    Người lớn: <strong><?= number_format($prices['adult'] ?? 0) ?> VNĐ</strong><br>
                    Trẻ em: <strong><?= number_format($prices['child'] ?? 0) ?> VNĐ</strong>
                </td>

                <!-- Chính sách -->
                <td>
                    <?= htmlspecialchars($policies['booking'] ?? 'Không có chính sách') ?>
                </td>

                <!-- Nhà cung cấp -->
                <td>
                    <?php if (!empty($suppliers)): ?>
                        <?php foreach ($suppliers as $s): ?>
                            <span class="badge bg-secondary"><?= htmlspecialchars($s) ?></span><br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <em>Không có NCC</em>
                    <?php endif; ?>
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

<?php
// Lấy toàn bộ nội dung vừa tạo
$content = ob_get_clean();

// Hiển thị layout Admin
view('layouts.AdminLayout', [
    'title' => 'tour - Website Quản Lý Tour',
    'pageTitle' => 'Tour',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Danh mục', 'url' => BASE_URL . 'home', 'active' => true],
    ],
]);
?>
