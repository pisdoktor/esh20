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
                                <label class="form-label small fw-bold text-muted">Cinsiyet</label>
                                <select name="cinsiyet" class="form-select">
                                    <option value="1" <?= $patient->cinsiyet == '1' ? 'selected':'' ?>>Erkek</option>
                                    <option value="2" <?= $patient->cinsiyet == '2' ? 'selected':'' ?>>Kadın</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Doğum Tarihi</label>
                                <input type="date" name="dogumtarihi" class="form-control" value="<?= $patient->dogumtarihi ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Kayıt Tarihi</label>
                                <input type="date" name="kayittarihi" class="form-control" value="<?= $patient->kayittarihi ?? date('Y-m-d') ?>">
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
                    <div class="card-header bg-dark text-white fw-bold py-3 small"><i class="fa-solid fa-chart-line me-2"></i> Barthel İndeksi (Bağımlılık Durumu)</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <?php 
                            $barthelFields = [
                                'barbeslenme' => 'Beslenme', 'barbanyo' => 'Banyo', 'barbakim' => 'Kişisel Bakım', 
                                'bargiyinme' => 'Giyinme', 'barbarsak' => 'Bağırsak', 'barmesane' => 'Mesane', 
                                'bartuvalet' => 'Tuvalet', 'bartransfer' => 'Transfer', 'barmobilite' => 'Mobilite', 
                                'barmerdiven' => 'Merdiven'
                            ];
                            foreach($barthelFields as $key => $label): ?>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text w-50 small fw-bold"><?= $label ?></span>
                                        <input type="number" name="<?= $key ?>" class="form-control" value="<?= $patient->$key ?>" min="0" max="15">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="col-12 mt-2">
                                <label class="small fw-bold">Genel Bağımlılık Skoru / Notu</label>
                                <input type="text" name="bagimlilik" class="form-control" value="<?= $patient->bagimlilik ?>" placeholder="Skor veya açıklama giriniz">
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
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="sonda" value="1" id="sondaCheck" <?= $patient->sonda ? 'checked':'' ?>>
                                    <label class="form-check-label small fw-bold">Sonda Takılı mı?</label>
                                </div>
                                <label class="x-small text-muted d-block">Sonda Tarihi</label>
                                <input type="date" name="sondatarihi" class="form-control form-control-sm" value="<?= $patient->sondatarihi ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-warning text-dark fw-bold py-3"><i class="fa-solid fa-box-open me-2"></i> Bakım ve Sarf Malzeme</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6 border-end border-light">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="mama" value="1" <?= $patient->mama ? 'checked':'' ?>>
                                    <label class="form-check-label small fw-bold">Mama Kullanımı</label>
                                </div>
                                <input type="text" name="mamacesit" class="form-control form-control-sm mb-1" placeholder="Mama Çeşidi" value="<?= $patient->mamacesit ?>">
                                <input type="date" name="mamaraporbitis" class="form-control form-control-sm mb-1" title="Rapor Bitiş Tarihi" value="<?= $patient->mamaraporbitis ?>">
                                <input type="text" name="mamaraporyeri" class="form-control form-control-sm" placeholder="Rapor Yeri" value="<?= $patient->mamaraporyeri ?>">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="bez" value="1" <?= $patient->bez ? 'checked':'' ?>>
                                    <label class="form-check-label small fw-bold">Bez Kullanımı</label>
                                </div>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="bezrapor" value="1" <?= $patient->bezrapor ? 'checked':'' ?>>
                                    <label class="form-check-label small">Bezi Raporu Var mı?</label>
                                </div>
                                <input type="date" name="bezraporbitis" class="form-control form-control-sm" title="Bez Rapor Bitiş" value="<?= $patient->bezraporbitis ?>">
                            </div>
                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="pansuman" value="1" <?= $patient->pansuman ? 'checked':'' ?>>
                                    <label class="form-check-label small fw-bold">Pansuman</label>
                                </div>
                                <input type="text" name="pgunleri" class="form-control form-control-sm mb-1" placeholder="Günler (Örn: Pzt-Per)" value="<?= $patient->pgunleri ?>">
                                <input type="text" name="pzaman" class="form-control form-control-sm" placeholder="Zaman (Örn: Sabah)" value="<?= $patient->pzaman ?>">
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
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="pasif" value="1" id="pasifSwitch" <?= $patient->pasif ? 'checked':'' ?>>
                                    <label class="form-check-label small fw-bold text-danger">Dosya Pasif</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <input type="date" name="pasiftarihi" class="form-control form-control-sm" value="<?= $patient->pasiftarihi ?>">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pasifnedeni" class="form-control form-control-sm" placeholder="Pasif Nedeni" value="<?= $patient->pasifnedeni ?>">
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
    // Chosen Initialization
    function initChosen() {
        $('.chosen-select').chosen({ width: '100%', allow_single_deselect: true });
    }
    initChosen();

    // Bootstrap Form Validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                // İlk hatalı alana odaklan
                $('html, body').animate({
                    scrollTop: $(form).find(":invalid").first().offset().top - 100
                }, 200);
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Cascade Ajax Handler (Generic)
    function handleCascadeChange(triggerSelector, targetSelector, type, nextPlaceholder) {
        $(document).on('change', triggerSelector, function() {
            const parentId = $(this).val();
            const $row = $(this).closest('.row');
            const $targetSelect = $row.find(targetSelector);
            
            // Altındaki tüm bağımlı selectleri sıfırla
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
        // İlk ilçeyi klonlamak yerine temiz bir html template kullanmak daha güvenlidir
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
</script>

<style>
    /* Küçük UX dokunuşları için CSS */
    .card { transition: all 0.3s ease; }
    .form-control:focus, .form-select:focus { border-color: #80bdff; box-shadow: 0 0 0 0.2rem rgba(0,123,255,.15); }
    .x-small { font-size: 0.75rem; }
    .sticky-bottom { z-index: 1020; border-top: 1px solid #dee2e6; }
    .custom-switch .form-check-input { width: 2.5em; cursor: pointer; }
    .address-row { border-left: 4px solid #198754 !important; }
</style>