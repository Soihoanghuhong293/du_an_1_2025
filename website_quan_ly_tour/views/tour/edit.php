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
                        
                        <form role="form" method="POST" action="index.php?act=tour-edit&id=<?php echo $tour['id'] ?? ''; ?>">
                            <div class="card-body">
                                
                                <div class="mb-3">
                                    <label for="ten_tour" class="form-label">Tên Tour (*)</label>
                                    <input type="text" class="form-control" id="ten_tour" name="ten_tour" required
                                           value="<?php echo htmlspecialchars($tour['ten_tour'] ?? ''); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="diem_den" class="form-label">Điểm Đến</label>
                                    <input type="text" class="form-control" id="diem_den" name="diem_den"
                                           value="<?php echo htmlspecialchars($tour['diem_den'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="gia" class="form-label">Giá (VNĐ) (*)</label>
                                    <input type="number" class="form-control" id="gia" name="gia" required min="0"
                                           value="<?php echo htmlspecialchars($tour['gia'] ?? 0); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="ngay_khoi_hanh" class="form-label">Ngày Khởi Hành (*)</label>
                                    <input type="date" class="form-control" id="ngay_khoi_hanh" name="ngay_khoi_hanh" required
                                           value="<?php echo htmlspecialchars($tour['ngay_khoi_hanh'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="mo_ta" class="form-label">Mô Tả</label>
                                    <textarea class="form-control" id="mo_ta" name="mo_ta" rows="5"><?php echo htmlspecialchars($tour['mo_ta'] ?? ''); ?></textarea>
                                </div>
                                
                                
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