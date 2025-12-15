<?php ob_start(); ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/index.css">

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 fw-bold text-dark">Sửa Hướng dẫn viên</h3>
                <p class="text-muted mb-0">Chỉnh sửa hồ sơ hướng dẫn viên</p>
            </div>
            <div>
                <a href="index.php?act=guides" class="btn btn-light border fw-bold">Quay lại</a>
            </div>
        </div>

        <div class="card card-modern">
            <div class="card-body">
                <?php
                    $old = $_SESSION['old'] ?? [];
                    $errors = $_SESSION['errors'] ?? [];
                    unset($_SESSION['old'], $_SESSION['errors']);
                    // Merge existing guide data with old input (old takes precedence)
                    $form = array_merge($guide ?? [], $old ?? []);
                ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="index.php?act=guides/update" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($guide['id']) ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Người dùng (User)</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">-- Chọn người dùng --</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= (isset($form['user_id']) && $form['user_id']==$u['id'])? 'selected':'' ?>><?= htmlspecialchars($u['name'] . ' <' . $u['email'] . '>') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="birthdate" class="form-control" value="<?= htmlspecialchars($form['birthdate'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ảnh đại diện</label>
                            <input type="file" name="avatar" class="form-control">
                            <?php if (!empty($form['avatar'])): ?>
                                <div class="mt-2"><img src="<?= BASE_URL ?>public/uploads/guides/<?= htmlspecialchars($form['avatar']) ?>" width="80" class="rounded-circle"></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Điện thoại</label>
                            <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($form['phone'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Chứng chỉ</label>
                            <input type="text" name="certificate" class="form-control" value="<?= htmlspecialchars($form['certificate'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ngôn ngữ</label>
                            <input type="text" name="languages" class="form-control" value="<?= htmlspecialchars($form['languages'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Kinh nghiệm</label>
                            <input type="text" name="experience" class="form-control" value="<?= htmlspecialchars($form['experience'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Lịch sử (History) - Chọn các tour liên quan</label>
                            <?php
                                // Determine selected tour ids from old input or existing history JSON
                                $selectedTours = $form['history_tours'] ?? [];
                                if (empty($selectedTours) && !empty($form['history'])) {
                                    $tmp = json_decode($form['history'], true);
                                    if (json_last_error() === JSON_ERROR_NONE && isset($tmp['tours']) && is_array($tmp['tours'])) {
                                        $selectedTours = $tmp['tours'];
                                    }
                                }
                            ?>
                            <select name="history_tours[]" class="form-select" multiple size="6">
                                <?php foreach ($tours as $t): ?>
                                    <option value="<?= $t['id'] ?>" <?= in_array($t['id'], $selectedTours) ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Rating</label>
                            <input type="number" step="0.1" name="rating" class="form-control" value="<?= htmlspecialchars($form['rating'] ?? 0) ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tình trạng sức khỏe</label>
                            <input type="text" name="health_status" class="form-control" value="<?= htmlspecialchars($form['health_status'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Group type</label>
                            <input type="text" name="group_type" class="form-control" value="<?= htmlspecialchars($form['group_type'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Chuyên môn</label>
                            <input type="text" name="specialty" class="form-control" value="<?= htmlspecialchars($form['specialty'] ?? '') ?>">
                        </div>

                        <div class="col-12 text-end">
                            <button class="btn btn-primary">Cập nhật</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

view('layouts.AdminLayout', [
    'title' => 'Sửa Hướng dẫn viên',
    'pageTitle' => 'Sửa HDV',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Dashboard', 'url' => BASE_URL . 'home'],
        ['label' => 'Guides', 'url' => BASE_URL . 'guides'],
        ['label' => 'Edit', 'active' => true],
    ],
]);