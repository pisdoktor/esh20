<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-4">
            <h4 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-hospital-user text-primary me-2"></i><?= $pageTitle;?>
            </h4>
        </div>
        <div class="col-md-5">
            <form action="index.php" method="GET" class="d-flex shadow-sm rounded-pill overflow-hidden bg-white p-1">
                <input type="hidden" name="controller" value="Patient">
                <input type="hidden" name="action" value="listwaiting">
                <input type="text" name="search" class="form-control border-0 px-3" 
                       placeholder="İsim, soyisim veya TC ile ara..." 
                       value="<?= htmlspecialchars($search ?? '') ?>">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
        <div class="col-md-3 text-end">
            <a href="index.php?controller=Patient&action=ilkkayit" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="fa-solid fa-plus me-2"></i>Yeni Kayıt
            </a>
        </div>
    </div>

    <?php if(!empty($search)): ?>
        <div class="alert alert-info py-2 d-flex justify-content-between align-items-center rounded-4 mb-3">
            <span><strong>"<?= htmlspecialchars($search) ?>"</strong> araması için <strong><?= $totalPatients ?></strong> sonuç bulundu.</span>
            <a href="index.php?controller=Patient&action=listwaiting" class="btn btn-sm btn-outline-info rounded-pill">Aramayı Temizle</a>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
    <tr class="align-middle">
        <?php $sort = \App\Helpers\UIHelper::sortIcon('h.isim', $ordering);?>
        <th>
            <a href="<?= $pagelink ?>&orderby=h.isim&orderdir=<?= $sort['nextDir'] ?>" class="text-decoration-none text-dark">
            Hasta Adı <?= $sort['icon'] ?>
            </a>
        </th>
        <?php $sort = \App\Helpers\UIHelper::sortIcon('h.tckimlik', $ordering);?>
        <th>
            <a href="<?= $pagelink ?>&orderby=h.tckimlik&orderdir=<?= $sort['nextDir'] ?>" class="text-decoration-none text-dark">
                TC Kimlik No <?= $sort['icon'] ?>
            </a>
        </th>
        <?php $sort = \App\Helpers\UIHelper::sortIcon('h.mahalle', $ordering);?> 
        <th>
            <a href="<?= $pagelink ?>&orderby=h.mahalle&orderdir=<?= $sort['nextDir'] ?>" class="text-decoration-none text-dark">
                Mahalle / İlçe <?= $sort['icon']; ?>
            </a>
        </th>
        
        <th class="text-muted">Anne / Baba Adı</th>
        
        <?php $sort = \App\Helpers\UIHelper::sortIcon('h.dogumtarihi', $ordering);?>
        <th>
            <a href="<?= $pagelink ?>&orderby=h.dogumtarihi&orderdir=<?= $sort['nextDir'] ?>" class="text-decoration-none text-dark">
                Doğum Tarihi <?= $sort['icon']; ?>
            </a>
        </th>
        
        <th>İletişim</th>
        <?php $sort = \App\Helpers\UIHelper::sortIcon('h.kayittarihi', $ordering);?>
        <th>
            <a href="<?= $pagelink ?>&orderby=h.kayittarihi&orderdir=<?= $sort['nextDir'] ?>" class="text-decoration-none text-dark">
                Kayıt Tarihi <?= $sort['icon']; ?>
            </a>
        </th>
        <?php $sort = \App\Helpers\UIHelper::sortIcon('h.randevutarihi', $ordering);?>
        <th class="pe-3">
            <a href="<?= $pagelink ?>&orderby=h.randevutarihi&orderdir=<?= $sort['nextDir'] ?>" class="text-decoration-none text-dark">
                Randevu Tarihi <?= $sort['icon']; ?>
            </a>
        </th>
    </tr>
</thead>
                    <tbody>
                        <?php foreach ($patients as $patient): ?>
    <tr>
            <td style="vertical-align: middle;">
            <div class="dropdown">
                <a class="dropdown-toggle text-decoration-none fw-bold" href="#" data-bs-toggle="dropdown" 
                   style="color:<?= $patient->cinsiyet == '1' ? '#0d6efd' : '#dc3545' ?>;">
                    <?= htmlspecialchars($patient->isim . ' ' . $patient->soyisim) ?>
                </a>

                <ul class="dropdown-menu shadow border-0">
                    <li><h6 class="dropdown-header">Hasta İşlemleri</h6></li>
                    <li><a class="dropdown-item" href="index.php?controller=Patient&action=view&id=<?= $patient->id ?>"><i class="fa-solid fa-id-card text-primary me-2"></i> Bilgileri Göster</a></li>
                    <li><a class="dropdown-item" href="index.php?controller=Patient&action=edit&id=<?= $patient->id ?>"><i class="fa-solid fa-pen-to-square text-warning me-2"></i> Bilgileri Düzenle</a></li>
                </ul>
            </div> 
        </td>

        <td style="vertical-align: middle;">
            <code class="text-dark fw-bold">
                <?= \App\Helpers\ValidationHelper::formatTc($patient->tckimlik) ?>
            </code>
        </td>
        
        <td style="vertical-align: middle;">
    <div class="d-flex flex-column">
        <div class="d-flex align-items-center mb-1">
            <i class="fa-solid fa-map-location-dot me-1 text-muted opacity-50"></i>
            <span class="small fw-semibold text-dark me-2"><?= $patient->mahalle_adi ?></span>
            
            <a href="index.php?controller=Patient&action=listwaiting&search=<?= urlencode($patient->ilce_adi) ?>" 
               class="badge bg-primary-soft text-primary x-small fw-normal text-decoration-none">
                <?= mb_strtoupper($patient->ilce_adi, 'UTF-8') ?>
            </a>
        </div>
        
        <div class="x-small text-muted">
             <?= $patient->sokak_adi ?> No: <?= $patient->kapino ?>
        </div>
    </div>
</td>
        
        <td style="vertical-align: middle;">
            <div class="x-small text-muted">
                <strong>A:</strong> <?= htmlspecialchars($patient->anneAdi) ?><br>
                <strong>B:</strong> <?= htmlspecialchars($patient->babaAdi) ?>
            </div>
        </td>

        <td style="vertical-align: middle;">
            <span class="small text-dark"><?= \App\Helpers\DateHelper::toTr($patient->dogumtarihi) ?></span><br>
            <span class="badge bg-light text-secondary border x-small">
                <?= \App\Helpers\DateHelper::calculateAge($patient->dogumtarihi) ?> Yaş
            </span>
        </td> 
        
        <td style="vertical-align: middle;">
            <?php if(!empty($patient->ceptel1)): ?>
                <div class="small">
                    <i class="fa-solid fa-mobile-screen text-success me-1"></i>
                    <a href="tel:<?= $patient->ceptel1 ?>" class="text-decoration-none text-dark"><?= $patient->ceptel1 ?></a>
                </div>
            <?php endif; ?>
            <?php if(!empty($patient->ceptel2)): ?>
                <div class="x-small text-muted">
                    <i class="fa-solid fa-phone me-1"></i>
                    <a href="tel:<?= $patient->ceptel2 ?>" class="text-decoration-none text-muted"><?= $patient->ceptel2 ?></a>
                </div>
            <?php endif; ?>
        </td>

        <td style="vertical-align: middle;" class="x-small text-muted">
            <?= \App\Helpers\DateHelper::toTr($patient->kayittarihi) ?>
        </td>
        <td style="vertical-align: middle;" class="small fw-bold <?= empty($patient->randevutarihi) ? 'text-danger' : 'text-success' ?>">
            <?= !empty($patient->randevutarihi) ? \App\Helpers\DateHelper::toTr($patient->randevutarihi) : 'Randevusu Yok' ?>
        </td>
    </tr> 
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="small text-muted">
                    <?= \App\Helpers\PaginationHelper::infoText($totalPatients, $page, $limit) ?>
                    </div>
                    <div>
                    <?= \App\Helpers\PaginationHelper::limitSelector($limit, $pagelink) ?>
                    </div>
                </div>
                <div>
                <?= \App\Helpers\PaginationHelper::render($totalPatients, $page, $limit, $pagelink) ?>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .table thead th { 
        font-size: 0.75rem; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
        font-weight: 700;
        padding: 1.2rem 0.5rem;
    }
    .btn-white { background: #fff; border: 1px solid #dee2e6; }
    .btn-white:hover { background: #f8f9fa; color: #0d6efd; }
    .x-small { font-size: 0.72rem; }
    .fw-bold { font-weight: 600 !important; }
    /* Satır üzerine gelince hafif belirginleşme */
    .table-hover tbody tr:hover { background-color: rgba(13, 110, 253, 0.02); }
</style>