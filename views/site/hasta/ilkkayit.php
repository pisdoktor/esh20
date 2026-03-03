<form action="index.php?controller=Patient&action=fsave" method="post" class="needs-validation" novalidate id="patientForm">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fa-solid fa-user-plus me-2"></i>Yeni Hasta İlk Kayıt
            </h5>
            <small class="opacity-75">* Zorunlu alanları lütfen doldurunuz</small>
        </div>

        <div class="card-body bg-light-50 p-4">
            <div class="row g-4">
                
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-info text-white fw-bold">
                            <i class="fa-solid fa-id-card me-2"></i> Kimlik ve Kişisel Bilgiler
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">TC Kimlik Numarası:</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-white"><i class="fa-solid fa-fingerprint text-primary"></i></span>
                                    <input type="text" maxlength="11" id="tckimlik" name="tckimlik" class="form-control" required placeholder="11 Haneli TC Kimlik No">
                                    <div class="invalid-feedback">Geçerli bir TC Kimlik numarası giriniz.</div>
                                </div>
                                <div id="sonuc" class="form-text mt-1"></div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Adı:</label>
                                    <input type="text" name="isim" class="form-control" required style="text-transform: uppercase;" placeholder="Örn: AHMET">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Soyadı:</label>
                                    <input type="text" name="soyisim" class="form-control" required style="text-transform: uppercase;" placeholder="Örn: YILMAZ">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold d-block">Cinsiyeti:</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="cinsiyet" id="gender-m" value="1" required>
                                    <label class="btn btn-outline-primary py-2" for="gender-m"><i class="fa-solid fa-person me-2"></i>Erkek</label>

                                    <input type="radio" class="btn-check" name="cinsiyet" id="gender-f" value="2" required>
                                    <label class="btn btn-outline-danger py-2" for="gender-f"><i class="fa-solid fa-person-dress me-2"></i>Kadın</label>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Anne Adı:</label>
                                    <input type="text" name="anneAdi" class="form-control" style="text-transform: uppercase;" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Baba Adı:</label>
                                    <input type="text" name="babaAdi" class="form-control" style="text-transform: uppercase;" required>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold">Doğum Tarihi:</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fa-solid fa-calendar-day"></i></span>
                                    <input type="text" id="dogumtarihi" name="dogumtarihi" class="form-control datepicker" placeholder="GG.AA.YYYY"  autocomplete="off" required style="background-color: #fff;">
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold">Güvence:</label>
                                <select name="guvence" class="form-select chosen-select">
                                    <option value="">Seçiniz...</option>
                                    <?php foreach($guvence as $g): ?>
                                        <option value="<?= $g->id ?>"><?= $g->guvenceadi ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div> <div class="card border-0 shadow-sm">
                        <div class="card-header bg-warning text-dark fw-bold">
                            <i class="fa-solid fa-phone me-2"></i> İletişim Bilgileri
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Cep Telefonu 1:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-mobile-screen"></i></span>
                                        <input type="text" id="telefon1" name="ceptel1" class="form-control tel-mask" placeholder="0 (5xx) xxx xx xx" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Cep Telefonu 2:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                        <input type="text" id="telefon2" name="ceptel2" class="form-control tel-mask" placeholder="0 (5xx) xxx xx xx">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> </div> <div class="col-lg-6">
                    <div class="card shadow-sm border-0 mb-4 border-top border-success border-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <span class="fw-bold text-success"><i class="fa-solid fa-map-location-dot me-2"></i> Adres Bilgileri</span>
                            <button type="button" class="btn btn-outline-success btn-sm fw-bold" id="btn-add-address"><i class="fa-solid fa-plus me-1"></i> Yeni Adres</button>
                        </div>
                        <div class="card-body">
                            <div id="address-container">
                                <div class="p-3 border rounded bg-light mb-3 address-row">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="small fw-bold">İlçe</label>
                                            <select name="adres[0][ilce]" id="ilce" class="form-select ilce-trigger chosen-select" required>
                                                <option value="">Seçiniz...</option>
                                                <?php foreach($districts as $d): ?>
                                                <option value="<?= $d->id ?>"><?= $d->adi ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold">Mahalle</label>
                                            <select name="adres[0][mahalle]" id="mahalle" class="form-select mahalle-target mahalle-trigger chosen-select" disabled>
                                                <option value="">İlçe Seçin...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold">Sokak/Cadde</label>
                                            <select name="adres[0][sokak]" id="sokak" class="form-select sokak-target sokak-trigger chosen-select" disabled>
                                                <option value="">Mahalle Seçin...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold">Kapı No</label>
                                            <select name="adres[0][kapino]" id="kapino" class="form-select kapino-target chosen-select" disabled>
                                                <option value="">Sokak Seçin...</option>
                                            </select>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <textarea name="adres[0][adres_aciklama]" class="form-control" rows="2" placeholder="Adres Açıklaması..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 p-3 bg-white border rounded shadow-sm">
                                <label class="form-label fw-bold small text-muted">Otomatik Koordinat (Asıl Adres):</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="fa-solid fa-location-crosshairs text-danger"></i></span>
                                    <input id="coords" type="text" name="coords" class="form-control bg-light" value="" placeholder="Enlem, Boylam" readonly>
                                </div>
                            </div>
                        </div>
                    </div> <div class="card border-0 shadow-sm">
                        <div class="card-header bg-secondary text-white fw-bold">
                            <i class="fa-solid fa-clipboard-list me-2"></i> Kayıt ve Randevu Detayları
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label fw-bold">E-Rapor Durumu:</label>
                                <div class="p-3 border rounded bg-white shadow-sm">
                                    <?php echo App\Helpers\FormHelper::switch('erapor', 'E-Rapor Hastası');?>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Sisteme Kayıt Tarihi:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-calendar-check text-success"></i></span>
                                        <input type="text" name="kayittarihi" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" required style="background-color: #fff;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">İlk Randevu Tarihi:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-calendar-plus text-info"></i></span>
                                        <input type="text" name="randevutarihi" class="form-control datepicker" placeholder="GG.AA.YYYY" autocomplete="off" style="background-color: #fff;">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Randevu Zaman Dilimi:</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="zaman" id="time-1" value="0" checked>
                                        <label class="btn btn-outline-warning py-2" for="time-1"><i class="fa-regular fa-sun me-1"></i>Sabah</label>

                                        <input type="radio" class="btn-check" name="zaman" id="time-2" value="1">
                                        <label class="btn btn-outline-danger py-2" for="time-2"><i class="fa-solid fa-sun me-1"></i>Öğle</label>

                                        <input type="radio" class="btn-check" name="zaman" id="time-3" value="2">
                                        <label class="btn btn-outline-primary py-2" for="time-3"><i class="fa-solid fa-moon me-1"></i>Akşam</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> </div> </div> </div> <div class="card-footer bg-white py-3 border-top">
            <div class="d-flex justify-content-end gap-2">
                <a href="javascript:history.go(-1);" class="btn btn-light border px-4 rounded-pill">
                    <i class="fa-solid fa-xmark me-2"></i>İptal
                </a>
                <button type="submit" id="save" class="btn btn-primary px-5 rounded-pill shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Hastayı Kaydet
                </button>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function() {
    // --- TEMEL BAŞLATICI ---
    function initPlugins(container) {
        const target = container || $(document);
        target.find(".chosen-select").chosen({ width: "100%", allow_single_deselect: true });
        target.find('.datepicker').datepicker({ format: "dd.mm.yyyy", autoclose: true, language: "tr" });
    }
    initPlugins();

    // --- YENİ ADRES SATIRI EKLEME ---
    let addrCount = 1;
    // Buton ID'sini HTML'dekiyle eşitledik: #btn-add-address
    $('#btn-add-address').on('click', function(e) {
        e.preventDefault();
        
        // İlk ilçedeki seçenekleri kopyalayalım
        const ilceOptions = $('.ilce-trigger').first().html();
        
        const html = `
        <div class="p-3 border rounded bg-white mb-3 address-row position-relative animate__animated animate__fadeIn">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-addr"></button>
            <h6 class="text-success small fw-bold mb-3"><i class="fa-solid fa-location-dot me-1"></i> Ek Adres</h6>
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="small fw-bold">İlçe</label>
                    <select name="adres[${addrCount}][ilce]" class="form-select ilce-trigger chosen-select">
                        ${ilceOptions}
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold">Mahalle</label>
                    <select name="adres[${addrCount}][mahalle]" class="form-select mahalle-target mahalle-trigger chosen-select" disabled>
                        <option value="">İlçe Seçin...</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold">Sokak/Cadde</label>
                    <select name="adres[${addrCount}][sokak]" class="form-select sokak-target sokak-trigger chosen-select" disabled>
                        <option value="">Mahalle Seçin...</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold">Kapı No</label>
                    <select name="adres[${addrCount}][kapino]" class="form-select kapino-target chosen-select" disabled>
                        <option value="">Sokak Seçin...</option>
                    </select>
                </div>
            </div>
        </div>`;

        const $newRow = $(html);
        $('#address-container').append($newRow);
        initPlugins($newRow); // Yeni satır için Chosen'ı aktifleştir
        addrCount++;
    });

    // --- İLÇE/MAHALLE/SOKAK ZİNCİRLEME AJAX ---
    $(document).on('change', '.ilce-trigger, .mahalle-trigger, .sokak-trigger', function() {
        const $this = $(this);
        const parentId = $this.val();
        const $row = $this.closest('.row');
        
        // Hangi seviyede olduğumuzu belirleyelim
        let type = '';
        let $target = null;
        let placeholder = '';

        if ($this.hasClass('ilce-trigger')) {
            type = 'mahalle';
            $target = $row.find('.mahalle-target');
            placeholder = 'Mahalle';
            // Altındakileri sıfırla
            $row.find('.sokak-target, .kapino-target').html('<option value="">Bekliyor...</option>').prop('disabled', true).trigger("chosen:updated");
        } else if ($this.hasClass('mahalle-trigger')) {
            type = 'sokak';
            $target = $row.find('.sokak-target');
            placeholder = 'Sokak';
            $row.find('.kapino-target').html('<option value="">Bekliyor...</option>').prop('disabled', true).trigger("chosen:updated");
        } else if ($this.hasClass('sokak-trigger')) {
            type = 'kapino';
            $target = $row.find('.kapino-target');
            placeholder = 'Kapı No';
        }

        if (parentId && $target) {
            $target.html('<option>Yükleniyor...</option>').trigger("chosen:updated");
            $.getJSON(`index.php?controller=Address&action=getSubAddresses`, { parent_id: parentId, type: type }, function(data) {
                let opts = `<option value="">${placeholder} Seçiniz</option>`;
                $.each(data, function(i, item) {
                    opts += `<option value="${item.id}">${item.adi}</option>`;
                });
                $target.html(opts).prop('disabled', false).trigger("chosen:updated");
            });
        }
    });

    // Satır silme
    $(document).on('click', '.remove-addr', function() {
        $(this).closest('.address-row').remove();
    });
    
    // --- TOMTOM KOORDİNAT FONKSİYONU ---
    function fetchCoordinates() {
    let ilce = $("#ilce option:selected").text();
    let mahalle = $("#mahalle option:selected").text();
    let sokak = $("#sokak option:selected").text();
    let kapino = $("#kapino option:selected").text();

    if (ilce && mahalle && sokak) {
        let adresMetni = `${mahalle} Mah. ${sokak} Sok. No:${kapino}, ${ilce}, Denizli, Türkiye`;
        const apiKey = '<?= TOMTOM_KEY; ?>'; 
        
        console.log("TomTom isteği gönderiliyor: ", adresMetni);

        $.ajax({
            url: `https://api.tomtom.com/search/2/geocode/${encodeURIComponent(adresMetni)}.json`,
            data: {
                key: apiKey,
                limit: 1,
                countrySet: 'TR'
            },
            method: 'GET',
            success: function(res) {
                console.log("TomTom Cevabı: ", res);
                if (res.results && res.results.length > 0) {
                    let pos = res.results[0].position;
                    $('#coords').val(pos.lat + ", " + pos.lon);
                    toastr.success('Konum bulundu!');
                } else {
                    toastr.warning('Adres bulundu ancak koordinat eşleşmedi.');
                }
            },
            error: function(xhr, status, error) {
                console.error("TomTom Hata Detayı: ", xhr.responseText);
                toastr.error('API Hatası: ' + xhr.status);
            }
        });
    }
}

    // --- TETİKLEYİCİLER (TRIGGER) ---

    // 1. Sokak seçildiğinde (Henüz kapı no seçilmeden kaba konum için)
    $(document).on('change', '#sokak', function() {
        fetchCoordinates();
    });

    // 2. Kapı No seçildiğinde (Nokta atışı konum için)
    $(document).on('change', '#kapino', function() {
        fetchCoordinates();
    });

    // Not: Eğer adreslerin dinamik ekleniyorsa (class kullanıyorsan) seçicileri şöyle değiştir:
    // $(document).on('change', '.sokak-trigger, .kapino-target', function() { fetchCoordinates(); });
});
</script>