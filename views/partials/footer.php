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
<?php 
// Veritabanı instance'ını alıyoruz
$db = \App\Core\Database::getInstance();

if ($db->isDebug()): 
    $logs = $db->getQueryLog();
?>
    <div class="container-fluid mt-5">
        <div class="card border-danger mb-4 shadow">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fa-solid fa-bug me-2"></i> SQL Debug Console</h6>
                <span class="badge bg-white text-danger"><?= count($logs) ?> Sorgu Çalıştırıldı</span>
            </div>
            <div class="card-body bg-dark p-0">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-dark table-striped table-hover mb-0 small">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">#</th>
                                <th>Sorgu (SQL)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $index => $sql): ?>
                                <tr>
                                    <td class="text-center text-muted"><?= $index + 1 ?></td>
                                    <td>
                                        <code class="text-info" style="word-break: break-all;">
                                            <?= htmlspecialchars($sql) ?>;
                                        </code>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light py-1 small text-muted">
                <strong>Hafıza Kullanımı:</strong> <?= round(memory_get_usage() / 1024 / 1024, 2) ?> MB
            </div>
        </div>
    </div>
<?php endif; ?>
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