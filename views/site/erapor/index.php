<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="text-primary"><i class="fas fa-chart-pie me-2"></i>e-Rapor Havuzu</h3>
            <p class="text-muted">Sisteme girilen tüm rapor verilerinin istatistiksel dökümü.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="index.php?controller=Erapor&action=create" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-1"></i>Yeni Rapor Verisi Ekle
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="eraporTable">
                    <thead class="table-light">
                        <tr>
                            <th>TC Kimlik No</th>
                            <th>Hasta Ad Soyad</th>
                            <th>Rapor Tarihi</th>
                            <th>Kategori</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($reports as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row->hastatckimlik) ?></td>
                            <td><strong><?= htmlspecialchars($row->isim) ?> <?= htmlspecialchars($row->soyisim) ?></strong></td>
                            <td><?= date('d.m.Y', strtotime($row->basvurutarihi)) ?></td>
                            <td><span class="badge bg-secondary"><?= $row->brans ?></span></td>
                            <td>
                                <?php if($row->kayitlimi): ?>
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Sistemde Kayıtlı</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border">Yeni Veri</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>