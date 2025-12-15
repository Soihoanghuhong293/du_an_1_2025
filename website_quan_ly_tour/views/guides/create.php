<?php ob_start(); ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/index.css">

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 fw-bold text-dark">Thêm Hướng dẫn viên</h3>
                <p class="text-muted mb-0">Tạo hồ sơ hướng dẫn viên mới</p>
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

                <form action="index.php?act=guides/store" method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Người dùng (User)</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">-- Chọn người dùng --</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= (isset($old['user_id']) && $old['user_id'] == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['name'] . ' <' . $u['email'] . '>') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="birthdate" class="form-control" value="<?= htmlspecialchars($old['birthdate'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ảnh đại diện</label>
                            <input type="file" name="avatar" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Điện thoại</label>
                            <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Chứng chỉ</label>
                            <input type="text" name="certificate" class="form-control" value="<?= htmlspecialchars($old['certificate'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ngôn ngữ</label>
                            <input type="text" name="languages" class="form-control" placeholder="Ví dụ: Tiếng Anh, Tiếng Việt" value="<?= htmlspecialchars($old['languages'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Kinh nghiệm</label>
                            <input type="text" name="experience" class="form-control" placeholder="Ví dụ: 5 năm kinh nghiệm" value="<?= htmlspecialchars($old['experience'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Lịch sử (History) - Chọn các tour liên quan</label>
                            <select name="history_tours[]" class="form-select" multiple size="6">
                                <?php
                                    $selectedTours = $old['history_tours'] ?? [];
                                    // if old not present but history text is JSON, try decode
                                    if (empty($selectedTours) && !empty($old['history'])) {
                                        $tmp = json_decode($old['history'], true);
                                        if (json_last_error() === JSON_ERROR_NONE && isset($tmp['tours']) && is_array($tmp['tours'])) {
                                            $selectedTours = $tmp['tours'];
                                        }
                                    }
                                    foreach ($tours as $t):
                                ?>
                                    <option value="<?= $t['id'] ?>" <?= in_array($t['id'], $selectedTours) ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Rating</label>
                            <input type="number" step="0.1" name="rating" class="form-control" value="<?= htmlspecialchars($old['rating'] ?? '0') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tình trạng sức khỏe</label>
                            <input type="text" name="health_status" class="form-control" value="<?= htmlspecialchars($old['health_status'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Group type</label>
                            <input type="text" name="group_type" class="form-control" value="<?= htmlspecialchars($old['group_type'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Chuyên môn</label>
                            <input type="text" name="specialty" class="form-control" value="<?= htmlspecialchars($old['specialty'] ?? '') ?>">
                        </div>

                        <div class="col-12 text-end">
                            <button class="btn btn-primary">Lưu</button>
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
    'title' => 'Thêm Hướng dẫn viên',
    'pageTitle' => 'Thêm hướng dẫn viên',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Dashboard', 'url' => BASE_URL . 'home'],
        ['label' => 'Guides', 'url' => BASE_URL . 'guides'],
        ['label' => 'Create', 'active' => true],
    ],
]);