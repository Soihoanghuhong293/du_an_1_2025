function printGuestList() {
    window.print();
}

document.addEventListener('DOMContentLoaded', function() {
    updateCheckinStats(); // Chạy ngay khi load trang

    const toggles = document.querySelectorAll('.checkin-toggle');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const guestId = this.dataset.id;
            const isChecked = this.checked ? 1 : 0;
            const timeLabel = this.closest('td').querySelector('.checkin-time');
            const row = this.closest('tr');

            
            this.disabled = true;

            // Gọi AJAX
            fetch('index.php?act=guest-ajax-checkin', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `guest_id=${guestId}&status=${isChecked}`
            })
            .then(response => response.json())
            .then(data => {
                this.disabled = false;
                if (data.success) {
                    if(isChecked) {
                        const now = new Date();
                        const timeString = now.getHours() + ':' + String(now.getMinutes()).padStart(2, '0') + ' ' + now.getDate() + '/' + (now.getMonth()+1);
                        timeLabel.innerText = timeString;
                        row.classList.add('table-success'); 
                    } else {
                        timeLabel.innerText = '';
                        row.classList.remove('table-success'); // Bỏ màu xanh
                    }
                    updateCheckinStats();
                } else {
                    alert('Lỗi: ' + data.message);
                    this.checked = !isChecked; // Revert switch nếu lỗi
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.disabled = false;
                this.checked = !isChecked;
                alert('Có lỗi kết nối!');
            });
        });
    });

    function updateCheckinStats() {
        const total = typeof TOTAL_GUESTS !== 'undefined' ? TOTAL_GUESTS : 0;

        const checked = document.querySelectorAll('.checkin-toggle:checked').length;
        const percent = total > 0 ? (checked / total) * 100 : 0;

        const countEl = document.getElementById('checkin-count');
        const progressEl = document.getElementById('checkin-progress');

        if(countEl) countEl.innerText = checked;
        if(progressEl) progressEl.style.width = percent + '%';
    }
});
