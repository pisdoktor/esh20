<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fa fa-calendar-alt me-2"></i><?= $pageTitle;?>
                    </h5>
                    <div class="btn-group shadow-sm">
                        <a href="index.php?controller=Dashboard&year=<?= $prevYear ?>&month=<?= $prevMonth ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-chevron-left"></i>
                        </a>
                        <span class="btn btn-sm btn-light fw-bold px-3">
                            <?= $currentMonthName . " " . $year ?>
                        </span>
                        <a href="index.php?controller=Dashboard&year=<?= $nextYear ?>&month=<?= $nextMonth ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <?= $calendarHtml ?>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 py-2">
                    <div class="d-flex flex-wrap gap-3 small">
                        <span><span class="badge bg-primary">İ</span> Planlı İzlem</span>
                        <span><span class="badge bg-warning">P</span> Planlı Pansuman</span>
                        <span><span class="badge bg-danger">+</span> Yeni Kayıt</span>
                        <span><span class="badge bg-info">N</span> Hastane Nakil</span>
                        <span><span class="badge bg-success">Y</span> Toplam Yapılan</span>
                    </div>
                </div>
            </div>
            
            </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100" style="min-height: 600px;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa fa-list-check me-2 text-warning"></i>Günün Planı</h5>
                    <small class="text-muted fw-bold" id="selected-date-label"><?= date('d.m.Y') ?></small>
                </div>
                <div class="card-body p-0" id="daily-events-container">
                    </div>
                <div class="card-footer bg-white border-top p-3 d-none" id="route-button-container">
                    <a href="#" id="view-route-btn" class="btn btn-primary w-100 fw-bold shadow-sm">
                        <i class="fa fa-map-marked-alt me-2"></i>BU GÜNÜN ROTASINI GÖR
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        getDailyTasks('<?= date("Y-m-d") ?>');
    });

    /**
     * Mevcut fonksiyonun içine sadece buton güncelleme satırlarını ekledim
     */
    function getDailyTasks(date) {
        var displayDate = date.split('-').reverse().join('.');
        $('#selected-date-label').text(displayDate);
        
        // --- BUTON GÜNCELLEME ---
        $('#view-route-btn').attr('href', 'index.php?controller=Dashboard&action=showRoute&date=' + date);
        $('#route-button-container').removeClass('d-none');
        // ------------------------

        $('#daily-events-container').html('<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>');

        $.getJSON('index.php?controller=Dashboard&action=getDailyEvents&date=' + date, function(data) {
            let sections = [
                { key: 'sabah', label: 'SABAH', icon: 'fa-sun', color: 'text-warning' },
                { key: 'ogle', label: 'ÖĞLE', icon: 'fa-cloud-sun', color: 'text-info' },
                { key: 'aksam', label: 'AKŞAM', icon: 'fa-moon', color: 'text-dark' }
            ];

            let navHtml = '<ul class="nav nav-tabs nav-fill mb-3" id="taskTab" role="tablist">';
            let contentHtml = '<div class="tab-content" id="taskTabContent">';
            let hasAnyData = false;

            sections.forEach((sec, index) => {
                let isActive = index === 0 ? 'active' : '';
                let rawData = data[sec.key] || {};
                
                let planliList = rawData.planli || [];
                let pansumanList = rawData.pansuman || []; 
                let ilkZiyaretList = rawData.ilkziyaret || [];
                let totalCount = pansumanList.length + planliList.length + ilkZiyaretList.length;

                if (totalCount > 0) hasAnyData = true;

                navHtml += `
                    <li class="nav-item" role="presentation">
                        <button class="nav-link ${isActive} py-3" id="${sec.key}-tab" data-bs-toggle="tab" data-bs-target="#tab-${sec.key}" type="button" role="tab">
                            <i class="fa ${sec.icon} ${sec.color} d-block mb-1"></i>
                            <span class="small fw-bold">${sec.label}</span>
                            <span class="badge rounded-pill bg-secondary ms-1">${totalCount}</span>
                        </button>
                    </li>`;

                contentHtml += `<div class="tab-pane fade show ${isActive}" id="tab-${sec.key}" role="tabpanel">`;
                
                if (totalCount > 0) {
                    if (ilkZiyaretList.length > 0) {
                        contentHtml += `<div class="alert alert-info py-2 px-3 mb-2 fw-bold small border-0 shadow-sm d-flex align-items-center">
                                            <i class="fa fa-user-plus me-2"></i> İLK ZİYARETLER (YENİ KAYIT)
                                        </div>`;
                        ilkZiyaretList.forEach(item => {
                            contentHtml += generateIlkKayitRow(item, 'border-info', 'bg-info-subtle');
                        });
                    }

                    if (planliList.length > 0) {
                        contentHtml += `<div class="text-muted small fw-bold mb-2 mt-4 px-2 text-uppercase">Planlı İzlemler</div>`;
                        planliList.forEach(item => {
                            contentHtml += generateTaskRow(item, 'border-primary', '');
                        });
                    }
                    
                    if (pansumanList.length > 0) {
                        contentHtml += `<div class="text-muted small fw-bold mb-2 mt-4 px-2 text-uppercase">Planlı Pansumanlar</div>`;
                        pansumanList.forEach(item => {
                            contentHtml += generatePansumanRow(item, 'border-warning', '');
                        });
                    }
                } else {
                    contentHtml += '<div class="p-5 text-center text-muted small border rounded-3 bg-light">Bu vaktin planlı görevi bulunmuyor.</div>';
                }
                contentHtml += '</div>';
            });

            navHtml += '</ul>';
            contentHtml += '</div>';

            let nakilHtml = '';
            let nakilList = data.nakiller || [];
            
            if (nakilList.length > 0) {
                hasAnyData = true;
                nakilHtml = `
                    <div class="mt-4 border-top pt-3 shadow-none">
                        <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                            <h6 class="mb-0 text-danger fw-bold"><i class="fa fa-ambulance me-2"></i> NAKİL GÖREVLERİ</h6>
                            <span class="badge bg-danger rounded-pill">${nakilList.length}</span>
                        </div>
                        <div class="nakil-list px-1">`;
                nakilList.forEach(item => {
                    nakilHtml += generateTaskRow(item, 'border-danger', 'bg-danger-subtle');
                });
                nakilHtml += `</div></div>`;
            }

            if (!hasAnyData) {
                $('#daily-events-container').html('<div class="p-5 text-center text-muted">Güne ait kayıt bulunamadı.</div>');
                $('#route-button-container').addClass('d-none'); // Veri yoksa butonu gizle
            } else {
                $('#daily-events-container').html(navHtml + contentHtml + nakilHtml);
            }
        });
    }

    // Yardımcı render fonksiyonların (generateTaskRow vb.) dokunulmadan aşağıda devam ediyor...
    function generateTaskRow(item, borderColor, bgColor) {
        let patientUrl = `index.php?controller=Patient&action=view&id=${item.hastaid}`;
        let visitUrl = `index.php?controller=PlannedVisit&action=add&id=${item.id}`;
        return `
        <div class="list-group-item p-3 border rounded mb-2 shadow-sm border-start-lg ${borderColor} ${bgColor}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="fw-bold text-uppercase">
                    <a href="${visitUrl}" class="text-primary text-decoration-none">
                        <i class="fa fa-user-circle me-1 text-secondary"></i> ${item.isim} ${item.soyisim}
                    </a>
                </div>
                <span class="badge bg-white text-dark border shadow-sm">${item.islem_label || 'İşlem Yok'}</span>
            </div>
            <div class="mt-2 small text-muted d-flex align-items-center flex-wrap">
                <span class="me-3">
                    <i class="fa fa-id-card me-1"></i>
                    <a href="${patientUrl}" class="text-primary text-decoration-none">
                    ${item.tckimlik || 'TC Yok'}
                    </a>
                </span>
                <span class="me-3">
                    <i class="fa fa-map-marker-alt me-1 text-danger"></i> ${item.ilce || '-'} / ${item.mahalle || '-'}
                </span>
            </div>
        </div>`;
    }

    function generatePansumanRow(item, borderColor, bgColor) {
        let patientUrl = `index.php?controller=Patient&action=view&id=${item.hastaid}`;
        let visitUrl = `index.php?controller=PlannedVisit&action=add&tc=${item.tckimlik}`;
        return `
        <div class="list-group-item p-3 border rounded mb-2 shadow-sm border-start-lg ${borderColor} ${bgColor}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="fw-bold text-uppercase">
                    <a href="${visitUrl}" class="text-primary text-decoration-none">
                        <i class="fa fa-user-circle me-1 text-secondary"></i> ${item.isim} ${item.soyisim}
                    </a>
                </div>
                <span class="badge bg-white text-dark border shadow-sm">${item.islem_label || 'İşlem Yok'}</span>
            </div>
            <div class="mt-2 small text-muted d-flex align-items-center flex-wrap">
                <span class="me-3">
                    <i class="fa fa-id-card me-1"></i>
                    <a href="${patientUrl}" class="text-primary text-decoration-none">
                    ${item.tckimlik || 'TC Yok'}
                    </a>
                </span>
                <span class="me-3">
                    <i class="fa fa-map-marker-alt me-1 text-danger"></i> ${item.ilce || '-'} / ${item.mahalle || '-'}
                </span>
            </div>
        </div>`;
    }

    function generateIlkKayitRow(item, borderColor, bgColor) {
        let patientUrl = `index.php?controller=Patient&action=firstSave&id=${item.hastaid}`;
        return `
        <div class="list-group-item p-3 border rounded mb-2 shadow-sm border-start-lg ${borderColor} ${bgColor}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="fw-bold text-uppercase">
                    <a href="${patientUrl}" class="text-primary text-decoration-none">
                        <i class="fa fa-user-circle me-1 text-secondary"></i> ${item.isim} ${item.soyisim}
                    </a>
                </div>
                <span class="badge bg-white text-dark border shadow-sm">${item.islem_label || 'İşlem Yok'}</span>
            </div>
            <div class="mt-2 small text-muted d-flex align-items-center flex-wrap">
                <span class="me-3">
                    <i class="fa fa-id-card me-1"></i> ${item.tckimlik || 'TC Yok'}
                </span>
                <span class="me-3">
                    <i class="fa fa-map-marker-alt me-1 text-danger"></i> ${item.ilce || '-'} / ${item.mahalle || '-'}
                </span>
            </div>
        </div>`;
    }
</script>

<style>
    .calendar-day:hover { background-color: #f8f9fa !important; cursor: pointer; }
    .badge { font-size: 0.7rem !important; }
    .border-start-lg { border-left-width: 5px !important; }
</style>