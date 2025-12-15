<?php
$pageTitle = 'Hồ sơ cá nhân';
ob_start();
$isEditMode = isset($_GET['edit']) && $_GET['edit'] == '1';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <?php if (!$isEditMode): ?>
                <!-- View Mode -->
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-circle"></i> Hồ sơ cá nhân
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="profile-avatar" style="width: 100px; height: 100px; margin: 0 auto;">
                                <?php if ($guideProfile && !empty($guideProfile->avatar)): ?>
                                    <img src="<?= htmlspecialchars($guideProfile->avatar) ?>" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;" alt="Avatar">
                                <?php else: ?>
                                    <i class="fas fa-user-circle" style="font-size: 100px; color: #007bff;"></i>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="profile-info">
                            <!-- Bảng thông tin từ database -->
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th class="bg-light">Tên:</th>
                                        <td><?= htmlspecialchars($user->name) ?></td>
                                        <th class="bg-light">Email:</th>
                                        <td><?= htmlspecialchars($user->email) ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Vai trò:</th>
                                        <td>
                                            <?php
                                            $roleLabels = [
                                                'admin' => 'Quản trị viên',
                                                'guide' => 'Hướng dẫn viên',
                                                'user' => 'Khách hàng'
                                            ];
                                            $roleLabel = $roleLabels[$user->role] ?? $user->role;
                                            ?>
                                            <span class="badge bg-info"><?= htmlspecialchars($roleLabel) ?></span>
                                        </td>
                                        <th class="bg-light">Trạng thái:</th>
                                        <td>
                                            <?php if ($user->status): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Hoạt động
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle"></i> Khóa
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <?php if ($guideProfile): ?>
                                <hr>
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle"></i> Thông tin chi tiết
                                </h5>
                                
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th class="bg-light">Ngày sinh:</th>
                                            <td><?= $guideProfile->birthdate ? date('d/m/Y', strtotime($guideProfile->birthdate)) : 'N/A' ?></td>
                                            <th class="bg-light">Điện thoại:</th>
                                            <td><?= htmlspecialchars($guideProfile->phone ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Chứng chỉ:</th>
                                            <td><?= htmlspecialchars($guideProfile->certificate ?? 'N/A') ?></td>
                                            <th class="bg-light">Ngôn ngữ:</th>
                                            <td><?= htmlspecialchars($guideProfile->languages ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Kinh nghiệm:</th>
                                            <td><?= htmlspecialchars($guideProfile->experience ?? 'N/A') ?></td>
                                            <th class="bg-light">Sức khỏe:</th>
                                            <td><?= htmlspecialchars($guideProfile->health_status ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Loại nhóm:</th>
                                            <td><?= htmlspecialchars($guideProfile->group_type ?? 'N/A') ?></td>
                                            <th class="bg-light">Chuyên môn:</th>
                                            <td><?= htmlspecialchars($guideProfile->specialty ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Đánh giá:</th>
                                            <td colspan="3">
                                                <span class="badge bg-warning text-dark">⭐ <?= number_format($guideProfile->rating ?? 0, 1) ?>/5</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Lịch sử:</th>
                                            <td colspan="3"><?= nl2br(htmlspecialchars($guideProfile->history ?? 'N/A')) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php endif; ?>

                            <hr>

                            <div class="d-grid gap-2">
                                <a href="<?= BASE_URL ?>profile?edit=1" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Chỉnh sửa hồ sơ
                                </a>
                                <a href="<?= BASE_URL ?>home" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Edit Mode -->
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">
                            <i class="fas fa-edit"></i> Chỉnh sửa hồ sơ
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>profile-update" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $user->id ?>">

                            <h5 class="text-primary mb-3">Thông tin cơ bản</h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Tên:</strong></label>
                                    <input type="text" name="name" class="form-control" placeholder="Tên đầy đủ" value="<?= htmlspecialchars($user->name) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Email:</strong></label>
                                    <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($user->email) ?>" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Vai trò:</strong></label>
                                    <select name="role" class="form-select" required>
                                        <option value="">-- Chọn vai trò --</option>
                                        <option value="guide" <?= $user->role === 'guide' ? 'selected' : '' ?>>Hướng dẫn viên</option>
                                        <option value="user" <?= $user->role === 'user' ? 'selected' : '' ?>>Khách hàng</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Mật khẩu mới:</strong></label>
                                    <input type="password" name="password" class="form-control" placeholder="Để trống nếu không đổi">
                                </div>
                            </div>

                            <?php if ($user->role === 'guide'): ?>
                                <hr>
                                <h5 class="text-primary mb-3">Thông tin hướng dẫn viên</h5>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Ngày sinh:</strong></label>
                                        <input type="date" name="birthdate" class="form-control" value="<?= $guideProfile && $guideProfile->birthdate ? substr($guideProfile->birthdate, 0, 10) : '' ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Điện thoại:</strong></label>
                                        <input type="tel" name="phone" class="form-control" placeholder="Số điện thoại" value="<?= $guideProfile ? htmlspecialchars($guideProfile->phone ?? '') : '' ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Avatar:</strong></label>
                                        <input type="text" name="avatar" class="form-control" placeholder="URL ảnh đại diện" value="<?= $guideProfile ? htmlspecialchars($guideProfile->avatar ?? '') : '' ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Chứng chỉ:</strong></label>
                                        <input type="text" name="certificate" class="form-control" placeholder="Chứng chỉ hướng dẫn viên" value="<?= $guideProfile ? htmlspecialchars($guideProfile->certificate ?? '') : '' ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Ngôn ngữ:</strong></label>
                                        <input type="text" name="languages" class="form-control" placeholder="Ví dụ: Tiếng Việt, Tiếng Anh" value="<?= $guideProfile ? htmlspecialchars($guideProfile->languages ?? '') : '' ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Kinh nghiệm:</strong></label>
                                        <input type="text" name="experience" class="form-control" placeholder="Ví dụ: 5 năm kinh nghiệm" value="<?= $guideProfile ? htmlspecialchars($guideProfile->experience ?? '') : '' ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Sức khỏe:</strong></label>
                                        <input type="text" name="health_status" class="form-control" placeholder="Tình trạng sức khỏe" value="<?= $guideProfile ? htmlspecialchars($guideProfile->health_status ?? '') : '' ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Loại nhóm:</strong></label>
                                        <input type="text" name="group_type" class="form-control" placeholder="Loại nhóm khách" value="<?= $guideProfile ? htmlspecialchars($guideProfile->group_type ?? '') : '' ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Chuyên môn:</strong></label>
                                    <input type="text" name="specialty" class="form-control" placeholder="Chuyên môn hướng dẫn" value="<?= $guideProfile ? htmlspecialchars($guideProfile->specialty ?? '') : '' ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Lịch sử:</strong></label>
                                    <textarea name="history" class="form-control" rows="3" placeholder="Lịch sử hướng dẫn"><?= $guideProfile ? htmlspecialchars($guideProfile->history ?? '') : '' ?></textarea>
                                </div>
                            <?php endif; ?>

                            <hr>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Lưu thay đổi
                                </button>
                                <a href="<?= BASE_URL ?>profile" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Hủy
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../views/layouts/AdminLayout.php';
?>