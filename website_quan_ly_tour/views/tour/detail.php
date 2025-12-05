<h2>Chi tiết Tour</h2>

<p><strong>Tên tour:</strong> <?= $tour['ten_tour'] ?></p>

<p><strong>Mô tả:</strong> <?= $tour['mo_ta'] ?></p>

<p><strong>Giá:</strong> <?= number_format($tour['gia'], 0, ',', '.') ?> VNĐ</p>

<p><strong>Ngày tạo:</strong> <?= $tour['created_at'] ?></p>

<a href="index.php?controller=tour&action=index" class="btn btn-secondary">Quay lại</a>

<h3>Chi tiết Tour</h3>

<p><strong>Tên tour:</strong> <?= $tour['name'] ?></p>
<p><strong>Giá:</strong> <?= number_format($tour['price']) ?> VNĐ</p>
<p><strong>Mô tả:</strong> <?= $tour['description'] ?></p>

<a href="index.php?controller=tour&action=list" class="btn btn-secondary">Quay lại</a>

