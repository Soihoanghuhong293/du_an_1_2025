<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Sửa Tour: <?php echo htmlspecialchars($tour['ten_tour'] ?? 'Tour không tên'); ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="card card-warning shadow">
                        <div class="card-header bg-warning text-dark">
                            <h3 class="card-title h5 mb-0">Cập Nhật Thông Tin Tour</h3>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger mx-3 mt-3" role="alert">
                                <?php foreach ($errors as $error) echo "<p class='mb-1'>$error</p>"; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form role="form" method="POST" action="index.php?act=tour-edit&id=<?php echo $tour['id'] ?? ''; ?>" enctype="multipart/form-data">
                            <div class="card-body">
                                
                                <div class="mb-3">
                                    <label for="ten_tour" class="form-label">Tên Tour (*)</label>
                                    <input type="text" class="form-control" id="ten_tour" name="ten_tour" required
                                           value="<?php echo htmlspecialchars($_POST['ten_tour'] ?? $tour['name'] ?? ''); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Danh Mục Tour (*)</label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        <?php if (!empty($categories)): foreach ($categories as $cate): ?>
                                            <option value="<?= $cate['id'] ?>" <?= (($_POST['category_id'] ?? $tour['category_id'] ?? '') == $cate['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cate['name']) ?>
                                            </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="gia" class="form-label">Giá (VNĐ) (*)</label>
                                    <input type="number" class="form-control" id="gia" name="gia" required min="0"
                                           value="<?php echo htmlspecialchars($_POST['gia'] ?? $tour['price'] ?? 0); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="mo_ta" class="form-label">Mô Tả</label>
                                    <textarea class="form-control" id="mo_ta" name="mo_ta" rows="5"><?php echo htmlspecialchars($_POST['mo_ta'] ?? $tour['description'] ?? ''); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Lịch trình</label>
                                    <?php
                                        // Pre-fill textarea with first activity if structured schedule exists
                                        $scheduleText = '';
                                        if (!empty($_POST['lich_trinh'])) {
                                            $scheduleText = $_POST['lich_trinh'];
                                        } elseif (!empty($tour['lich_trinh']['days'][0]['activities'][0])) {
                                            $scheduleText = $tour['lich_trinh']['days'][0]['activities'][0];
                                        }
                                    ?>
                                    <textarea class="form-control" name="lich_trinh" rows="4"><?php echo htmlspecialchars($scheduleText); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Chính sách</label>
                                    <?php
                                        $policyText = '';
                                        if (!empty($_POST['chinh_sach'])) {
                                            $policyText = $_POST['chinh_sach'];
                                        } elseif (!empty($tour['chinh_sach']['booking'])) {
                                            $policyText = $tour['chinh_sach']['booking'];
                                        }
                                    ?>
                                    <textarea class="form-control" name="chinh_sach" rows="4"><?php echo htmlspecialchars($policyText); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Giá chi tiết</label>
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <label class="form-label small">Người lớn</label>
                                            <input type="number" class="form-control" name="prices[adult]" min="0" value="<?= htmlspecialchars($_POST['prices']['adult'] ?? $tour['gia_chi_tiet']['adult'] ?? '') ?>">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small">Trẻ em</label>
                                            <input type="number" class="form-control" name="prices[child]" min="0" value="<?= htmlspecialchars($_POST['prices']['child'] ?? $tour['gia_chi_tiet']['child'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nhà cung cấp</label>
                                    <?php
                                        // Prefill suppliers_text: prefer POST, else use existing supplier names joined by comma
                                        $prefSuppliersText = '';
                                        if (!empty($_POST['suppliers_text'])) {
                                            $prefSuppliersText = $_POST['suppliers_text'];
                                        } elseif (!empty($tour['nha_cung_cap'])) {
                                            $prefSuppliersText = implode(', ', $tour['nha_cung_cap']);
                                        }
                                    ?>
                                    <input type="text" class="form-control" name="suppliers_text" placeholder="Nhập tên nhà cung cấp, phân tách bằng dấu phẩy" value="<?= htmlspecialchars($prefSuppliersText) ?>">
                                    <small class="text-muted">Bạn có thể nhập nhiều nhà cung cấp, phân tách bằng dấu phẩy.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ảnh Tour (thêm mới)</label>
                                    <input type="file" class="form-control" name="hinh_anh[]" accept="image/*" multiple>
                                </div>

                                <?php if (!empty($tour['hinh_anh'])): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Ảnh hiện có</label>
                                        <div>
                                            <?php foreach ($tour['hinh_anh'] as $img): ?>
                                                <div style="display:inline-block;margin:4px;">
                                                    <img src="<?= htmlspecialchars($img) ?>" alt="img" style="max-width:120px;max-height:80px;display:block;" />
                                                    <small class="text-muted"><?= htmlspecialchars($img) ?></small>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                
                            </div>
                            <div class="card-footer d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-sync-alt me-1"></i> Cập Nhật Tour
                                </button>
                                <a href="index.php?act=tour" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Quay Lại
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>