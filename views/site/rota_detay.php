<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fa fa-map-marked-alt me-2 text-info"></i>Ekip Rotaları</h5>
            <input type="date" id="routeDateSelector" class="form-control form-control-sm w-auto" value="<?= $date ?>">
        </div>
        <div class="card-body p-0">
            <div class="row g-0" style="min-height: calc(100vh - 200px);">
                <div class="col-md-8 border-end position-relative">
                    <div id="map" style="width: 100%; height: 100%;"></div>
                </div>
                <div class="col-md-4 bg-light" id="teamListContainer" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                    </div>
            </div>
        </div>
    </div>
</div>

<script>
    var map;
    var routeLayers = { sabah: [], ogle: [], aksam: [] };
    var routeMarkers = { sabah: [], ogle: [], aksam: [] };

    document.addEventListener("DOMContentLoaded", function() {
        // Haritayı başlat
        map = tt.map({
            key: '<?= TOMTOM_KEY; ?>',
            container: 'map',
            center: [29.0875, 37.7744], 
            zoom: 12
        });

        // Sayfa açıldığında rotayı çiz
        drawRoute('<?= $date ?>');

        // Tarih değiştiğinde rotayı güncelle
        $('#routeDateSelector').on('change', function() {
            drawRoute($(this).val());
        });
    });

    // --- Önceki adımda hazırladığımız drawRoute ve yardımcı fonksiyonlar buraya gelecek ---
    function drawRoute(date) {
        clearMap();
        $("#teamListContainer").html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');

        $.getJSON('index.php?controller=Dashboard&action=getRoute&date=' + date, function(response) {
            var container = $("#teamListContainer").empty();
            if (!response.success || response.data.length === 0) {
                container.html('<div class="alert alert-warning m-3">Bu tarih için rota bulunamadı.</div>');
                return;
            }

            // Tab yapısını kur (Daha önce verdiğim Sabah/Öğle/Akşam kodları)
            container.append(`
                <ul class="nav nav-pills nav-justified mb-2 p-2 bg-white sticky-top shadow-sm" id="routeTabs">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-sabah">Sabah</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ogle">Öğle</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-aksam">Akşam</button></li>
                </ul>
                <div class="tab-content" id="routeTabsContent">
                    <div class="tab-pane fade show active" id="tab-sabah"></div>
                    <div class="tab-pane fade" id="tab-ogle"></div>
                    <div class="tab-pane fade" id="tab-aksam"></div>
                </div>
            `);

            // Her bir ekip için döngü
            response.data.forEach(function(team, index) {
                // Ekibin hangi tab'a gireceğini belirle (Sabah/Öğle/Akşam)
                var targetTab = "#tab-sabah";
                var timeKey = "sabah";
                if (team.label.includes("Öğle")) { targetTab = "#tab-ogle"; timeKey = "ogle"; }
                if (team.label.includes("Akşam")) { targetTab = "#tab-aksam"; timeKey = "aksam"; }

                // 1. Sağ Liste Kartını Oluştur
                var teamHtml = `
                    <div class="card m-2 border-0 shadow-sm">
                        <div class="card-header bg-white fw-bold" style="border-left: 4px solid ${team.color}">
                            ${team.label}
                        </div>
                        <ul class="list-group list-group-flush" id="list-${team.key}"></ul>
                    </div>
                `;
                $(targetTab).append(teamHtml);

                var routePoints = [];

                // 2. Noktaları İşle
                team.points.forEach(function(point, pIdx) {
                    var lngLat = [point.lng, point.lat];
                    routePoints.push(lngLat);

                    // Haritaya Marker Ekle
                    var markerElement = document.createElement('div');
                    markerElement.className = 'custom-marker';
                    markerElement.innerHTML = point.is_center ? 
                        '<i class="fa fa-hospital text-danger fs-4"></i>' : 
                        `<span class="badge rounded-circle p-2" style="background:${team.color}">${pIdx}</span>`;

                    var marker = new tt.Marker({ element: markerElement })
                        .setLngLat(lngLat)
                        .setPopup(new tt.Popup({ offset: 30 }).setHTML(`<b>${point.name}</b><br>Varış: ${point.varis_saati || 'Merkez'}`))
                        .addTo(map);
                    
                    routeMarkers[timeKey].push(marker);

                    // Listeye Ekle
                    $(`#list-${team.key}`).append(`
                        <button class="list-group-item list-group-item-action border-0 py-2 small" onclick="focusPoint(${point.lat}, ${point.lng})">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>${pIdx}. ${point.name}</span>
                                <span class="badge bg-light text-dark">${point.varis_saati || '--:--'}</span>
                            </div>
                        </button>
                    `);
                });

                // 3. Haritada Çizgiyi Oluştur
                var layerId = 'route-' + team.key;
                routeLayers[timeKey].push(layerId);

                map.addLayer({
                    'id': layerId,
                    'type': 'line',
                    'source': {
                        'type': 'geojson',
                        'data': {
                            'type': 'Feature',
                            'properties': {},
                            'geometry': {
                                'type': 'LineString',
                                'coordinates': routePoints
                            }
                        }
                    },
                    'layout': { 'line-join': 'round', 'line-cap': 'round' },
                    'paint': { 'line-color': team.color, 'line-width': 4, 'line-opacity': 0.8 }
                });
            });
        });
    }

    function clearMap() {
        Object.keys(routeMarkers).forEach(z => routeMarkers[z].forEach(m => m.remove()));
        Object.keys(routeLayers).forEach(z => routeLayers[z].forEach(l => {
            if(map.getLayer(l)) map.removeLayer(l);
            if(map.getSource(l)) map.removeSource(l);
        }));
        routeLayers = { sabah: [], ogle: [], aksam: [] };
        routeMarkers = { sabah: [], ogle: [], aksam: [] };
    }
    
    function focusPoint(lat, lng) {
        map.flyTo({
            center: [lng, lat],
            zoom: 15,
            duration: 1000
        });
    }
</script>