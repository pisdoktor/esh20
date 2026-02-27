<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-hospital-user text-primary me-2"></i><?= $pageTitle;?>
            </h4>
            <small class="text-muted">Sistemde kayıtlı toplam <strong><?= $totalPatients ?></strong> hasta bulunmaktadır.</small>
        </div>
        <a href="index.php?controller=Patient&action=create" class="btn btn-primary shadow-sm rounded-pill px-4">
            <i class="fa-solid fa-plus me-2"></i>Yeni Hasta Kaydı
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 60px;">ID</th>
                            <th>Hasta Bilgileri</th>
                            <th>T.C. Kimlik</th>
                            <th>Bölge / Adres</th>
                            <th>İletişim</th>
                            <th>Sağlık Durumu</th>
                            <th>Durum</th>
                            <th class="text-end pe-4">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($patients)): foreach ($patients as $p): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="text-muted fw-bold">#<?= $p->id ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 40px; height: 40px; background: rgba(13,110,253,0.1);">
                                        <?= mb_substr($p->isim, 0, 1) . mb_substr($p->soyisim, 0, 1) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($p->isim . ' ' . $p->soyisim) ?></div>
                                        <small class="text-muted"><?= \App\Helpers\DateHelper::calculateAge($p->dogumtarihi) ?> Yaş / <?= $p->cinsiyet == 1 ? 'Erkek' : 'Kadın' ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="text-dark fw-medium"><?= $p->tckimlik ?></code>
                            </td>
                            <td>
                                <div class="small">
                                    <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                    <?= htmlspecialchars($p->ilce_adi ?? 'Bilinmiyor') ?> / <?= htmlspecialchars($p->mahalle_adi ?? 'Bilinmiyor') ?>
                                </div>
                            </td>
                            <td>
                                <div class="small"><i class="fa-solid fa-phone me-1 text-success"></i><?= $p->ceptel1 ?></div>
                                <?php if($p->ceptel2): ?>
                                    <div class="x-small text-muted"><i class="fa-solid fa-phone me-1"></i><?= $p->ceptel2 ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= \App\Helpers\BadgeHelper::priority($p->bagimlilik) ?>
                            </td>
                            <td>
                                <?= \App\Helpers\BadgeHelper::status($p->pasif) ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm">
                                    <a href="<?= $viewlink;?><?= $p->id ?>" class="btn btn-white btn-sm text-primary" title="Detay">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="<?= $editlink;?><?= $p->id ?>" class="btn btn-white btn-sm text-warning" title="Düzenle">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" onclick="hastaSil(<?= $p->id ?>)" class="btn btn-white btn-sm text-danger" title="Sil">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fa-3x mb-3 opacity-25"></i>
                                <p>Henüz kayıtlı hasta bulunamadı.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    <?= $limit ?> kayıt gösteriliyor.
                </div>
                <div>
                    <?= \App\Helpers\PaginationHelper::render($totalPatients, $page, $limit, $pagelink) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background-color: rgba(13, 110, 253, 0.08); }
    .table thead th { 
        font-size: 0.75rem; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
        font-weight: 700;
        padding: 1rem 0.5rem;
    }
    .btn-white { background: #fff; border: 1px solid #dee2e6; }
    .btn-white:hover { background: #f8f9fa; }
    .x-small { font-size: 0.7rem; }
</style>