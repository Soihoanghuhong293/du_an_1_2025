
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
        <th>Tên Tour</th>
        <th>Ngày tạo</th>
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
    <td><?= htmlspecialchars($tour['name']) ?></td>
    <td><?= date('d/m/Y H:i', strtotime($tour['created_at'])) ?></td>

    <td>
        <a href="index.php?act=tour-show&id=<?= $tour['id'] ?>" class="btn btn-info btn-sm">
            Xem chi tiết
        </a>

        <a href="<?= BASE_URL . 'index.php?act=tour-edit&id=' . $tour['id'] ?>" class="btn btn-warning btn-sm">
            Sửa
        </a>

        <a href="<?= BASE_URL . 'index.php?act=tour-delete&id=' . $tour['id'] ?>"
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
