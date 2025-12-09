document.addEventListener('DOMContentLoaded', function() {
    // =========================================================================
    // 1. KHAI BÁO CÁC DOM ELEMENT
    // =========================================================================
    const tourSelect = document.getElementById('select_tour');
    const scheduleTextarea = document.getElementById('schedule_detail');
    const serviceTextarea = document.getElementById('service_detail');
    
    // Ngày tháng
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    
    // Hướng dẫn viên
    const guideSelect = document.querySelector('select[name="guide_id"]');

    // Giá tiền & Số lượng
    const numAdultsInput = document.getElementById('num_adults');
    const numChildrenInput = document.getElementById('num_children');
    const totalPriceInput = document.getElementById('total_price');
    const displayAdultPrice = document.getElementById('price_adult_display');
    const displayChildPrice = document.getElementById('price_child_display');

    // =========================================================================
    // 2. BIẾN TOÀN CỤC
    // =========================================================================
    let currentTourDuration = 1; 
    let currentPriceAdult = 0;   
    let currentPriceChild = 0;   

    // =========================================================================
    // 3. SỰ KIỆN KHI CHỌN TOUR
    // =========================================================================
    tourSelect.addEventListener('change', function() {
        const tourId = this.value;

        if (tourId) {
            // Loading...
            scheduleTextarea.value = "Đang tải dữ liệu...";
            
            // Gọi API
            fetch('index.php?act=api-get-tour-info', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'tour_id=' + tourId
            })
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    const data = res.data;

                    // --- [QUAN TRỌNG] GỌI HÀM XỬ LÝ TEXT MỚI ---
                    fillScheduleAndService(data, scheduleTextarea, serviceTextarea);

                    // Các logic tính toán khác giữ nguyên
                    currentTourDuration = parseInt(data.duration_days) || parseInt(data.days) || 1;
                    currentPriceAdult = parseFloat(data.price_adult) || parseFloat(data.price) || 0;
                    currentPriceChild = parseFloat(data.price_child) || 0;

                    if (displayAdultPrice) displayAdultPrice.textContent = `Đơn giá: ${currentPriceAdult.toLocaleString('vi-VN')} đ`;
                    if (displayChildPrice) displayChildPrice.textContent = `Đơn giá: ${currentPriceChild.toLocaleString('vi-VN')} đ`;

                    if (startDateInput.value) {
                        calculateEndDate();
                    }
                    calculateTotal();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                scheduleTextarea.value = "Lỗi khi tải dữ liệu.";
            });
        } else {
            // Reset
            scheduleTextarea.value = "";
            serviceTextarea.value = "";
            currentPriceAdult = 0;
            currentPriceChild = 0;
            calculateTotal();
        }
    });

    // =========================================================================
    // 4. SỰ KIỆN NGÀY THÁNG & GIÁ (Giữ nguyên logic cũ)
    // =========================================================================
    startDateInput.addEventListener('change', function() {
        calculateEndDate(); 
    });

    endDateInput.addEventListener('change', function() {
        checkAvailableGuides(); 
    });

    if(numAdultsInput) numAdultsInput.addEventListener('input', calculateTotal);
    if(numChildrenInput) numChildrenInput.addEventListener('input', calculateTotal);

    // =========================================================================
    // 5. CÁC HÀM XỬ LÝ LOGIC (HELPER FUNCTIONS)
    // =========================================================================

    function calculateTotal() {
        const adults = parseInt(numAdultsInput.value) || 0;
        const children = parseInt(numChildrenInput.value) || 0;
        const total = (adults * currentPriceAdult) + (children * currentPriceChild);
        if (totalPriceInput) {
            totalPriceInput.value = total; 
        }
    }

    function calculateEndDate() {
        if (!startDateInput.value) return;
        const start = new Date(startDateInput.value);
        const end = new Date(start);
        end.setDate(start.getDate() + (currentTourDuration - 1));
        
        const yyyy = end.getFullYear();
        const mm = String(end.getMonth() + 1).padStart(2, '0');
        const dd = String(end.getDate()).padStart(2, '0');
        endDateInput.value = `${yyyy}-${mm}-${dd}`;
        
        checkAvailableGuides();
    }

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

    /**
     * --- [HÀM MỚI QUAN TRỌNG] ---
     * Logic đệ quy tương tự PHP để làm sạch JSON
     */
    function recursiveCleanJS(input) {
        if (!input) return '';

        // 1. Nếu là chuỗi, thử parse JSON
        if (typeof input === 'string') {
            input = input.trim();
            // Chỉ thử parse nếu trông nó giống JSON
            if (input.startsWith('{') || input.startsWith('[')) {
                try {
                    let parsed = JSON.parse(input);
                    return recursiveCleanJS(parsed); // Đệ quy tiếp
                } catch (e) {
                    return input; // Không phải JSON hợp lệ thì trả về text gốc
                }
            }
            return input;
        }

        // 2. Nếu là Mảng
        if (Array.isArray(input)) {
            return input.map(item => recursiveCleanJS(item)).join('\n');
        }

        // 3. Nếu là Object
        if (typeof input === 'object') {
            // Case A: Cấu trúc Schedule chuẩn (có 'days')
            if (input.days && Array.isArray(input.days)) {
                return input.days.map((day, index) => {
                    let dateInfo = day.date ? ` (${day.date})` : '';
                    let dayTitle = `Ngày ${index + 1}${dateInfo}:`;
                    
                    let acts = '';
                    if (day.activities) {
                        // Lấy nội dung activities
                        let rawActs = recursiveCleanJS(day.activities);
                        // Tách dòng để thêm gạch đầu dòng
                        let actLines = rawActs.split('\n');
                        acts = actLines.map(line => {
                            line = line.trim();
                            if(!line) return '';
                            return line.startsWith('-') ? line : `- ${line}`;
                        }).filter(l => l !== '').join('\n');
                    }
                    return `${dayTitle}\n${acts}`;
                }).join('\n\n');
            }

            // Case B: Object thường (policy, text, description)
            if (input.text) return recursiveCleanJS(input.text);
            if (input.description) return recursiveCleanJS(input.description);

            // Fallback: Nối các value lại
            return Object.values(input).map(val => recursiveCleanJS(val)).join('\n');
        }

        return String(input);
    }

    // --- Hàm điền dữ liệu vào form ---
    function fillScheduleAndService(tourData, scheduleEl, serviceEl) {
        // Sử dụng hàm recursiveCleanJS để xử lý dữ liệu phức tạp
        
        // 1. Xử lý Lịch trình
        let scheduleContent = recursiveCleanJS(tourData.schedule);
        scheduleEl.value = scheduleContent;

        // 2. Xử lý Dịch vụ (Lấy từ policy hoặc description)
        // Ưu tiên policies nếu có, nếu không thì lấy description
        let serviceContent = recursiveCleanJS(tourData.policies);
        if (!serviceContent || serviceContent.trim() === '') {
            serviceContent = recursiveCleanJS(tourData.description);
        }
        serviceEl.value = serviceContent;
    }
});