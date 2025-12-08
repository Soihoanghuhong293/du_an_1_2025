
document.addEventListener('DOMContentLoaded', function() {
    const tourSelect = document.getElementById('select_tour');
    const scheduleTextarea = document.getElementById('schedule_detail');
    const serviceTextarea = document.getElementById('service_detail');

    tourSelect.addEventListener('change', function() {
        const tourId = this.value;

        if (tourId) {
            // Hiển thị trạng thái đang tải
            scheduleTextarea.value = "Đang tải dữ liệu...";
            
            // Gọi AJAX lấy dữ liệu
            fetch('index.php?act=api-get-tour-info', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'tour_id=' + tourId
            })
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    const tourData = res.data;

                    
                    let formattedSchedule = "";
                    try {
                        const scheduleObj = JSON.parse(tourData.schedule);
                        if (scheduleObj && scheduleObj.days) {
                            scheduleObj.days.forEach((day, index) => {
                                formattedSchedule += `Ngày ${index + 1} (${day.date || 'Tự túc'}):\n`;
                                if (Array.isArray(day.activities)) {
                                    day.activities.forEach(act => {
                                        formattedSchedule += `- ${act}\n`;
                                    });
                                }
                                formattedSchedule += "\n--------------------\n\n";
                            });
                        } else {
                            // Nếu không phải cấu trúc days thì gán thẳng
                            formattedSchedule = tourData.schedule; 
                        }
                    } catch (e) {
                        // Nếu trong DB lưu text thường chứ không phải JSON
                        formattedSchedule = tourData.schedule;
                    }
                    scheduleTextarea.value = formattedSchedule;


                    
                    serviceTextarea.value = tourData.description || "";

                } else {
                    alert('Không tìm thấy thông tin tour');
                    scheduleTextarea.value = "";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                scheduleTextarea.value = "Lỗi khi tải dữ liệu.";
            });
        } else {
            scheduleTextarea.value = "";
            serviceTextarea.value = "";
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    const guideSelect = document.querySelector('select[name="guide_id"]');
    
    // Hàm gọi API kiểm tra HDV
    function checkAvailableGuides() {
        const sDate = startDateInput.value;
        const eDate = endDateInput.value;

        // Chỉ chạy khi có đủ cả ngày bắt đầu và kết thúc
        if (!sDate || !eDate) return;

        // 1. Hiển thị trạng thái đang tải
        guideSelect.innerHTML = '<option value="">-- Đang kiểm tra lịch... --</option>';
        guideSelect.disabled = true;

        // 2. Gọi AJAX
        fetch('index.php?act=api-get-available-guides', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `start_date=${sDate}&end_date=${eDate}`
        })
        .then(response => response.json())
        .then(res => {
            guideSelect.disabled = false;
            guideSelect.innerHTML = '<option value="">-- Chọn HDV (Đã lọc trùng lịch) --</option>';
            
            if (res.status === 'success' && res.data.length > 0) {
                // 3. Đổ dữ liệu HDV rảnh vào select
                res.data.forEach(guide => {
                    const option = document.createElement('option');
                    option.value = guide.id;
                    option.textContent = guide.name;
                    guideSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.textContent = "-- Tất cả HDV đều bận --";
                option.disabled = true;
                guideSelect.appendChild(option);
            }
        })
        .catch(err => {
            console.error(err);
            guideSelect.disabled = false;
            guideSelect.innerHTML = '<option value="">-- Lỗi kiểm tra --</option>';
        });
    }

    // Gắn sự kiện: Khi thay đổi ngày bắt đầu hoặc ngày kết thúc thì check lại
    startDateInput.addEventListener('change', checkAvailableGuides);
    endDateInput.addEventListener('change', checkAvailableGuides);
});
document.addEventListener('DOMContentLoaded', function() {
    const tourSelect = document.getElementById('select_tour');
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    
    // Biến lưu số ngày của tour (Mặc định 1 ngày)
    let tourDuration = 1; 

    // 1. KHI CHỌN TOUR -> Lấy số ngày từ Server
    tourSelect.addEventListener('change', function() {
        const tourId = this.value;
        if (tourId) {
            fetch('index.php?act=api-get-tour-info', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'tour_id=' + tourId
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    // Lưu số ngày vào biến
                    tourDuration = parseInt(res.data.days) || 1;
                    
                    // Nếu người dùng đã chọn ngày đi trước đó -> Tính lại ngày về ngay lập tức
                    if (startDateInput.value) {
                        calculateEndDate();
                    }
                }
            });
        }
    });

    // 2. KHI CHỌN NGÀY ĐI -> Tự động tính ngày về
    startDateInput.addEventListener('change', function() {
        calculateEndDate();
    });

    // Hàm tính toán: Ngày về = Ngày đi + (Số ngày tour - 1)
    function calculateEndDate() {
        if (!startDateInput.value) return;

        const start = new Date(startDateInput.value);
        const end = new Date(start);

        // Ví dụ: Đi 3 ngày. Bắt đầu mùng 1.
        // Kết thúc = Mùng 1 + (3 - 1) = Mùng 3. (Đi 1, 2, 3)
        end.setDate(start.getDate() + (tourDuration - 1));

        // Format ra YYYY-MM-DD để gán vào input
        const yyyy = end.getFullYear();
        const mm = String(end.getMonth() + 1).padStart(2, '0');
        const dd = String(end.getDate()).padStart(2, '0');

        endDateInput.value = `${yyyy}-${mm}-${dd}`;
    }
});