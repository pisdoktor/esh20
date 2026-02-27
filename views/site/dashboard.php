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
                        <span><span class="badge bg-primary">P</span> Planlı</span>
                        <span><span class="badge bg-info">N</span> Nakil</span>
                        <span><span class="badge bg-success">Y</span> Yapılan</span>
                        <span><span class="badge bg-danger">+</span> Yeni Kayıt</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-success">
                        <i class="fa fa-map-marked-alt me-2"></i>Günlük Rota İzleme
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 500px; width: 100%;"></div>
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
            </div>
        </div>
    </div>
</div>

<script>
    var ttApiKey = '<?php echo TOMTOM_KEY; ?>';
    var map, markers = [], layers = [];

    $(document).ready(function() {
        map = tt.map({
            key: ttApiKey,
            container: 'map',
            center: [29.0864, 37.7765],
            zoom: 11
        });

        getDailyTasks('<?= date("Y-m-d") ?>');
    });

    /**
     * Gruplanmış verileri işleyen AJAX fonksiyonu
     */
function getDailyTasks(date) {
    var displayDate = date.split('-').reverse().join('.');
    $('#selected-date-label').text(displayDate);
    
    $('#daily-events-container').html('<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>');

    $.getJSON('index.php?controller=Dashboard&action=getDailyEvents&date=' + date, function(data) {
        // Navigasyonda sadece zaman dilimleri kalıyor (Nakil buraya dahil değil)
        let sections = [
            { key: 'sabah', label: 'SABAH', icon: 'fa-sun', color: 'text-warning' },
            { key: 'ogle', label: 'ÖĞLE', icon: 'fa-cloud-sun', color: 'text-info' },
            { key: 'aksam', label: 'AKŞAM', icon: 'fa-moon', color: 'text-dark' }
        ];

        let navHtml = '<ul class="nav nav-tabs nav-fill mb-3" id="taskTab" role="tablist">';
        let contentHtml = '<div class="tab-content" id="taskTabContent">';
        let hasAnyData = false;

        // 1. ZAMAN DİLİMLERİ DÖNGÜSÜ (TABLAR)
        sections.forEach((sec, index) => {
            let isActive = index === 0 ? 'active' : '';
            let rawData = data[sec.key] || {};
            
            let planliList = rawData.planli || [];
            let ilkZiyaretList = rawData.ilkziyaret || [];
            let totalCount = planliList.length + ilkZiyaretList.length;

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
                // İlk Ziyaretler
                if (ilkZiyaretList.length > 0) {
                    contentHtml += `<div class="alert alert-info py-2 px-3 mb-2 fw-bold small border-0 shadow-sm d-flex align-items-center">
                                        <i class="fa fa-user-plus me-2"></i> İLK ZİYARETLER (YENİ KAYIT)
                                    </div>`;
                    ilkZiyaretList.forEach(item => {
                        contentHtml += generateIlkKayitRow(item, 'border-info', 'bg-info-subtle');
                    });
                }

                // Planlı İzlemler
                if (planliList.length > 0) {
                    if (ilkZiyaretList.length > 0) {
                        contentHtml += `<div class="text-muted small fw-bold mb-2 mt-4 px-2 text-uppercase">Rutin Planlı İzlemler</div>`;
                    }
                    planliList.forEach(item => {
                        contentHtml += generateTaskRow(item, 'border-primary', '');
                    });
                }
            } else {
                contentHtml += '<div class="p-5 text-center text-muted small border rounded-3 bg-light">Bu vaktin planlı görevi bulunmuyor.</div>';
            }
            contentHtml += '</div>';
        });

        navHtml += '</ul>';
        contentHtml += '</div>';

        // 2. NAKİL BÖLÜMÜ (TABLARDAN BAĞIMSIZ - EN ALTA)
        let nakilHtml = '';
        let nakilList = data.nakiller || []; // Nakil doğrudan dizi geliyor
        
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
                // Nakiller genelde planlı izlem statüsündedir (Visit/Add)
                nakilHtml += generateTaskRow(item, 'border-danger', 'bg-danger-subtle');
            });
            
            nakilHtml += `</div></div>`;
        }

        // DOM'a Basma
        if (!hasAnyData) {
            $('#daily-events-container').html('<div class="p-5 text-center text-muted">Güne ait kayıt bulunamadı.</div>');
        } else {
            $('#daily-events-container').html(navHtml + contentHtml + nakilHtml);
        }

        if(typeof drawRoute === "function") drawRoute(date);
    });
}

// Yardımcı Fonksiyon: Kart Satırı Oluşturma
function generateTaskRow(item, borderColor, bgColor) {
    let patientUrl = `index.php?controller=Patient&action=view&id=${item.hastaid}`;
    let visitUrl = `index.php?controller=Visit&action=add&id=${item.id}`;
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

function drawRoute(date) {
    clearMap();

    // Akıllı rota algoritmasından gelen veriyi çekiyoruz
    $.getJSON('index.php?controller=Dashboard&action=getRoute&date=' + date, function(response) {
        if (response.success && response.data) {
            var bounds = new tt.LngLatBounds();
            var hasValidPoints = false;

            // PHP'den gelen her vardiyayı (Sabah, Öğle, Akşam) dön
            $.each(response.data, function(vKey, vardiyaVerisi) {
                
                // Her vardiyanın içindeki ekipleri dön
                $.each(vardiyaVerisi, function(eKey, ekip) {
                    if (!ekip.hastalar || ekip.hastalar.length === 0) return;

                    var groupColor = ekip.color || '#007bff';
                    var locations = [];

                    // 1. Merkez noktasını başlangıç olarak ekle (PHP'den gelen merkez bilgisi)
                    if (ekip.merkez) {
                        var mPos = [parseFloat(ekip.merkez.lng), parseFloat(ekip.merkez.lat)];
                        locations.push(mPos);
                        bounds.extend(mPos);
                        
                        addMarker(mPos, 'center', ekip.merkez.name, groupColor);
                    }

                    // 2. Ekibin hastalarını sıralı olarak ekle ve markerları koy
                    $.each(ekip.hastalar, function(i, h) {
                        var coords = h.coords.replace(/\s/g, '').split(',');
                        var lat = parseFloat(coords[0]);
                        var lng = parseFloat(coords[1]);

                        if (isNaN(lat) || isNaN(lng)) return;

                        var pos = [lng, lat];
                        locations.push(pos);
                        bounds.extend(pos);
                        hasValidPoints = true;

                        // Hasta Marker'ı (Sıra numarası ve varış saati ile)
                        addMarker(pos, 'patient', `${i + 1}. ${h.isim} ${h.soyisim} <br> <small>Varış: ${h.varis_saati}</small>`, groupColor, i + 1);
                    });

                    // 3. TomTom Routing API ile Gerçek Yol Çiz (Sadece o ekibe özel hat)
                    if (locations.length > 1) {
                        tt.services.calculateRoute({
                            key: '<?= TOMTOM_KEY;?>',
                            locations: locations,
                            travelMode: 'car'
                        }).then(function(routeResponse) {
                            var geojson = routeResponse.toGeoJson();
                            var layerId = 'route-' + vKey + '-' + eKey;

                            map.addLayer({
                                'id': layerId,
                                'type': 'line',
                                'source': { 'type': 'geojson', 'data': geojson },
                                'paint': {
                                    'line-color': groupColor,
                                    'line-width': 4,
                                    'line-opacity': 0.7
                                }
                            });
                            layers.push(layerId);
                        });
                    }
                });
            });

            if (hasValidPoints) {
                map.fitBounds(bounds, { padding: 50 });
            }
        }
    });
}

// Marker eklemek için yardımcı fonksiyon (Kodu temiz tutar)
function addMarker(pos, type, content, color, index) {
    var el = document.createElement('div');
    el.className = 'marker-custom';
    
    if (type === 'center') {
        el.innerHTML = `<div style="background-color:#ef4444; width:30px; height:30px; border-radius:5px; border:2px solid white; color:white; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 4px rgba(0,0,0,0.3);"><i class="fa fa-building"></i></div>`;
    } else {
        el.innerHTML = `<div style="background-color:${color}; width:24px; height:24px; border-radius:50%; border:2px solid white; color:white; font-size:11px; font-weight:bold; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 4px rgba(0,0,0,0.3);">${index}</div>`;
    }

    var m = new tt.Marker({ element: el })
        .setLngLat(pos)
        .setPopup(new tt.Popup({ offset: 30 }).setHTML(content))
        .addTo(map);
    markers.push(m);
}

    function clearMap() {
        markers.forEach(m => m.remove());
        layers.forEach(l => { if(map.getLayer(l)) map.removeLayer(l); if(map.getSource(l)) map.removeSource(l); });
        markers = []; layers = [];
    }
</script>

<style>
    .marker-custom { width: 22px; height: 22px; border-radius: 50%; border: 2px solid white; color: white; text-align: center; font-size: 11px; font-weight: bold; line-height: 18px; box-shadow: 0 2px 4px rgba(0,0,0,0.3); }
    .calendar-day:hover { background-color: #f8f9fa !important; cursor: pointer; }
    .badge { font-size: 0.7rem !important; }
</style>