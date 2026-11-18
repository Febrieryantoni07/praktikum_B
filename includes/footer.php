<?php
/**
 * Footer Layout Reusable
 * Sistem Informasi Rumah Sakit Sehat Harmoni
 */
$flash = get_flash();
?>
    <!-- End of Main Content or Layout Wrapper -->

    <!-- Bootstrap 5.3.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom Theme JS -->
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>

    <?php if ($flash): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const iconType = '<?php echo $flash['type'] === 'danger' ? 'error' : ($flash['type'] === 'warning' ? 'warning' : 'success'); ?>';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: iconType,
                    title: <?php echo json_encode($flash['message']); ?>,
                    showConfirmButton: false,
                    timer: 4500,
                    timerProgressBar: true
                });
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
