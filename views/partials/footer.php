</main>

    <footer class="footer mt-auto py-3 bg-white border-top">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="text-muted small">&copy; <?= date('Y') ?> ESH Panel v2.0</span>
            <div class="small">
                <span class="badge bg-light text-dark border">PHP 8.x</span>
                <span class="badge bg-light text-dark border">TomTom SDK</span>
            </div>
        </div>
    </footer>

    <script>
        $(document).ready(function() {
            // Global Chosen Başlatıcı
            $('.chosen-select').chosen({ width: '100%', no_results_text: "Kayıt bulunamadı:" });

            // Toastr Ayarları
            toastr.options = { "progressBar": true, "positionClass": "toast-top-right", "timeOut": "4000" };

            // PHP'den gelen Session Mesajlarını Yakala
            <?php if(isset($_SESSION['success'])): ?>
                toastr.success("<?= $_SESSION['success'] ?>");
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                toastr.error("<?= $_SESSION['error'] ?>");
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            // URL'den gelen status mesajlarını yakala
            const params = new URLSearchParams(window.location.search);
            if(params.get('msg') === 'kayit_basarili') toastr.success('İşlem başarıyla tamamlandı.');
        });
    </script>
</body>
</html>