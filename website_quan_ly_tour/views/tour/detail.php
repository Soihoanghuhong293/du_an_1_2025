<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/show.css">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><?= htmlspecialchars($tour['name']) ?></h4>
        <div class="text-muted">Mã Tour: #<?= $tour['id'] ?></div>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?act=tours" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
        <a href="index.php?act=tour-edit&id=<?= $tour['id'] ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil"></i> Sửa Tour
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">

        <!-- Tabs -->
        <div class="card card-modern">
            <div class="card-header border-0 pb-0">
                <ul class="nav nav-tabs nav-tabs-modern card-header-tabs">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#info">Thông tin</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#images">Hình ảnh</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#schedule">Lịch trình</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#prices">Giá chi tiết</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#policies">Chính sách</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#suppliers">Nhà cung cấp</button></li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content pt-2">

                    <!-- INFO TAB -->
                    <div class="tab-pane fade show active" id="info">
                        <p><strong>Danh mục:</strong> <?= $tour['category_id'] ?></p>
                        <p><strong>Giá:</strong> <?= number_format($tour['price']) ?> VNĐ</p>
                        <p><strong>Mô tả:</strong><br><?= nl2br(htmlspecialchars($tour['description'])) ?></p>
                    </div>

                    <!-- IMAGES -->
                    <div class="tab-pane fade" id="images">
                        <?php if (!empty($tour['images'])): ?>
                            <div class="row g-3">
                                <?php foreach ($tour['images'] as $img): ?>
                                    <div class="col-6 col-md-4">
                                        <img src="<?= BASE_URL ?>/public/dist/assets/img/<?= htmlspecialchars($img) ?>" 
                                            class="img-fluid rounded shadow-sm border">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted fst-italic">Chưa có hình.</p>
                        <?php endif; ?>
                    </div>

                    <!-- JSON TABS -->
                    <div class="tab-pane fade" id="schedule">
                        <?php if (!empty($tour['schedule']['days'])): ?>
                        <?php foreach ($tour['schedule']['days'] as $day): ?>
                        <div class="mb-3 p-3 border rounded shadow-sm">
                            <h6 class="fw-bold text-primary"><?= $day['title'] ?> (<?= $day['date'] ?>)</h6>

                            <ul class="mt-2">
                                <?php foreach ($day['activities'] as $act): ?>
                            <li><?= htmlspecialchars($act) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted fst-italic">Chưa có lịch trình.</p>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="prices">
                    <?php if (!empty($tour['prices'])): ?>
                        <table class="table table-bordered">
                            <tr>
                                <th>Loại khách</th>
                                <th>Giá</th>
                            </tr>

                            <?php if (isset($tour['prices']['adult'])): ?>
                            <tr>
                                <td>Người lớn</td>
                                <td><?= number_format($tour['prices']['adult']) ?> VNĐ</td>
                            </tr>
                            <?php endif; ?>

                            <?php if (isset($tour['prices']['child'])): ?>
                            <tr>
                                <td>Trẻ em</td>
                                <td><?= number_format($tour['prices']['child']) ?> VNĐ</td>
                            </tr>
                            <?php endif; ?>

                            <?php if (isset($tour['prices']['baby'])): ?>
                            <tr>
                                <td>Em bé</td>
                                <td><?= number_format($tour['prices']['baby']) ?> VNĐ</td>
                            </tr>
                            <?php endif; ?>

                        </table>
                        <?php else: ?>
                            <p class="text-muted fst-italic">Không có dữ liệu giá.</p>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="policies">
                    <?php if (!empty($tour['policies'])): ?>
                        <p><strong>Chính sách đặt/hủy:</strong></p>
                        <p><?= htmlspecialchars($tour['policies']['booking']) ?></p>

                        <p><strong>Bao gồm:</strong></p>
                        <ul>
                            <?php foreach ($tour['policies']['include'] as $item): ?>
                                <li><?= htmlspecialchars($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted fst-italic">Không có chính sách.</p>
                    <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="suppliers">
                    <?php if (!empty($tour['suppliers'])): ?>
                        <?php foreach ($tour['suppliers'] as $sup): ?>
                            <span class="badge bg-secondary me-1"><?= htmlspecialchars($sup) ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted fst-italic">Không có nhà cung cấp.</p>
                    <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="col-md-4">

        <div class="card card-modern">
            <div class="card-header-modern">
                <span><i class="bi bi-info-circle text-info me-2"></i> Thông tin chung</span>
            </div>
            <div class="card-body">
                <p><strong>Ngày tạo:</strong> <?= $tour['created_at'] ?></p>
                <p><strong>Ngày cập nhật:</strong> <?= $tour['updated_at'] ?></p>
                <p><strong>Trạng thái:</strong> <?= $tour['status'] ? 'Hiển thị' : 'Ẩn' ?></p>
            </div>
        </div>

    </div>
</div>
