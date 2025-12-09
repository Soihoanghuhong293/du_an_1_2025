<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="m-0 fw-bold">Thêm Tour Mới</h1>
                <a href="index.php?act=tours" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger shadow-sm mb-4">
                    <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Đã có lỗi xảy ra:</h6>
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $error) echo "<li>$error</li>"; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="index.php?act=tour-create" method="POST" enctype="multipart/form-data">
                <div class="row">
                    
                    <div class="col-lg-8">
                        <div class="card card-primary card-outline shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title fw-bold m-0 text-primary"><i class="bi bi-info-circle me-2"></i>Thông tin chung</h5>
                            </div>
                            <div class="card-body">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tên Tour <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required placeholder="Nhập tên tour...">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-select" required>
                                            <option value="">-- Chọn danh mục --</option>
                                            <?php if (!empty($categories)): foreach ($categories as $cate): ?>
                                                <option value="<?= $cate['id'] ?>" <?= (($_POST['category_id'] ?? '') == $cate['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cate['name']) ?>
                                                </option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Thời lượng (Ngày)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="duration_days" value="<?= htmlspecialchars($_POST['duration_days'] ?? 1) ?>" min="1">
                                            <span class="input-group-text">Ngày</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mô tả / Giới thiệu</label>
                                    <textarea class="form-control" name="description" rows="5" placeholder="Mô tả ngắn về tour..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Lịch trình chi tiết</label>
                                    <textarea class="form-control" name="schedule_text" rows="8" placeholder="Ngày 1: ... &#10;Ngày 2: ..."><?= htmlspecialchars($_POST['schedule_text'] ?? '') ?></textarea>
                                    <small class="text-muted"><i class="bi bi-info-circle"></i> Nhập nội dung chi tiết. Hệ thống sẽ tự động lưu.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Chính sách & Điều khoản</label>
                                    <textarea class="form-control" name="policy_text" rows="4" placeholder="Chính sách hoàn hủy, bao gồm/không bao gồm..."><?= htmlspecialchars($_POST['policy_text'] ?? '') ?></textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        
                        <div class="card card-success card-outline shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title fw-bold m-0 text-success"><i class="bi bi-gear-fill me-2"></i>Cấu hình & Giá</h5>
                            </div>
                            <div class="card-body">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Trạng thái</label>
                                    <select name="status" class="form-select">
                                        <option value="1" <?= (($_POST['status'] ?? 1) == 1) ? 'selected' : '' ?>>Hoạt động</option>
                                        <option value="0" <?= (($_POST['status'] ?? 1) == 0) ? 'selected' : '' ?>>Tạm ẩn</option>
                                    </select>
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-success">Giá Cơ Bản (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control fw-bold text-success border-success" name="price" value="<?= htmlspecialchars($_POST['price'] ?? 0) ?>" required min="0">
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Giá Người lớn</label>
                                        <input type="number" class="form-control form-control-sm" name="prices[adult]" value="<?= htmlspecialchars($_POST['prices']['adult'] ?? 0) ?>" min="0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Giá Trẻ em</label>
                                        <input type="number" class="form-control form-control-sm" name="prices[child]" value="<?= htmlspecialchars($_POST['prices']['child'] ?? 0) ?>" min="0">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card card-info card-outline shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title fw-bold m-0 text-info"><i class="bi bi-images me-2"></i>Hình ảnh & NCC</h5>
                            </div>
                            <div class="card-body">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nhà cung cấp</label>
                                    <input type="text" class="form-control" name="suppliers_text" value="<?= htmlspecialchars($_POST['suppliers_text'] ?? '') ?>" placeholder="NCC A, NCC B...">
                                    <small class="text-muted d-block mt-1">Phân tách bằng dấu phẩy (,)</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ảnh Tour</label>
                                    <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                                    <small class="text-muted d-block mt-1">Chọn nhiều ảnh cùng lúc</small>
                                </div>

                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                                <i class="bi bi-plus-lg me-2"></i> TẠO TOUR MỚI
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </section>
</div>