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
