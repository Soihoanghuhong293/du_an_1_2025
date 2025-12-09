document.addEventListener('DOMContentLoaded', function() {
    // =========================================================================
    // 1. KHAI BÁO CÁC DOM ELEMENT (Lấy tất cả các ô input cần thiết)
    // =========================================================================
    const tourSelect = document.getElementById('select_tour');
    const scheduleTextarea = document.getElementById('schedule_detail');
    const serviceTextarea = document.getElementById('service_detail');
    
    // Ngày tháng
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    
    // Hướng dẫn viên
    const guideSelect = document.querySelector('select[name="guide_id"]');

    // Giá tiền & Số lượng (Phần mới thêm)
    const numAdultsInput = document.getElementById('num_adults');
    const numChildrenInput = document.getElementById('num_children');
    const totalPriceInput = document.getElementById('total_price');
    const displayAdultPrice = document.getElementById('price_adult_display');
    const displayChildPrice = document.getElementById('price_child_display');

    // =========================================================================
    // 2. BIẾN TOÀN CỤC (Lưu trữ dữ liệu tạm để tính toán)
    // =========================================================================
    let currentTourDuration = 1; // Mặc định 1 ngày
    let currentPriceAdult = 0;   // Giá người lớn
    let currentPriceChild = 0;   // Giá trẻ em

    // =========================================================================
    // 3. SỰ KIỆN KHI CHỌN TOUR (Xử lý chính)
    // =========================================================================
    tourSelect.addEventListener('change', function() {
        const tourId = this.value;

        if (tourId) {
            // Hiệu ứng loading
            scheduleTextarea.value = "Đang tải dữ liệu...";
            
            // Gọi API lấy thông tin Tour
            fetch('index.php?act=api-get-tour-info', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'tour_id=' + tourId
            })
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    const data = res.data;

                    // A. Xử lý Lịch trình & Dịch vụ
                    fillScheduleAndService(data, scheduleTextarea, serviceTextarea);

                    // B. Lưu số ngày tour để tính ngày kết thúc
                    currentTourDuration = parseInt(data.days) || 1;

                    // C. Lưu Giá tiền vào biến toàn cục
                    currentPriceAdult = parseFloat(data.price_adult) || 0;
                    currentPriceChild = parseFloat(data.price_child) || 0;

                    // D. Hiển thị đơn giá ra màn hình cho user thấy
                    if (displayAdultPrice) displayAdultPrice.textContent = `Đơn giá: ${currentPriceAdult.toLocaleString('vi-VN')} đ`;
                    if (displayChildPrice) displayChildPrice.textContent = `Đơn giá: ${currentPriceChild.toLocaleString('vi-VN')} đ`;

                    // E. Logic tự động
                    if (startDateInput.value) {
                        calculateEndDate(); // Tính ngày về
                    }
                    calculateTotal(); // Tính lại tổng tiền ngay
                }
            })
            .catch(error => {
                console.error('Error:', error);
                scheduleTextarea.value = "Lỗi khi tải dữ liệu.";
            });
        } else {
            // Reset nếu bỏ chọn
            scheduleTextarea.value = "";
            serviceTextarea.value = "";
            currentPriceAdult = 0;
            currentPriceChild = 0;
            calculateTotal();
        }
    });

    // =========================================================================
    // 4. SỰ KIỆN NGÀY THÁNG (Tự động tính ngày về & Check HDV)
    // =========================================================================
    startDateInput.addEventListener('change', function() {
        calculateEndDate(); // Khi chọn ngày đi -> Tự tính ngày về
        // Lưu ý: Trong hàm calculateEndDate đã gọi checkAvailableGuides rồi
    });

    // Nếu sửa tay ngày kết thúc cũng check lại HDV
    endDateInput.addEventListener('change', function() {
        checkAvailableGuides(); 
    });

    // =========================================================================
    // 5. SỰ KIỆN TÍNH TIỀN (Khi thay đổi số lượng khách)
    // =========================================================================
    if(numAdultsInput) numAdultsInput.addEventListener('input', calculateTotal);
    if(numChildrenInput) numChildrenInput.addEventListener('input', calculateTotal);

    // =========================================================================
    // 6. CÁC HÀM XỬ LÝ LOGIC (HELPER FUNCTIONS)
    // =========================================================================

    // --- Hàm tính tổng tiền ---
    function calculateTotal() {
        // Lấy số lượng, nếu ô trống thì coi là 0
        const adults = parseInt(numAdultsInput.value) || 0;
        const children = parseInt(numChildrenInput.value) || 0;

        // Công thức: (Người lớn * Giá) + (Trẻ em * Giá)
        const total = (adults * currentPriceAdult) + (children * currentPriceChild);

        // Hiển thị vào ô input (Input number không hỗ trợ dấu phẩy, chỉ hiện số thô)
        if (totalPriceInput) {
            totalPriceInput.value = total; 
        }
    }

    // --- Hàm tính ngày kết thúc ---
    function calculateEndDate() {
        if (!startDateInput.value) return;

        const start = new Date(startDateInput.value);
        const end = new Date(start);

        // Công thức: Ngày đi + (Duration - 1)
        end.setDate(start.getDate() + (currentTourDuration - 1));

        // Format YYYY-MM-DD
        const yyyy = end.getFullYear();
        const mm = String(end.getMonth() + 1).padStart(2, '0');
        const dd = String(end.getDate()).padStart(2, '0');

        endDateInput.value = `${yyyy}-${mm}-${dd}`;

        // Sau khi điền ngày xong -> Check HDV ngay lập tức
        checkAvailableGuides();
    }

    // --- Hàm check HDV trùng lịch ---
    function checkAvailableGuides() {
        const sDate = startDateInput.value;
        const eDate = endDateInput.value;

        if (!sDate || !eDate) return;

        guideSelect.innerHTML = '<option value="">-- Đang kiểm tra lịch... --</option>';
        guideSelect.disabled = true;

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

    // --- Hàm format lịch trình (Xử lý JSON hoặc Text thường) ---
    function fillScheduleAndService(tourData, scheduleEl, serviceEl) {
        let formattedSchedule = "";
        try {
            const scheduleObj = JSON.parse(tourData.schedule);
            if (scheduleObj && scheduleObj.days) {
                scheduleObj.days.forEach((day, index) => {
                    formattedSchedule += `Ngày ${index + 1} (${day.date || 'Tự túc'}):\n`;
                    if (Array.isArray(day.activities)) {
                        day.activities.forEach(act => formattedSchedule += `- ${act}\n`);
                    }
                    formattedSchedule += "\n--------------------\n\n";
                });
            } else {
                formattedSchedule = tourData.schedule;
            }
        } catch (e) {
            formattedSchedule = tourData.schedule;
        }
        scheduleEl.value = formattedSchedule;
        serviceEl.value = tourData.description || ""; // Hoặc tourData.service_detail tùy DB
    }
});