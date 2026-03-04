</div>
<!-- End Main Content -->

<!-- Footer -->
<footer class="text-center text-muted py-3" style="margin-left: 250px;">
    <small>
        &copy; <?= date('Y') ?> GDSS Bantuan Rumah Keluarga Miskin |
        Universitas Islam Negeri Malang
    </small>
</footer>

<!-- Bootstrap JS lokal -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>

<!-- jQuery (untuk kemudahan) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Custom JS -->
<script>
    // Auto hide alerts after 5 seconds
    setTimeout(function () {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Confirm delete
    $('.btn-delete').on('click', function (e) {
        if (!confirm('Yakin ingin menghapus data ini?')) {
            e.preventDefault();
        }
    });

    // Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>
</body>

</html>