
<div class="row">
  <div class="col-12">
    <!-- Card Danh mục -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Danh sách tài khoản admin và hdv</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="bi bi-dash-lg"></i>
          </button>
        </div>
      </div>
<div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= BASE_URL ?>users/create" class="btn btn-primary">Thêm người dùng</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><?= $u->id ?></td>
                    <td><?= htmlspecialchars($u->name) ?></td>
                    <td><?= htmlspecialchars($u->email) ?></td>
                    <td><?= htmlspecialchars($u->role) ?></td>
                    <td>
                        <span class="<?= $u->status ? 'text-success' : 'text-danger' ?>">
                            <?= $u->status ? 'Hoạt động' : 'Khóa' ?>
                        </span>
                    </td>
                    <td>
                    <a href="<?= BASE_URL ?>users/show?id=<?= $u->id ?>" class="btn btn-sm btn-info">Xem</a>
                    <a href="<?= BASE_URL ?>users/edit?id=<?= $u->id ?>" class="btn btn-sm btn-warning">Sửa</a>

                    <?php if ($u->status == 1): ?>
                        <a href="<?= BASE_URL ?>users/toggleStatus?id=<?= $u->id ?>"
                        onclick="return confirm('Bạn có chắc muốn KHÓA tài khoản này?')"
                        class="btn btn-sm btn-secondary">
                        Khóa
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>users/toggleStatus?id=<?= $u->id ?>"
                        onclick="return confirm('Bạn muốn MỞ KHÓA tài khoản này?')"
                        class="btn btn-sm btn-success">
                        Mở khóa
                        </a>
                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>users/delete?id=<?= $u->id ?>" 
                    onclick="return confirm('Xác nhận xóa?')" 
                    class="btn btn-sm btn-danger">Xóa</a>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($users)): ?>
                <tr>
                    <td colspan="6" class="text-center">Không có người dùng nào.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Lấy toàn bộ nội dung vừa tạo
$content = ob_get_clean();

// Hiển thị layout Admin
view('layouts.AdminLayout', [
    'title' => 'Danh mục - Website Quản Lý Tour',
    'pageTitle' => 'Account',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Acount', 'url' => BASE_URL . 'home', 'active' => true],
    ],
]);
?>

