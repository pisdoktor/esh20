<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-3 col-md-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 80px; z-index: 100;">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="mb-0"><i class="fa-solid fa-filter me-2"></i>Arşiv Filtresi</h6>
                </div>
                <form method="GET" action="index.php" id="archiveFilterForm" class="card-body p-3">
                    <input type="hidden" name="controller" value="Archive">
                    <input type="hidden" name="action" value="index">

                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase">İsim Baş Harfi</label>
                        <select name="isim" class="form-select form-select-sm">
                            <option value="">Tümü</option>
                            <?php 
                            
                            foreach($alfabe as $alp): ?>
                                <option value="<?= $alp ?>" <?= ($filters['isim'] == $alp) ? 'selected' : '' ?>><?= $alp ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase">Soyisim</label>
                        <select name="soyisim" class="form-select form-select-sm">
                            <option value="">Tümü</option>
                            <?php 
                            
                            foreach($alfabe as $alp): ?>
                                <option value="<?= $alp ?>" <?= ($filters['soyisim'] == $alp) ? 'selected' : '' ?>><?= $alp ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <label class="small fw-bold text-muted mb-2 text-uppercase">Bölgeler / Mahalleler</label>
                    
                    <div class="form-check mb-2 border-bottom pb-2">
                        <input class="form-check-input" type="checkbox" id="masterCheck">
                        <label class="form-check-label fw-bold text-primary small" for="masterCheck">TÜMÜNÜ SEÇ</label>
                    </div>

                    <div class="mahalle-scroll-container" style="max-height: 450px; overflow-y: auto; overflow-x: hidden;">
                        <?php 
                        $seciliMahalleler = is_array($filters['mahalle']) ? $filters['mahalle'] : [];
                        
                        foreach ($locations as $ilce => $mahalleler): 
                            $ilceId = $mahalleler[0]->ilce_id;
                            $hasSelected = array_intersect(array_column($mahalleler, 'id'), $seciliMahalleler);
                        ?>
                            <div class="ilce-group mb-1">
                                <div class="ilce-header d-flex align-items-center bg-light p-2 border rounded cursor-pointer shadow-sm">
                                    <input class="form-check-input ilce-master-check me-2" type="checkbox" 
                                           data-target="ilce-box-<?= $ilceId ?>" id="ilce-<?= $ilceId ?>"
                                           <?= (count($hasSelected) == count($mahalleler)) ? 'checked' : '' ?>>
                                    <label class="fw-bold small mb-0 flex-grow-1" for="ilce-<?= $ilceId ?>"><?= $ilce ?></label>
                                    <i class="fa-solid <?= $hasSelected ? 'fa-chevron-up' : 'fa-chevron-down' ?> text-muted small ms-2 toggle-icon" 
                                       data-bs-toggle="collapse" data-bs-target="#ilce-box-<?= $ilceId ?>"></i>
                                </div>

                                <div class="collapse p-2 bg-white border border-top-0 rounded-bottom <?= $hasSelected ? 'show' : '' ?>" id="ilce-box-<?= $ilceId ?>">
                                    <?php foreach ($mahalleler as $m): ?>
                                        <div class="form-check">
                                            <input class="form-check-input mahalle-check" type="checkbox" 
                                                   name="mahalle[]" value="<?= $m->id ?>" id="m-<?= $m->id ?>"
                                                   <?= in_array($m->id, $seciliMahalleler) ? 'checked' : '' ?>>
                                            <label class="form-check-label small" for="m-<?= $m->id ?>"><?= $m->mahalle ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3 shadow">
                        <i class="fa-solid fa-magnifying-glass me-2"></i>Sorgula
                    </button>
                    <a href="index.php?controller=Archive&action=index" class="btn btn-outline-secondary btn-sm w-100 mt-2">Filtreleri Temizle</a>
                </form>
            </div>
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-list-ul me-2 text-primary"></i>Dosya Kayıtları 
                        <span class="badge bg-secondary ms-2 small fw-normal" style="font-size: 0.7rem;"><?= $total ?> Kayıt</span>
                    </h5>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small" style="width: 35%;">HASTA ADI / TC</th>
                                    <th class="text-muted small" style="width: 25%;">BÖLGE / ADRES</th>
                                    <th class="text-center text-muted small" style="width: 15%;">TOPLAM İZLEM</th>
                                    <th class="text-muted small" style="width: 15%;">SON İZLEM</th>
                                    <th class="text-end pe-4" style="width: 10%;">İŞLEM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($rows)): foreach($rows as $row): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-primary"><?= htmlspecialchars($row->isim . ' ' . $row->soyisim) ?></div>
                                        <div class="text-muted small"><i class="fa-solid fa-id-card me-1 opacity-50"></i><?= $row->tckimlik ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?= $row->mahalleadi ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= $row->ilceadi ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-info text-dark px-3 fw-bold">
                                            <?= $row->izlemsayisi ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($row->sonizlemtarihi): ?>
                                            <div class="small fw-bold text-success"><?= date('d.m.Y', strtotime($row->sonizlemtarihi)) ?></div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Gerçekleşti</div>
                                        <?php else: ?>
                                            <span class="text-muted small">Kayıt yok</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?controller=Patient&action=view&id=<?= $row->id ?>" class="btn btn-outline-primary" title="Detaylar">
                                                <i class="fa-solid fa-folder-open"></i>
                                            </a>
                                            <a href="index.php?controller=Patient&action=edit&id=<?= $row->id ?>" class="btn btn-outline-secondary" title="Düzenle">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <img src="assets/img/empty.svg" alt="" style="width: 100px; opacity: 0.3;" class="mb-3 d-block mx-auto">
                                        <p class="text-muted">Kriterlere uygun herhangi bir kayıt bulunamadı.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if($totalPages > 1): ?>
                <div class="card-footer bg-white border-top-0 py-3">
                    <nav>
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="index.php?controller=Archive&action=index&page=<?= $page-1 ?>&isim=<?= $filters['isim'] ?>&soyisim=<?= $filters['soyisim'] ?><?php foreach($seciliMahalleler as $m) echo '&mahalle[]='.$m; ?>">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for($i=1; $i<=$totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?controller=Archive&action=index&page=<?= $i ?>&isim=<?= $filters['isim'] ?>&soyisim=<?= $filters['soyisim'] ?><?php foreach($seciliMahalleler as $m) echo '&mahalle[]='.$m; ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="index.php?controller=Archive&action=index&page=<?= $page+1 ?>&isim=<?= $filters['isim'] ?>&soyisim=<?= $filters['soyisim'] ?><?php foreach($seciliMahalleler as $m) echo '&mahalle[]='.$m; ?>">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Genel Tümünü Seç
    const masterCheck = document.getElementById('masterCheck');
    if(masterCheck) {
        masterCheck.addEventListener('change', function() {
            document.querySelectorAll('.mahalle-check, .ilce-master-check').forEach(c => c.checked = this.checked);
        });
    }

    // 2. İlçe Bazlı Seçim: İlçeye basınca altındaki mahalleleri seç
    document.querySelectorAll('.ilce-master-check').forEach(master => {
        master.addEventListener('change', function() {
            const targetId = this.getAttribute('data-target');
            const container = document.getElementById(targetId);
            container.querySelectorAll('.mahalle-check').forEach(c => c.checked = this.checked);
        });
    });

    // 3. İkon Değişimi: Collapse açılıp kapandığında ok yönünü değiştir
    document.querySelectorAll('.collapse').forEach(collapseEl => {
        collapseEl.addEventListener('show.bs.collapse', function () {
            const icon = this.previousElementSibling.querySelector('.toggle-icon');
            icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
        });
        collapseEl.addEventListener('hide.bs.collapse', function () {
            const icon = this.previousElementSibling.querySelector('.toggle-icon');
            icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
        });
    });
});
</script>