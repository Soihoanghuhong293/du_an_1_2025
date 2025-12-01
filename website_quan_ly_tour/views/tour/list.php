<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Quản Lý Tour</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title h5 mb-0">Danh Sách Tour</h3>
                            <a href="index.php?act=tour-add" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Thêm Tour Mới
                            </a>
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên Tour</th>
                                            <th>Giá</th>
                                            <th>Ngày Khởi Hành</th>
                                            <th style="width: 150px;">Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($tours)): ?>
                                            <?php foreach ($tours as $tour): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($tour['id'] ?? ''); ?></td>
                                                
                                                <td><?php echo htmlspecialchars($tour['ten_tour'] ?? ''); ?></td>
                                                
                                                <td><?php echo number_format($tour['gia'] ?? 0); ?> VNĐ</td>
                                                
                                                <td><?php echo htmlspecialchars($tour['ngay_khoi_hanh'] ?? ''); ?></td>
                                                
                                                <td>
                                                    <a href="index.php?act=tour-edit&id=<?php echo $tour['id'] ?? ''; ?>" class="btn btn-warning btn-sm me-1">
                                                        <i class="fas fa-edit"></i> Sửa
                                                    </a>
                                                    <a href="index.php?act=tour-delete&id=<?php echo $tour['id'] ?? ''; ?>" 
                                                       
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa tour <?php echo htmlspecialchars($tour['ten_tour'] ?? ''); ?>?');"
                                                       class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash-alt"></i> Xóa
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4">Chưa có tour nào được thêm.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>