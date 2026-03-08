<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex flex-wrap gap-2 align-items-center">
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
                        <li><a class="dropdown-item" href="index.php?controller=Visit&action=izlemgir&tc=<?php echo $hasta->tckimlik; ?>"><i class="fa-solid fa-stethoscope me-2 text-primary"></i>İzlem Gir</a></li>
                        <li><a class="dropdown-item" href="index.php?controller=PlannedVisit&action=izlemplanla&tc=<?php echo $hasta->tckimlik; ?>"><i class="fa-solid fa-calendar-plus me-2 text-success"></i>İzlem Planla</a></li>
                        <li><a class="dropdown-item" href="index.php?controller=Visit&action=history&tc=<?php echo $hasta->tckimlik; ?>"><i class="fa-solid fa-list-check me-2 text-info"></i>İzlemleri Getir</a></li>
                    </ul>
                </div>
            </div>

            <div class="d-flex gap-2 align-items-center">
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

    <div class="card-body bg-light">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="bg-white p-4 rounded border shadow-sm h-100">
                    <div class="border-bottom pb-3 mb-3">
                        <h4 class="mb-1">
                            <i class="fa-solid fa-circle-user text-primary me-2"></i>
                            <span style="color:<?php echo $hasta->cinsiyet == '1' ? '#0d6efd' : '#dc3545'; ?>;">
                                <?php echo $hasta->isim . " " . $hasta->soyisim; ?>
                            </span>
                        </h4>
                        <?php if($hasta->pasif): ?>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="text-muted small fw-bold">
                                Pasif Tarihi: <?= \App\Helpers\DateHelper::toTr($hasta->pasiftarihi); ?>
                            </span>
                            <span class="badge bg-danger">
                                <i class="fa-solid fa-lock me-1"></i> DOSYA KAPALI (<?php echo $pasifnedeni; ?>)
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <h5 class="text-primary small fw-bold text-uppercase mb-3"><i class="fa-solid fa-id-card-clip me-2"></i>Kimlik ve Adres Bilgileri</h5>
                    
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted small">TC Kimlik No:</label>
                        <div class="col-sm-8 text-dark fw-bold font-monospace"><?= \App\Helpers\ValidationHelper::formatTc($hasta->tckimlik); ?></div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted small">Anne / Baba Adı:</label>
                        <div class="col-sm-8 text-dark"><?php echo $hasta->anneAdi.' / '.$hasta->babaAdi; ?></div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted small">Doğum Tarihi:</label>
                        <div class="col-sm-8 text-dark">
                            <?= \App\Helpers\DateHelper::toTr($hasta->dogumtarihi);?> 
                            <span class="badge bg-secondary ms-2"><?php echo \App\Helpers\DateHelper::calculateAge($hasta->dogumtarihi); ?> Yaş</span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted small">Sağlık Güvencesi:</label>
                        <div class="col-sm-8 text-dark"><?= $guvence; ?></div>
                    </div>
                    <hr class="text-muted opacity-25">
                    
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted small">Ana Adres:</label>
                        <div class="col-sm-8 text-uppercase small text-dark">
                            <?php echo $anaadres->mahalle; ?> MAH. <?php echo $anaadres->sokak; ?> SK-CD NO: <?php echo $anaadres->kapino; ?> - <?php echo $anaadres->ilce; ?>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted small">Adres Açıklaması:</label>
                        <div class="col-sm-8 fst-italic text-secondary small"><?php echo $hasta->adres_aciklama ?: 'Açıklama girilmemiş.'; ?></div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <label class="col-sm-4 fw-bold text-muted small">Koordinatlar:</label>
                        <div class="col-sm-8 d-flex gap-2">
                            <span class="text-primary font-monospace small"><?php echo $hasta->coords ?: 'Girilmemiş'; ?></span>
                            <div class="btn-group btn-group-sm ms-auto">
                                <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#konumbul"><i class="fa-solid fa-map-pin"></i> Bul</button>
                                <?php if($hasta->coords): ?>
                                    <a href="https://www.google.com/maps?q=<?php echo $hasta->coords; ?>" target="_blank" class="btn btn-outline-primary"><i class="fa-solid fa-diamond-turn-right"></i> Git</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($diger_adres)): ?>
                        <?php foreach ($diger_adres as $v):?>
                        <div class="row mb-2 border-top pt-2 mt-2">
                            <label class="col-sm-4 fw-bold text-muted small">Diğer Adres:</label>
                            <div class="col-sm-8 text-uppercase small text-dark">
                                <?php echo $v['adres']->mahalle; ?> MAH. <?php echo $v['adres']->sokak; ?> SK-CD NO: <?php echo $v['adres']->kapino; ?> - <?php echo $v['adres']->ilce; ?>
                            </div>
                        </div>
                        <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted small">Adres Açıklaması:</label>
                        <div class="col-sm-8 fst-italic text-secondary small"><?php echo $v['adres_aciklama'] ?: 'Açıklama girilmemiş.'; ?></div>
                    </div>
                        <?php endforeach;?>
                    <?php endif;?>
                    
                    <hr class="text-muted opacity-25">
                    <h5 class="text-primary small fw-bold text-uppercase mb-3 mt-4"><i class="fa-solid fa-stethoscope me-2"></i>Klinik Durum</h5>
                    <div class="row mb-2">
                        <label class="col-sm-4 fw-bold text-muted small">Tanılar / Hastalıklar:</label>
                        <div class="col-sm-8 small lh-sm text-dark">
                            <?= !empty($hastaliklar) ? implode(', ', $hastaliklar) : '<em class="text-muted">Tanı girilmemiş</em>';?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 d-flex flex-column gap-4">
                <div class="bg-white rounded border shadow-sm overflow-hidden">
                    <ul class="nav nav-tabs nav-justified bg-light border-0" id="hastaTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active py-3 border-0" data-bs-toggle="tab" data-bs-target="#geneltab" type="button">
                                <i class="fa-solid fa-circle-info fa-lg d-block mb-1"></i><span class="small fw-bold">Genel</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-3 border-0 text-primary" data-bs-toggle="tab" data-bs-target="#pansumantab" type="button">
                                <i class="fa-solid fa-band-aid fa-lg d-block mb-1"></i><span class="small fw-bold">Pansuman</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-3 border-0 text-danger" data-bs-toggle="tab" data-bs-target="#sondatab" type="button">
                                <i class="fa-solid fa-syringe fa-lg d-block mb-1"></i><span class="small fw-bold">Sonda</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-3 border-0 text-warning" data-bs-toggle="tab" data-bs-target="#mamatab" type="button">
                                <i class="fa-solid fa-bottle-droplet fa-lg d-block mb-1"></i><span class="small fw-bold">Mama</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-3 border-0 text-info" data-bs-toggle="tab" data-bs-target="#beztab" type="button">
                                <i class="fa-solid fa-baby-carriage fa-lg d-block mb-1"></i><span class="small fw-bold">Bez</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-4" id="hastaTabContent" style="min-height: 250px;">
                        <div class="tab-pane fade show active" id="geneltab" role="tabpanel">
                            <div class="list-group list-group-flush small">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
    <span><i class="fa-solid fa-user-clock text-warning me-2"></i>Geçici Takipli:</span>
    <?php echo $hasta->gecici ? '<span class="badge bg-danger">Evet</span>' : '<span class="badge bg-success">Hayır</span>'; ?>
</div>

<div class="list-group-item d-flex justify-content-between align-items-center px-0">
    <span><i class="fa-solid fa-file-invoice text-primary me-2"></i>E-Rapor Hastası:</span>
    <?php echo $hasta->erapor ? '<span class="badge bg-danger">Evet</span>' : '<span class="badge bg-success">Hayır</span>'; ?>
</div>

<div class="list-group-item d-flex justify-content-between align-items-center px-0">
    <span><i class="fa-solid fa-mask-ventilator text-info me-2"></i>Oksijen Bağımlı:</span>
    <?php echo $hasta->o2bagimli ? '<span class="badge bg-danger">Evet</span>' : '<span class="badge bg-success">Hayır</span>'; ?>
</div>

<div class="list-group-item d-flex justify-content-between align-items-center px-0">
    <span><i class="fa-solid fa-kit-medical text-info me-2"></i>Ventilatör Bağımlı:</span>
    <?php echo $hasta->ventilator ? '<span class="badge bg-danger">Evet</span>' : '<span class="badge bg-success">Hayır</span>'; ?>
</div>

<div class="list-group-item d-flex justify-content-between align-items-center px-0">
    <span><i class="fa-solid fa-vial text-secondary me-2"></i>NG (Nazogastrik):</span>
    <?php echo $hasta->ng ? '<span class="badge bg-danger">Evet</span>' : '<span class="badge bg-success">Hayır</span>'; ?>
</div>

<div class="list-group-item d-flex justify-content-between align-items-center px-0">
    <span><i class="fa-solid fa-circle-dot text-danger me-2"></i>PEG:</span>
    <?php echo $hasta->peg ? '<span class="badge bg-danger">Evet</span>' : '<span class="badge bg-success">Hayır</span>'; ?>
</div>

<div class="list-group-item d-flex justify-content-between align-items-center px-0">
    <span><i class="fa-solid fa-syringe text-primary me-2"></i>Port:</span>
    <?php echo $hasta->port ? '<span class="badge bg-danger">Evet</span>' : '<span class="badge bg-success">Hayır</span>'; ?>
</div>

<div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
    <span><i class="fa-solid fa-toilet-paper-slash text-dark me-2"></i>Kolostomi:</span>
    <?php echo $hasta->kolostomi ? '<span class="badge bg-danger">Evet</span>' : '<span class="badge bg-success">Hayır</span>'; ?>
</div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pansumantab" role="tabpanel">
                            <?php if($hasta->pansuman): ?>
                                <div class="alert alert-primary d-flex align-items-center small"><i class="fa-solid fa-check-circle me-2"></i> Pansuman Takibi Var</div>
                                <p class="small text-muted">Son Tarih: <strong><?php echo $hasta->pansumantarihi; ?></strong></p>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted"><i class="fa-solid fa-hand-dots fa-3x mb-2 opacity-25"></i><p class="small">Kayıt yok.</p></div>
                            <?php endif; ?>
                        </div>
                        <div class="tab-pane fade" id="sondatab" role="tabpanel">
                            <?php if($hasta->sonda): ?>
                                <div class="alert alert-danger d-flex align-items-center small"><i class="fa-solid fa-syringe me-2"></i> Sonda Mevcut</div>
                                <p class="small text-muted">Takılma: <strong><?php echo $hasta->sondatarihi; ?></strong></p>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted"><i class="fa-solid fa-circle-xmark fa-3x mb-2 opacity-25"></i><p class="small">Kayıt yok.</p></div>
                            <?php endif; ?>
                        </div>
                        <div class="tab-pane fade" id="mamatab" role="tabpanel">
                            <?php if($hasta->mama): ?>
                                <div class="alert alert-warning d-flex align-items-center small"><i class="fa-solid fa-bowl-food me-2"></i> Mama Kullanımı</div>
                                <p class="small text-muted">Mama: <strong><?php echo $hasta->mamaadi; ?></strong></p>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted"><i class="fa-solid fa-wheat-awn-circle-exclamation fa-3x mb-2 opacity-25"></i><p class="small">Kayıt yok.</p></div>
                            <?php endif; ?>
                        </div>
                        <div class="tab-pane fade" id="beztab" role="tabpanel">
                            <?php if($hasta->bez): ?>
                                <div class="alert alert-info d-flex align-items-center small"><i class="fa-solid fa-layer-group me-2"></i> Bez Kullanımı</div>
                                <p class="small text-muted">Günlük: <strong><?php echo $hasta->bezsayisi; ?> Adet</strong></p>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted"><i class="fa-solid fa-box fa-3x mb-2 opacity-25"></i><p class="small">Kayıt yok.</p></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 border-top border-warning border-4 rounded">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0 fw-bold text-warning small"><i class="fa-solid fa-note-sticky me-2"></i>Hasta Notları</h6>
                        <button class="btn btn-warning btn-sm border-0 shadow-sm" data-bs-toggle="modal" data-bs-target="#changenotes">
                            <i class="fa-solid fa-plus text-dark"></i>
                        </button>
                    </div>
                    <div class="card-body bg-light" style="min-height: 120px; max-height: 300px; overflow-y: auto;">
    <?php 
$allNotes = json_decode($hasta->notes, true); 
if (!empty($allNotes) && is_array($allNotes)): 
    // Notları orijinal sırasıyla (index bozulmadan) alalım ama görsel olarak tersten dönelim
    $reversedNotes = array_reverse($allNotes, true); 
    foreach ($reversedNotes as $index => $note): 
?>
    <div class="p-3 bg-white rounded border border-warning shadow-sm mb-3 position-relative note-item">
    <button type="button" 
            class="btn btn-link text-danger position-absolute top-0 end-0 m-1 p-1" 
            onclick="deleteNote(this, <?php echo $hasta->id; ?>, <?php echo $index; ?>)"
            title="Notu Sil">
        <i class="fa-solid fa-trash-can shadow-sm"></i>
    </button>

        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2 pe-4">
            <span class="badge bg-warning text-dark small">
                <i class="fa-solid fa-calendar-day me-1"></i> <?php echo $note['date']; ?>
            </span>
        </div>
        <p class="mb-0 text-dark small" style="white-space: pre-wrap;"><?php echo htmlspecialchars($note['message']); ?></p>
    </div>
<?php 
    endforeach; 
else:
echo "Henüz not girilmemiş";
endif; 
?>
</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Tab Kaymalarını Önleyen CSS */
    #hastaTab .nav-item { flex: 1; min-width: 0; }
    #hastaTab .nav-link { border-radius: 0; white-space: nowrap; overflow: hidden; color: #6c757d; }
    #hastaTab .nav-link.active { background-color: #fff !important; border-bottom: 3px solid #0d6efd !important; color: #0d6efd !important; }
    .bg-light-warning { background-color: #fff9e6; }
    .font-monospace { font-family: 'Courier New', monospace; letter-spacing: 1px; }
</style>

<div class="modal fade" id="ek3hazirla" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?controller=Patient&action=generateEk3" method="post" target="_blank" class="modal-content border-0 shadow-lg">
            <input type="hidden" name="patient_id" value="<?php echo $hasta->id; ?>">
            
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-pdf me-2"></i>EK-3 Form Hazırlığı</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Formda yer alacak ek bilgileri seçiniz veya kontrol ediniz:</p>
                
                <div class="mb-3">
                    <label class="form-label fw-bold small">Rapor Türü</label>
                    <select class="form-select form-select-sm" name="report_type">
                        <option value="1">İlk Rapor</option>
                        <option value="2">Yenileme Raporu</option>
                        <option value="3">Durum Bildirir Rapor</option>
                    </select>
                </div>

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="include_history" name="include_history" checked>
                    <label class="form-check-label small" for="include_history">Geçmiş İzlemleri Formlara Ekle</label>
                </div>
            </div>
            
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">İptal</button>
                <button type="submit" class="btn btn-info btn-sm px-4 fw-bold text-white shadow-sm">
                    <i class="fa-solid fa-print me-1"></i> Formu Oluştur
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="changenotes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?controller=Patient&action=updateNotes" method="post" class="modal-content border-0 shadow-lg">
            <input type="hidden" name="id" value="<?php echo $hasta->id; ?>">
            
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-edit me-2"></i>Hasta Notu Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            
            <div class="modal-body p-4">
    <label class="form-label fw-bold small text-muted">Yeni Not Ekle:</label>
    <textarea class="form-control border-warning shadow-none" 
              name="new_note" 
              rows="4" 
              placeholder="Mesajınızı yazın (Tarih otomatik eklenecektir)..."
              style="resize: none;"></textarea>
    
    <div class="mt-3 p-2 bg-light rounded border">
        <small class="text-muted"><i class="fa-solid fa-info-circle me-1"></i> Önceki notlar korunacak ve bu not listenin başına eklenecektir.</small>
    </div>
</div>
            
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="clearNotesArea()">
                    <i class="fa-solid fa-trash-can me-1"></i> Tümünü Sil
                </button>
                <div class="ms-auto">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-warning btn-sm px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-save me-1"></i> Kaydet
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="shownotes" tabindex="-1" aria-labelledby="shownotesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold" id="shownotesLabel">
                    <i class="fa-solid fa-sticky-note me-2"></i> Önemli Hasta Notları
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body p-3 bg-light" style="max-height: 450px; overflow-y: auto;">
                <?php 
$allNotes = json_decode($hasta->notes, true); 
if (!empty($allNotes) && is_array($allNotes)): 
    // Notları orijinal sırasıyla (index bozulmadan) alalım ama görsel olarak tersten dönelim
    $reversedNotes = array_reverse($allNotes, true); 
    foreach ($reversedNotes as $index => $note): 
?>
    <div class="p-3 bg-white rounded border border-warning shadow-sm mb-3 position-relative note-item">
    <button type="button" 
            class="btn btn-link text-danger position-absolute top-0 end-0 m-1 p-1" 
            onclick="deleteNote(this, <?php echo $hasta->id; ?>, <?php echo $index; ?>)"
            title="Notu Sil">
        <i class="fa-solid fa-trash-can shadow-sm"></i>
    </button>

        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2 pe-4">
            <span class="badge bg-warning text-dark small">
                <i class="fa-solid fa-calendar-day me-1"></i> <?php echo $note['date']; ?>
            </span>
        </div>
        <p class="mb-0 text-dark small" style="white-space: pre-wrap;"><?php echo htmlspecialchars($note['message']); ?></p>
    </div>
<?php 
    endforeach; 
endif; 
?>
            </div>
            <div class="modal-footer bg-white border-top-0">
                <small class="text-muted me-auto small">
                    <i class="fa-solid fa-info-circle me-1"></i> Toplam <?php echo !empty($allNotes) ? count($allNotes) : 0; ?> not bulundu.
                </small>
                <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-warning btn-sm px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#changenotes">
                    <i class="fa-solid fa-plus me-1"></i> Yeni Not Ekle
                </button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    // Sayfa açıldığında not varsa modalı göster
    <?php if (!empty($hasta->notes) && strlen($hasta->notes) > 3) { ?>
        var noteModal = new bootstrap.Modal(document.getElementById('shownotes'));
        noteModal.show(); // Tercihen otomatik açılabilir
    <?php } ?>
});

// Not alanını temizleme fonksiyonu
function clearNotesArea() {
    if(confirm('Notun tamamını silmek istediğinize emin misiniz?')) {
        document.querySelector('textarea[name="new_note"]').value = '';
    }
}

// Modal açıldığında textarea'nın sonuna odaklanma (opsiyonel)
$('#changenotes').on('shown.bs.modal', function () {
    const textarea = $(this).find('textarea');
    textarea.focus();
    const val = textarea.val();
    textarea.val('').val(val); // Kürsörü sona taşır
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


function deleteNote(btn, hastaId, noteIndex) {
    if (!confirm('Bu notu kalıcı olarak silmek istediğinize emin misiniz?')) return;

    // Tıklanan butonu bir değişkene alalım
    const $button = $(btn);
    const $noteBox = $button.closest('.note-item'); // Notun tüm kutusunu bulur

    // Butonu geçici olarak kilitleyelim (çift tıklamayı önlemek için)
    $button.prop('disabled', true);

    $.ajax({
        url: 'index.php?controller=Patient&action=deleteNote',
        type: 'POST',
        data: { 
            id: hastaId, 
            index: noteIndex 
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                toastr.success("Not başarıyla silindi.");
                
                // Sayfayı yenilemek yerine kutuyu animasyonla kaldırıyoruz
                $noteBox.fadeOut(400, function() {
                    $(this).remove(); // Animasyon bittikten sonra DOM'dan tamamen siler
                    
                    // Opsiyonel: Eğer hiç not kalmadıysa "Not yok" mesajı basabilirsin
                    if ($('.note-item').length === 0) {
                        $('.modal-body').html('<div class="text-center py-5 text-muted"><i class="fa-solid fa-note-sticky fa-3x mb-3 opacity-25"></i><p>Tüm notlar silindi.</p></div>');
                    }
                });
            } else {
                toastr.error("Hata: " + response.message);
                $button.prop('disabled', false); // Hata durumunda butonu tekrar aç
            }
        },
        error: function() {
            toastr.error("Sunucu hatası oluştu.");
            $button.prop('disabled', false);
        }
    });
}
</script>