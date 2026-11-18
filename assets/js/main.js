/**
 * JavaScript Utama Sistem Informasi Rumah Sakit Sehat Harmoni
 * Client Server A
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Auto dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // 2. Client-side Table Search Filter Helper
    const searchInputs = document.querySelectorAll('[data-table-search]');
    searchInputs.forEach(function (input) {
        input.addEventListener('keyup', function () {
            const targetTableId = this.getAttribute('data-table-search');
            const table = document.getElementById(targetTableId);
            if (!table) return;

            const filter = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // 3. Konfirmasi Hapus Data dengan SweetAlert2 (jika tersedia) atau konfirmasi standar
    const deleteButtons = document.querySelectorAll('.btn-confirm-delete');
    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            const itemName = this.getAttribute('data-item-name') || 'data ini';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: `Data "${itemName}" akan dihapus permanen dari sistem!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-trash-fill"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            } else {
                if (confirm(`Apakah Anda yakin ingin menghapus "${itemName}"? Tindakan ini tidak dapat dibatalkan.`)) {
                    window.location.href = href;
                }
            }
        });
    });
});
