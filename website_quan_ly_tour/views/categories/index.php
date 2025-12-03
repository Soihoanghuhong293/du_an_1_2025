<div class="content-wrapper p-4">
    <h3 class="mb-4">Danh sách danh mục</h3>

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
                            onclick="return confirm('Bạn muốn xóa thật không ?')"
                            class ="btn btn-danger btn-sm">
                            Xóa
                                
                            </a>
                        </td>
                        
                        
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Chưa có danh mục nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
