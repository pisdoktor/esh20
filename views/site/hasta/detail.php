<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="mb-0">
                <i class="fa-solid fa-file-waveform text-primary me-2"></i>
                <span style="color:<?php echo $hasta->cinsiyet == '1' ? '#0d6efd' : '#dc3545'; ?>;">
                    <?php echo $hasta->isim . " " . $hasta->soyisim; ?>
                </span>
                <small class="text-muted ms-2">(<?php echo $hasta->anneAdi; ?> / <?php echo $hasta->babaAdi; ?>)</small>
                <?php if($hasta->pasif): ?>
                    <span class="badge bg-danger ms-2" style="font-size: 0.6em; vertical-align: middle;">
                        <i class="fa-solid fa-lock me-1"></i> <?php echo $pasifnedeni; ?>
                    </span>
                <?php endif; ?>
            </h4>
            <?php if($hasta->pasif): ?>
                <div class="alert alert-danger d-flex align-items-center mb-0 py-2 px-3 shadow-sm border-0">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <div><strong>DOSYA KAPALI</strong> (<?php echo $hasta->pasiftarihi; ?>)</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body bg-light">
        <div class="row mb-4">
            <div class="col-12">
                <div class="p-2 bg-white rounded border shadow-xs d-flex flex-wrap gap-2 align-items-center">
                    <a href="javascript:history.go(-1);" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="fa-solid fa-arrow-left me-1"></i> Geri
                    </a>
                    <button type="button" class="btn btn-danger btn-sm px-3 shadow-sm" onclick="tekliMernisSorgula('<?php echo $hasta->tckimlik; ?>')">
                        <i class="fa-solid fa-sync me-1"></i> MERNİS Sorgula
                    </button>
                    <a href="index.php?controller=Patient&action=edit&id=<?= $hasta->id;?>" class="btn btn-warning btn-sm px-3 shadow-sm">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Düzenle
                    </a>
                    <button type="button" class="btn btn-info btn-sm px-3 text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#ek3hazirla">
                        <i class="fa-solid fa-file-pdf me-1"></i> EK-3
                    </button>

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle px-3 shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-user-doctor me-1"></i> Tıbbi İşlemler
                        </button>
                        <ul class="dropdown-menu shadow">
                            <li><a class="dropdown-item" href="index.php?option=site&bolum=izlemler&task=hedit&tc=<?php echo $hasta->tckimlik; ?>"><i class="fa-solid fa-stethoscope me-2 text-primary"></i>İzlem Gir</a></li>
                            <li><a class="dropdown-item" href="index.php?option=site&bolum=pizlemler&task=hedit&tc=<?php echo $hasta->tckimlik; ?>"><i class="fa-solid fa-calendar-plus me-2 text-success"></i>İzlem Planla</a></li>
                            <li><a class="dropdown-item border-bottom" href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $hasta->tckimlik; ?>"><i class="fa-solid fa-list-check me-2 text-info"></i>İzlemleri Getir</a></li>
                            <li><a class="dropdown-item mt-1" href="index.php?option=site&bolum=hastailacrapor&id=<?php echo $hasta->id; ?>"><i class="fa-solid fa-pills me-2 text-danger"></i>İlaç Raporları</a></li>
                        </ul>
                    </div>

                    <div class="ms-auto d-flex gap-2 align-items-center">
                        <?php 
                            $ana_tel = preg_replace('/[^0-9]/', '', $hasta->ceptel1);
                            if(substr($ana_tel, 0, 1) == "0") $ana_tel = "9" . $ana_tel;
                        ?>
                        <a href="https://wa.me/<?php echo $ana_tel; ?>" target="_blank" class="btn btn-success btn-sm rounded-circle shadow-sm" title="WhatsApp">
                            <i class="fa-brands fa-whatsapp fa-lg"></i>
                        </a>
                        <a href="tel:<?php echo $hasta->ceptel1; ?>" class="btn btn-primary btn-sm rounded-circle shadow-sm" title="Hemen Ara">
                            <i class="fa-solid fa-phone"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="bg-white p-4 rounded border shadow-sm h-100">
                    <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fa-solid fa-id-card-clip me-2"></i>Kimlik ve Adres</h5>
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted">TC Kimlik No:</label>
                        <div class="col-sm-8 text-dark fw-bold"><?php echo $hasta->tckimlik; ?></div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted">Doğum Tarihi:</label>
                        <div class="col-sm-8"><?php  \App\Helpers\DateHelper::toTr($hasta->dogumtarihi); ?> <span class="badge bg-secondary ms-2"><?php echo \App\Helpers\DateHelper::calculateAge($hasta->dogumtarihi); ?> Yaş</span></div>
                    </div>
                    <hr class="text-muted opacity-25">
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted">Ana Adres:</label>
                        <div class="col-sm-8 text-uppercase">
                            <?php echo $adres->mahalle; ?> MAH. <?php echo $adres->sokak; ?> SK-CD NO: <?php echo $adres->kapino; ?> - <?php echo $adres->ilce; ?>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted">Adres Açıklaması:</label>
                        <div class="col-sm-8 fst-italic text-secondary"><?php echo $hasta->adres_aciklama ?: 'Adres açıklaması yok'; ?></div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <label class="col-sm-4 fw-bold text-muted">Koordinatlar:</label>
                        <div class="col-sm-8 d-flex gap-2">
                            <span class="text-primary font-monospace"><?php echo $hasta->coords ?: 'Girilmemiş'; ?></span>
                            <div class="btn-group btn-group-sm ms-auto">
                                <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#konumbul"><i class="fa-solid fa-map-pin"></i> Bul</button>
                                <?php if($hasta->coords): ?>
                                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $hasta->coords; ?>" target="_blank" class="btn btn-outline-primary"><i class="fa-solid fa-diamond-turn-right"></i> Git</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    
                    <hr class="text-muted opacity-25">
                    <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fa-solid fa-stethoscope me-2"></i>Klinik Durum</h5>
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted">Son İzlem:</label>
                        
                    </div>
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted">Hastalıklar:</label>
                        <div class="col-sm-8 small lh-sm">
                        <?php //$hasta::getDiseaseNames($hasta->hastaliklar);?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="bg-white rounded border shadow-sm mb-4">
                    <ul class="nav nav-tabs nav-justified bg-light rounded-top" id="hastaTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active py-3" id="genel-tab" data-bs-toggle="tab" data-bs-target="#geneltab" type="button"><i class="fa-solid fa-info-circle fa-lg"></i></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-3 text-danger" id="sonda-tab" data-bs-toggle="tab" data-bs-target="#sondatab" type="button"><i class="fa-solid fa-syringe fa-lg"></i></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-3 text-warning" id="mama-tab" data-bs-toggle="tab" data-bs-target="#mamatab" type="button"><i class="fa-solid fa-bowl-food fa-lg"></i></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-3 text-primary" id="bez-tab" data-bs-toggle="tab" data-bs-target="#beztab" type="button"><i class="fa-solid fa-layer-group fa-lg"></i></button>
                        </li>
                    </ul>
                    <div class="tab-content p-4 border-top" id="hastaTabContent" style="min-height: 250px;">
                        <div class="tab-pane fade show active" id="geneltab" role="tabpanel">
                            <div class="list-group list-group-flush small">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="fa-solid fa-user-clock text-warning me-2"></i>Geçici Takipli:</span>
                                    <?php echo $hasta->gecici ? '<span class="text-success fw-bold">Evet</span>' : '<span class="text-danger">Hayır</span>'; ?>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="fa-solid fa-file-signature text-primary me-2"></i>E-Rapor Hastası:</span>
                                    <?php echo $hasta->erapor ? '<span class="text-success fw-bold">Evet</span>' : '<span class="text-danger">Hayır</span>'; ?>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                                    <span><i class="fa-solid fa-lungs text-info me-2"></i>Oksijen Bağımlı:</span>
                                    <?php echo $hasta->o2bagimli ? '<span class="text-success fw-bold">Evet</span>' : '<span class="text-danger">Hayır</span>'; ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="sondatab" role="tabpanel">
                            <?php if($hasta->sonda): ?>
                                <div class="alert alert-success d-flex align-items-center"><i class="fa-solid fa-check-circle me-2"></i> Sonda Mevcut</div>
                                <p class="small mb-0 text-muted">Takılma Tarihi: <strong><?php echo $hasta->sondatarihi; ?></strong></p>
                            <?php else: ?>
                                <div class="text-center py-4"><i class="fa-solid fa-circle-xmark fa-3x text-muted mb-3"></i><p>Sonda kullanımı yok.</p></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 border-top border-warning border-4 rounded">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h6 class="mb-0 fw-bold text-warning"><i class="fa-solid fa-note-sticky me-2"></i>Hasta Notları</h6>
                        <button class="btn btn-outline-warning btn-sm px-3" data-bs-toggle="modal" data-bs-target="#changenotes"><i class="fa-solid fa-plus"></i></button>
                    </div>
                    <div class="card-body bg-light-warning" style="min-height: 120px; max-height: 250px; overflow-y: auto;">
                        <p class="small text-dark mb-0 lh-sm"><?php echo $hasta->notes ? nl2br($hasta->notes) : '<em class="text-muted">Not bulunmuyor...</em>'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changenotes" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?option=site&bolum=hastalar&task=notekle" method="post" class="modal-content border-0 shadow-lg">
            <input type="hidden" name="id" value="<?php echo $hasta->id; ?>">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold text-dark">Hasta Notu Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <textarea class="form-control border-warning shadow-none" name="notes" rows="6" placeholder="Notunuzu buraya yazın..."><?php echo $hasta->notes; ?></textarea>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="document.querySelector('textarea[name=notes]').value=''">Tümünü Sil</button>
                <div class="ms-auto">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal">Kapat</button>
                    <button type="submit" class="btn btn-warning btn-sm fw-bold">Kaydet</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="ek3hazirla" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?option=site&bolum=hastalar&task=ek3hazirla" method="post" target="_blank" class="modal-content shadow-lg border-0">
            <input type="hidden" name="id" value="<?php echo $hasta->id; ?>">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-pdf me-2"></i>EK-3 Form Hazırlığı</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="list-group list-group-flush shadow-sm">
                    
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-link text-muted me-auto btn-sm text-decoration-none" data-bs-dismiss="modal">İptal</button>
                <button type="submit" class="btn btn-info btn-sm px-4 fw-bold text-white shadow-sm">Formu Oluştur</button>
            </div>
        </form>
    </div>
</div>

<style>
    .shadow-xs { shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.05); }
    .bg-light-warning { background-color: #fff9e6; }
    .cursor-pointer { cursor: pointer; }
    .nav-tabs .nav-link { border: none; color: #6c757d; font-weight: 500; transition: all 0.2s; }
    .nav-tabs .nav-link.active { background-color: #fff !important; border-bottom: 3px solid #0d6efd !important; color: #0d6efd !important; }
    .wa-btn:hover { background-color: #128c7e !important; transform: scale(1.1); transition: all 0.2s; }
    .modal-header-primary { background-color: #0d6efd; color: white; }
    .modal-header-danger { background-color: #dc3545; color: white; }
    .modal-header-success { background-color: #198754; color: white; }
</style>

<script>
$(document).ready(function(){
    // Sayfa açıldığında not varsa modalı göster
    <?php if (!empty($hasta->notes)) { ?>
        var noteModal = new bootstrap.Modal(document.getElementById('shownotes'));
        // noteModal.show(); // Tercihen otomatik açılabilir
    <?php } ?>
});

function tekliMernisSorgula(tc) {
    if (!tc) return;
    const btn = event.currentTarget;
    const oldHtml = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sorgulanıyor...';
    btn.disabled = true;

    $.ajax({
        url: 'index.php?controller=Patient&action=died',
        type: 'GET',
        dataType: 'json',
        data: { tc: tc },
        success: function(response) {
        if (response.oldu > 0) {
        // Vefat bilgisi bulunduğunda kırmızı/turuncu bir uyarı
        toastr.error(
            "MERNİS: Hastanın vefat ettiği tespit edildi.<br><b>Vefat Tarihi: " + response.olumTarihi + "</b>", 
            "Sistem Uyarısı", 
            {
                timeOut: 5000,
                closeButton: true,
                progressBar: true
            }
        );

        // İstersen 3 saniye sonra sayfayı yenileyebilirsin
        // setTimeout(function() { window.location.reload(); }, 3000);

    } else {
        // Vefat yoksa mavi/bilgi uyarısı
        toastr.info(response.mesaj || "Durum değişikliği yok.", "Sorgu Tamamlandı");
    }
},
        complete: function() {
            btn.innerHTML = oldHtml;
            btn.disabled = false;
        }
    });
}
</script>