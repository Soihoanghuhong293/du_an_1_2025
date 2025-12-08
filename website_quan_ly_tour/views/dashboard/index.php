<?php 
ob_start(); 
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row">
    <div class="col-12">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(45deg, #4e73df, #224abe);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-white-50 text-uppercase fw-bold">Doanh thu (Năm nay)</div>
                                <div class="h3 mb-0 fw-bold mt-1"><?= number_format($summary['revenue']) ?> đ</div>
                            </div>
                            <i class="bi bi-currency-dollar fs-1 text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(45deg, #1cc88a, #13855c);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-white-50 text-uppercase fw-bold">Lợi nhuận ước tính</div>
                                <div class="h3 mb-0 fw-bold mt-1"><?= number_format($summary['profit']) ?> đ</div>
                            </div>
                            <i class="bi bi-graph-up-arrow fs-1 text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-white border-start border-4 border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-info text-uppercase fw-bold">Tổng Booking</div>
                                <div class="h3 mb-0 fw-bold text-dark mt-1"><?= $summary['bookings'] ?></div>
                            </div>
                            <i class="bi bi-calendar-check fs-1 text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-white border-start border-4 border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-warning text-uppercase fw-bold">Khách phục vụ</div>
                                <div class="h3 mb-0 fw-bold text-dark mt-1"><?= $summary['guests'] ?></div>
                            </div>
                            <i class="bi bi-people-fill fs-1 text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h3 class="card-title fw-bold text-primary">
                            <i class="bi bi-bar-chart-line me-2"></i>Biểu đồ doanh thu theo tháng
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="bi bi-dash-lg"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 350px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h3 class="card-title fw-bold text-primary">
                            <i class="bi bi-pie-chart me-2"></i>Tỷ lệ Booking
                        </h3>
                        <div class="card-tools">
                             <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="bi bi-dash-lg"></i></button>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div style="width: 100%; max-width: 300px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- Chart 1: Doanh thu ---
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'bar', 
            data: {
                labels: <?= $chartRevenue['labels'] ?>,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: <?= $chartRevenue['data'] ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    hoverBackgroundColor: '#2e59d9',
                    borderColor: '#4e73df',
                    borderWidth: 1,
                    borderRadius: 4,
                    barThickness: 40,
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [2], color: "#e3e6f0" },
                        ticks: { callback: function(value) { return value.toLocaleString() + ' đ'; } }
                    },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString() + ' VNĐ';
                            }
                        }
                    }
                }
            }
        });

        // --- Chart 2: Trạng thái ---
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: <?= $chartStatus['labels'] ?>,
                datasets: [{
                    data: <?= $chartStatus['data'] ?>,
                    backgroundColor: ['#f6c23e', '#36b9cc', '#1cc88a', '#e74a3b', '#858796'], 
                    hoverOffset: 4
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>

<?php
// Lấy toàn bộ nội dung vừa tạo từ bộ đệm
$content = ob_get_clean();

view('layouts.AdminLayout', [
    'title' => 'Dashboard - Website Quản Lý Tour',
    'pageTitle' => 'Thống kê tổng quan', 
    'content' => $content, 
    'breadcrumb' => [
        ['label' => 'Trang chủ', 'url' => BASE_URL . 'home', 'active' => false],
        ['label' => 'Thống kê', 'url' => BASE_URL . 'index.php?act=dashboard', 'active' => true],
    ],
]);
?>