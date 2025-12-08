<?php
// src/controllers/DashboardController.php
require_once BASE_PATH . '/src/models/DashboardModel.php';

class DashboardController {
    public function index() {
        // Lấy dữ liệu
        $summary = DashboardModel::getSummary();
        $revenueData = DashboardModel::getRevenueByMonth();
        $statusData = DashboardModel::getStatusRatio();

        // Chuẩn bị dữ liệu cho Biểu đồ Doanh thu (ChartJS cần 2 mảng: labels và data)
        $months = [];
        $revenues = [];
        // Khởi tạo 12 tháng bằng 0
        for ($i=1; $i<=12; $i++) {
            $months[] = "Tháng $i";
            $revenues[$i] = 0;
        }
        // Gán dữ liệu thực tế vào
        foreach ($revenueData as $item) {
            $revenues[$item['month']] = (float)$item['total'];
        }

        // Chuẩn bị dữ liệu cho Biểu đồ Trạng thái
        $statusLabels = [];
        $statusCounts = [];
        foreach ($statusData as $item) {
            $statusLabels[] = $item['status_name'];
            $statusCounts[] = $item['count'];
        }

        view('dashboard.index', [
            'summary' => $summary,
            'chartRevenue' => [
                'labels' => json_encode($months),
                'data'   => json_encode(array_values($revenues))
            ],
            'chartStatus' => [
                'labels' => json_encode($statusLabels),
                'data'   => json_encode($statusCounts)
            ],
            'title' => 'Tổng quan hệ thống'
        ]);
    }
}