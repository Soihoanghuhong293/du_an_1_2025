<?php
// Bắt đầu lưu output vào $content
ob_start();
?>

<div class="row">
  <div class="col-12">
    <!-- Card Danh mục -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Danh sách danh mục</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="bi bi-dash-lg"></i>
          </button>
        </div>
      </div>

      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên danh mục</th>
              <th>Mô tả</th>
              <th>Status</th>
              <th>Created at</th>
              <th>Updated at</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($categories)): ?>
              <?php foreach ($categories as $item): ?>
                <tr>
                  <td><?= htmlspecialchars($item['id']) ?></td>
                  <td><?= htmlspecialchars($item['name']) ?></td>
                  <td><?= htmlspecialchars($item['description']) ?></td>
                  <td><?= htmlspecialchars($item['status']) ?></td>
                  <td><?= htmlspecialchars($item['created_at']) ?></td>
                  <td><?= htmlspecialchars($item['updated_at']) ?></td>
                  <td>
                    <a href="index.php?act=category-edit&id=<?= $item['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                    <a href="index.php?act=category-delete&id=<?= $item['id'] ?>"
                       onclick="return confirm('Bạn muốn xóa thật không?')"
                       class="btn btn-danger btn-sm">Xóa</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center">Chưa có danh mục nào</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <!-- /.card -->
  </div>
</div>
<!-- /.row -->

<?php
// Lấy toàn bộ nội dung vừa tạo
$content = ob_get_clean();

// Hiển thị layout Admin
view('layouts.AdminLayout', [
    'title' => 'Danh mục - Website Quản Lý Tour',
    'pageTitle' => 'Danh mục',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Danh mục', 'url' => BASE_URL . 'home', 'active' => true],
    ],
]);
?>
