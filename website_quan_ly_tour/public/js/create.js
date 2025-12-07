
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
