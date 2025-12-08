<h2>Chi tiết Tour</h2>

<p><strong>Tên tour:</strong> <?= $tour['name'] ?></p>

<p><strong>Giá:</strong> <?= number_format($tour['price'], 0, ',', '.') ?> VNĐ</p>

<p><strong>Mô tả:</strong> <?= $tour['description'] ?></p>

<!-- HÌNH ẢNH -->
<?php if (!empty($tour['images'])): ?>
    <h3>Hình ảnh:</h3>
    <?php foreach ($tour['images'] as $img): ?>
        <img src="uploads/<?= $img ?>" width="150" style="margin:5px;">
    <?php endforeach; ?>
<?php endif; ?>

<!-- LỊCH TRÌNH -->
<?php if (!empty($tour['schedule'])): ?>
    <h3>Lịch trình:</h3>
    <?php foreach ($tour['schedule']['days'] as $day): ?>
        <p><strong>Ngày:</strong> <?= $day['date'] ?></p>
        <ul>
            <?php foreach ($day['activities'] as $act): ?>
                <li><?= $act ?></li>
            <?php endforeach; ?>
        </ul>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>

<!-- GIÁ CHI TIẾT -->
<?php if (!empty($tour['prices'])): ?>
    <h3>Giá chi tiết:</h3>
    Người lớn: <?= number_format($tour['prices']['adult'], 0, ',', '.') ?> VNĐ <br>
    Trẻ em: <?= number_format($tour['prices']['child'], 0, ',', '.') ?> VNĐ
<?php endif; ?>

<!-- CHÍNH SÁCH -->
<?php if (!empty($tour['policies'])): ?>
    <h3>Chính sách:</h3>
    <?= $tour['policies']['booking'] ?>
<?php endif; ?>

<!-- NHÀ CUNG CẤP -->
<?php if (!empty($tour['suppliers'])): ?>
    <h3>Nhà cung cấp:</h3>
    <ul>
        <?php foreach ($tour['suppliers'] as $sup): ?>
            <li><?= $sup ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><strong>Ngày tạo:</strong> <?= $tour['created_at'] ?></p>
<p><strong>Ngày cập nhật:</strong> <?= $tour['updated_at'] ?></p>

<a href="?controller=tour" class="btn btn-secondary">Quay lại</a>
