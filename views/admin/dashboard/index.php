<div class="row">
        <div class="col-lg-8">
            <div class="row">
                <div class="col-12">
                    <?= \App\Helpers\StatHelper::yasGruplariGrafigi(); ?>
                </div>
                <div class="col-md-6">
                    <?= \App\Helpers\StatHelper::cikarilmaNedenleriWidget(); ?>
                </div>
                <div class="col-md-6">
                    <?= \App\Helpers\StatHelper::izlemSikligiWidget(); ?>
                </div>
                <div class="col-md-12">
                     <?= \App\Helpers\StatHelper::izlenenYasGruplariWidget(); ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <?= \App\Helpers\StatHelper::genelIstatistikWidget(); ?>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-rocket me-2"></i>Hızlı İşlemler</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="index.php?controller=Erapor&action=create" class="btn btn-light text-start border-0 py-3 shadow-sm mb-1">
                            <i class="fas fa-plus-circle text-primary me-2"></i> Yeni Rapor Oluştur
                        </a>
                        <a href="index.php?controller=User&action=list" class="btn btn-light text-start border-0 py-3 shadow-sm mb-1">
                            <i class="fas fa-users text-secondary me-2"></i> Personel Listesi
                        </a>
                        <a href="index.php?controller=Brans&action=index" class="btn btn-light text-start border-0 py-3 shadow-sm mb-1">
                            <i class="fas fa-tags text-info me-2"></i> Branş Yönetimi
                        </a>
                    </div>
                </div>
            </div>

            <?= \App\Helpers\StatHelper::dogumGunuListesi(); ?>
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-hospital-user me-2"></i>Branş Yoğunluğu</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Branş</th>
                                    <th class="text-end pe-3">Adet</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($stats['brans_dist'] as $b): ?>
                                <tr>
                                    <td class="ps-3 small"><?= htmlspecialchars($b->brans ?: 'Belirtilmemiş') ?></td>
                                    <td class="text-end pe-3 fw-bold small text-primary"><?= $b->count ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>