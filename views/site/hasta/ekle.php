<div class="container-fluid py-4">
    <form action="index.php?controller=Patient&action=store" method="POST" id="patientForm" class="needs-validation" novalidate>
        <input type="hidden" name="id" value="<?= $patient->id ?>">

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-primary text-white fw-bold py-3"><i class="fa-solid fa-id-card me-2"></i> Kimlik ve İletişim</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">TC Kimlik No</label>
                                <input type="text" name="tckimlik" class="form-control" maxlength="11" pattern="\d{11}" value="<?= $patient->tckimlik ?>" required title="11 haneli TC kimlik numarası giriniz.">
                                <div class="invalid-feedback small">Geçerli bir TC No giriniz.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Ad</label>
                                <input type="text" name="isim" class="form-control" value="<?= $patient->isim ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Soyad</label>
                                <input type="text" name="soyisim" class="form-control" value="<?= $patient->soyisim ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Anne Adı</label>
                                <input type="text" name="anneAdi" class="form-control" value="<?= $patient->anneAdi ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Baba Adı</label>
                                <input type="text" name="babaAdi" class="form-control" value="<?= $patient->babaAdi ?>">
                            </div>
                            <div class="col-md-4">
    <label class="form-label small fw-bold text-muted d-block">Cinsiyet</label>
    <div class="btn-group w-100" role="group" aria-label="Cinsiyet Seçimi">
        <input type="radio" class="btn-check" name="cinsiyet" id="genderMale" value="1" 
            <?= ($patient->cinsiyet == '1' || empty($patient->cinsiyet)) ? 'checked' : '' ?> autocomplete="off">
        <label class="btn btn-outline-primary shadow-sm py-2" for="genderMale">
            <i class="fa-solid fa-mars me-1"></i> Erkek
        </label>

        <input type="radio" class="btn-check" name="cinsiyet" id="genderFemale" value="2" 
            <?= ($patient->cinsiyet == '2') ? 'checked' : '' ?> autocomplete="off">
        <label class="btn btn-outline-danger shadow-sm py-2" for="genderFemale">
            <i class="fa-solid fa-venus me-1"></i> Kadın
        </label>
    </div>
</div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Doğum Tarihi</label>
                                <input type="text" name="dogumtarihi" class="form-control datepicker" value="<?= !empty($patient->dogumtarihi) ? date('d.m.Y', strtotime($patient->dogumtarihi)) : '' ?>" placeholder="GG.AA.YYYY">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Kayıt Tarihi</label>
                                <input type="text" name="kayittarihi" class="form-control datepicker" value="<?= !empty($patient->kayittarihi) ? date('d.m.Y', strtotime($patient->kayittarihi)) : date('d.m.Y') ?>" placeholder="GG.AA.YYYY">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Güvence</label>
                                <select name="guvence" class="form-select chosen-select">
                                    <option value="">Seçiniz...</option>
                                    <?php foreach($guvence as $g): ?>
                                        <option value="<?= $g->id ?>" <?= $patient->guvence == $g->id ? 'selected':'' ?>><?= $g->guvenceadi ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Telefon 1 (Cep)</label>
                                <input type="text" name="ceptel1" class="form-control phone-mask" value="<?= $patient->ceptel1 ?>" required placeholder="05XX XXX XX XX">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Telefon 2</label>
                                <input type="text" name="ceptel2" class="form-control" value="<?= $patient->ceptel2 ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-success text-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-map-location-dot me-2"></i> Adres Bilgileri</span>
                        <button type="button" class="btn btn-light btn-sm fw-bold" id="btn-add-address"><i class="fa-solid fa-plus me-1"></i> Yeni Adres</button>
                    </div>
                    <div class="card-body">
                        <div id="address-container">
                            <div class="p-3 border rounded bg-light mb-3 address-row">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="small fw-bold">İlçe</label>
                                        <select name="adres[0][ilce]" class="form-select ilce-trigger">
                                            <option value="">Seçiniz...</option>
                                            <?php foreach($districts as $d): ?>
                                                <option value="<?= $d->id ?>" <?= $patient->ilce == $d->id ? 'selected':'' ?>><?= $d->adi ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">Mahalle</label>
                                        <select name="adres[0][mahalle]" class="form-select mahalle-target mahalle-trigger" disabled>
                                            <option value="">İlçe Seçin...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">Sokak/Cadde</label>
                                        <select name="adres[0][sokak]" class="form-select sokak-target sokak-trigger" disabled>
                                            <option value="">Mahalle Seçin...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">Kapı No</label>
                                        <select name="adres[0][kapino]" class="form-select kapino-target" disabled>
                                            <option value="">Sokak Seçin...</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <textarea name="adres[0][adres_aciklama]" class="form-control" rows="2" placeholder="Adres Açıklaması..."><?= $patient->adres_aciklama ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-dark text-white fw-bold py-3 small d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-chart-line me-2"></i> Barthel İndeksi</span>
        <span class="badge bg-light text-dark shadow-sm" id="barthel-total-badge">Toplam Skor: 0</span>
    </div>
    <div class="card-body">
        <div class="row g-2" id="barthel-fields-container">
            <?php 
            foreach($barthelFields as $key => $data): ?>
    <div class="col-md-6">
        <div class="input-group input-group-sm">
            <span class="input-group-text w-50 small fw-bold" 
                  data-bs-toggle="tooltip" 
                  data-bs-placement="top" 
                  title="<?= $data['desc'] ?>" 
                  style="cursor: help;">
                <?= $data['label'] ?> <i class="fa-solid fa-circle-info ms-1 text-muted opacity-50"></i>
            </span>
            <input type="number" name="<?= $key ?>" class="form-control barthel-input" 
                   value="<?= (int)$patient->$key ?>" min="0" max="<?= $data['max'] ?>">
        </div>
    </div>
<?php endforeach; ?>
            <div class="col-12 mt-3">
                <div class="alert alert-info py-2 mb-2 d-flex justify-content-between align-items-center">
                    <span class="small fw-bold">Bağımlılık Durumu:</span>
                    <span id="barthel-status" class="fw-bold small">-</span>
                </div>
                <label class="small fw-bold">Genel Not</label>
                <input type="text" name="bagimlilik" id="bagimlilik-input" class="form-control" 
                       value="<?= $patient->bagimlilik ?>" placeholder="Skor otomatik hesaplanır...">
            </div>
        </div>
    </div>
</div>

            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-danger text-white fw-bold py-3"><i class="fa-solid fa-microchip me-2"></i> Tıbbi Cihaz ve Destek</div>
                    <div class="card-body">
                        <div class="row">
                            <?php $devices = ['ng' => 'NG (Beslenme Tüpü)', 'peg' => 'PEG', 'port' => 'Port Kateter', 'o2bagimli' => 'Oksijen Bağımlı', 'ventilator' => 'Ventilatör', 'kolostomi' => 'Kolostomi']; 
                            foreach($devices as $key => $label): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check form-switch custom-switch">
                                        <input class="form-check-input" type="checkbox" name="<?= $key ?>" value="1" <?= $patient->$key ? 'checked':'' ?>>
                                        <label class="form-check-label small fw-bold"><?= $label ?></label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="col-12"><hr class="my-2 text-muted"></div>
                            <div class="col-md-6">
    <label class="form-label small fw-bold text-muted d-block">Sonda Takılı mı?</label>
    <div class="btn-group w-100 mb-2" role="group">
        <input type="radio" class="btn-check" name="sonda" id="sondaYok" value="0" <?= !$patient->sonda ? 'checked' : '' ?>>
        <label class="btn btn-outline-secondary btn-sm" for="sondaYok">Hayır</label>

        <input type="radio" class="btn-check" name="sonda" id="sondaVar" value="1" <?= $patient->sonda ? 'checked' : '' ?>>
        <label class="btn btn-outline-primary btn-sm" for="sondaVar">Evet</label>
    </div>

    <div id="sondaTarihiArea" style="<?= !$patient->sonda ? 'display: none;' : '' ?>">
        <label class="x-small text-muted d-block">Sonda Değişim / Takılma Tarihi</label>
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fa-solid fa-calendar-day"></i></span>
            <input type="text" name="sondatarihi" class="form-control datepicker" 
                   value="<?= !empty($patient->sondatarihi) ? date('d.m.Y', strtotime($patient->sondatarihi)) : '' ?>" 
                   placeholder="GG.AA.YYYY">
        </div>
    </div>
</div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-warning text-dark fw-bold py-3"><i class="fa-solid fa-box-open me-2"></i> Bakım ve Sarf Malzeme</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6 border-end border-light">
                <label class="form-label small fw-bold text-muted d-block">Mama Kullanımı</label>
                <div class="btn-group w-100 mb-3" role="group">
                    <input type="radio" class="btn-check" name="mama" id="mamaYok" value="0" <?= !$patient->mama ? 'checked' : '' ?>>
                    <label class="btn btn-outline-secondary btn-sm" for="mamaYok">Hayır</label>

                    <input type="radio" class="btn-check" name="mama" id="mamaVar" value="1" <?= $patient->mama ? 'checked' : '' ?>>
                    <label class="btn btn-outline-primary btn-sm" for="mamaVar">Evet</label>
                </div>

                <div id="mamaDetailsArea" style="<?= !$patient->mama ? 'display: none;' : '' ?>">
                    <input type="text" name="mamacesit" class="form-control form-control-sm mb-1" placeholder="Mama Çeşidi" value="<?= $patient->mamacesit ?>">
                    <input type="text" name="mamaraporbitis" class="form-control form-control-sm mb-1 datepicker" title="Rapor Bitiş Tarihi" value="<?= !empty($patient->mamaraporbitis) ? date('d.m.Y', strtotime($patient->mamaraporbitis)) : '' ?>" placeholder="Rapor Bitiş GG.AA.YYYY">
                    <input type="text" name="mamaraporyeri" class="form-control form-control-sm" placeholder="Rapor Yeri" value="<?= $patient->mamaraporyeri ?>">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted d-block">Bez Kullanımı</label>
                <div class="btn-group w-100 mb-3" role="group">
                    <input type="radio" class="btn-check" name="bez" id="bezYok" value="0" <?= !$patient->bez ? 'checked' : '' ?>>
                    <label class="btn btn-outline-secondary btn-sm" for="bezYok">Hayır</label>

                    <input type="radio" class="btn-check" name="bez" id="bezVar" value="1" <?= $patient->bez ? 'checked' : '' ?>>
                    <label class="btn btn-outline-primary btn-sm" for="bezVar">Evet</label>
                </div>

                <div id="bezDetailsArea" style="<?= !$patient->bez ? 'display: none;' : '' ?>">
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="bezrapor" value="1" <?= $patient->bezrapor ? 'checked' : '' ?>>
                        <label class="form-check-label small">Bezi Raporu Var mı?</label>
                    </div>
                    <input type="text" name="bezraporbitis" class="form-control form-control-sm datepicker" title="Bez Rapor Bitiş" value="<?= !empty($patient->bezraporbitis) ? date('d.m.Y', strtotime($patient->bezraporbitis)) : '' ?>" placeholder="Bez Rapor Bitiş GG.AA.YYYY">
                </div>
            </div>
                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-md-6 border-start border-light">
    <label class="form-label small fw-bold text-muted d-block">Pansuman</label>
    <div class="btn-group w-100 mb-2" role="group">
        <input type="radio" class="btn-check" name="pansuman" id="pansumanYok" value="0" <?= !$patient->pansuman ? 'checked' : '' ?>>
        <label class="btn btn-outline-secondary btn-sm" for="pansumanYok">Hayır</label>

        <input type="radio" class="btn-check" name="pansuman" id="pansumanVar" value="1" <?= $patient->pansuman ? 'checked' : '' ?>>
        <label class="btn btn-outline-primary btn-sm" for="pansumanVar">Evet</label>
    </div>
    
    <div id="pansumanDetailsArea" style="<?= !$patient->pansuman ? 'display: none;' : '' ?>">
        <input type="text" name="pgunleri" class="form-control form-control-sm mb-1" placeholder="Günler (Örn: Pzt-Per)" value="<?= $patient->pgunleri ?>">
        <input type="text" name="pzaman" class="form-control form-control-sm" placeholder="Zaman (Örn: Sabah)" value="<?= $patient->pzaman ?>">
    </div>
</div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="yatak" value="1" <?= $patient->yatak ? 'checked':'' ?>>
                                    <label class="form-check-label small fw-bold">Hasta Yatağı</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 border-danger border-start border-4">
                    <div class="card-header bg-white text-danger fw-bold small border-0">Dosya Durumu</div>
                    <div class="card-body py-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted d-block">Dosya Aktif mi?</label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="pasif" id="pasifHayir" value="0" <?= !$patient->pasif ? 'checked' : '' ?>>
                    <label class="btn btn-outline-success btn-sm" for="pasifHayir">Aktif</label>

                    <input type="radio" class="btn-check" name="pasif" id="pasifEvet" value="1" <?= $patient->pasif ? 'checked' : '' ?>>
                    <label class="btn btn-outline-danger btn-sm" for="pasifEvet">Pasif</label>
                </div>
            </div>
            
            <div class="col-md-8">
                <div id="pasifDetailsArea" style="<?= !$patient->pasif ? 'display: none;' : '' ?>">
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="text" name="pasiftarihi" class="form-control form-control-sm datepicker" value="<?= !empty($patient->pasiftarihi) ? date('d.m.Y', strtotime($patient->pasiftarihi)) : '' ?>" placeholder="Pasif Tarihi">
                        </div>
                        <div class="col-6">
                            <input type="text" name="pasifnedeni" class="form-control form-control-sm" placeholder="Pasif Nedeni" value="<?= $patient->pasifnedeni ?>">
                        </div>
                    </div>
                </div>
            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small fw-bold mb-2">Tanılı Hastalıklar</label>
                            <select name="hastaliklar[]" class="form-select chosen-select" multiple data-placeholder="Hastalıkları seçiniz..." required>
                                <?php foreach($hastaliklar as $h): ?>
                                    <option value="<?= $h->id ?>" <?= $patient->hastaliklar == $h->id ? 'selected':'' ?>><?= $h->hastalikadi ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-2">Genel Notlar</label>
                            <textarea name="notes" class="form-control" rows="4" placeholder="Hasta hakkında önemli notlar..."><?= $patient->notes ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow border-0 sticky-bottom mt-4">
            <div class="card-body text-center p-3 bg-light">
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm rounded-pill"><i class="fa-solid fa-floppy-disk me-2"></i> Kaydı Tamamla</button>
                <a href="index.php?controller=Patient&action=view&id=<?= $patient->id;?>" class="btn btn-link btn-lg text-secondary text-decoration-none ms-2">İptal</a>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Chosen Initialization (Global JS'deki ayarlarla uyumlu)
    function initChosen() {
        $('.chosen-select').chosen({ 
            width: '100%', 
            allow_single_deselect: true,
            no_results_text: "Sonuç bulunamadı: ",
            placeholder_text_single: "Seçiniz...",
            placeholder_text_multiple: "Hastalıkları seçiniz..."
        });
    }
    initChosen();

    // Bootstrap Form Validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                $('html, body').animate({
                    scrollTop: $(form).find(":invalid").first().offset().top - 100
                }, 200);
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Cascade Ajax Handler
    function handleCascadeChange(triggerSelector, targetSelector, type, nextPlaceholder) {
        $(document).on('change', triggerSelector, function() {
            const parentId = $(this).val();
            const $row = $(this).closest('.row');
            const $targetSelect = $row.find(targetSelector);
            const $allTargets = $row.find('.mahalle-target, .sokak-target, .kapino-target').slice($row.find('.mahalle-target, .sokak-target, .kapino-target').index($targetSelect));
            
            if(parentId) {
                $targetSelect.html('<option>Yükleniyor...</option>').prop('disabled', false);
                $.getJSON(`index.php?controller=Address&action=getSubAddresses&parent_id=${parentId}&type=${type}`, function(data) {
                    let options = `<option value="">${nextPlaceholder} Seçiniz</option>`;
                    data.forEach(item => options += `<option value="${item.id}">${item.adi}</option>`);
                    $targetSelect.html(options).trigger("chosen:updated");
                });
            } else {
                $allTargets.html('<option value="">Seçiniz...</option>').prop('disabled', true).trigger("chosen:updated");
            }
        });
    }

    handleCascadeChange('.ilce-trigger', '.mahalle-target', 'mahalle', 'Mahalle');
    handleCascadeChange('.mahalle-trigger', '.sokak-target', 'sokak', 'Sokak');
    handleCascadeChange('.sokak-trigger', '.kapino-target', 'kapino', 'Kapı No');

    // Çoklu Adres Ekleme
    let addrCount = 1;
    $('#btn-add-address').click(function() {
        const ilceOptions = $('.ilce-trigger').first().html();
        const html = `
        <div class="p-4 border rounded bg-white mb-3 position-relative shadow-sm border-start border-success border-4 animate__animated animate__fadeIn">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-addr" title="Adresi Kaldır"></button>
            <h6 class="mb-3 text-success fw-bold small"><i class="fa-solid fa-location-dot me-2"></i>Ek Adres</h6>
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">İlçe</label>
                    <select name="adres[${addrCount}][ilce]" class="form-select ilce-trigger">
                        ${ilceOptions}
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">Mahalle</label>
                    <select name="adres[${addrCount}][mahalle]" class="form-select mahalle-target mahalle-trigger" disabled>
                        <option value="">İlçe Seçin...</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">Sokak/Cadde</label>
                    <select name="adres[${addrCount}][sokak]" class="form-select sokak-target sokak-trigger" disabled>
                        <option value="">Mahalle Seçin...</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold text-muted">Kapı No</label>
                    <select name="adres[${addrCount}][kapino]" class="form-select kapino-target" disabled>
                        <option value="">Sokak Seçin...</option>
                    </select>
                </div>
                <div class="col-12 mt-2">
                    <textarea name="adres[${addrCount}][adres_aciklama]" class="form-control" rows="2" placeholder="Diğer Adres Açıklaması..."></textarea>
                </div>
            </div>
        </div>`;
        $('#address-container').append(html);
        addrCount++;
    });

    $(document).on('click', '.remove-addr', function() {
        if(confirm('Bu adresi silmek istediğinize emin misiniz?')) {
            $(this).closest('.p-4').fadeOut(300, function() { $(this).remove(); });
        }
    });
});

$(document).ready(function() {
    
    // Ortak Slide Fonksiyonu (Daha temiz bir kod için)
    function toggleSlide(inputName, targetId) {
        $(document).on('change', `input[name="${inputName}"]`, function() {
            if ($(this).val() == '1') {
                $(`#${targetId}`).slideDown(300);
            } else {
                $(`#${targetId}`).slideUp(300);
                // İsteğe bağlı: Kapandığında içindeki inputları temizlemek istersen:
                // $(`#${targetId} input`).val(''); 
            }
        });
    }

    // Tetikleyicileri Başlat
    toggleSlide('sonda', 'sondaTarihiArea');
    toggleSlide('mama', 'mamaDetailsArea');
    toggleSlide('bez', 'bezDetailsArea');
    toggleSlide('pansuman', 'pansumanDetailsArea');
    toggleSlide('pasif', 'pasifDetailsArea'); // Dosya Pasif seçilirse (val:1) detaylar açılır

});

$(document).ready(function() {
    function calculateBarthel() {
        let total = 0;
        
        // Tüm barthel inputlarını topla
        $('.barthel-input').each(function() {
            let val = parseInt($(this).val()) || 0;
            total += val;
        });

        // Skor badge'ini güncelle
        $('#barthel-total-badge').text('Toplam Skor: ' + total);
        
        // Bağımlılık durumunu belirle
        let status = '';
        let colorClass = '';

        if (total <= 20) { status = 'Tam Bağımlı'; colorClass = 'text-danger'; }
        else if (total <= 60) { status = 'İleri Derecede Bağımlı'; colorClass = 'text-warning'; }
        else if (total <= 90) { status = 'Orta Derecede Bağımlı'; colorClass = 'text-info'; }
        else if (total <= 99) { status = 'Hafif Derecede Bağımlı'; colorClass = 'text-primary'; }
        else { status = 'Tam Bağımsız'; colorClass = 'text-success'; }

        // Görsel güncellemeler
        $('#barthel-status').text(status).removeClass().addClass('fw-bold small ' + colorClass);
        
        // İstersen otomatik olarak "bagimlilik" inputuna da yazdırabilirsin
        $('#bagimlilik-input').val(total + ' Puan - ' + status);
    }

    // Herhangi bir input değiştiğinde hesapla
    $(document).on('input change', '.barthel-input', function() {
        calculateBarthel();
    });

    // Sayfa açıldığında ilk hesaplamayı yap (Düzenleme modu için)
    calculateBarthel();
});
</script>