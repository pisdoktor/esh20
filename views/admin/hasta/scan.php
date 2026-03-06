<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa-solid fa-microchip me-2"></i>MERNİS Toplu Vefat Taraması</h5>
            <button class="btn btn-primary btn-sm" id="startBtn" onclick="startScan()">
                <i class="fa fa-play me-1"></i> Taramayı Başlat
            </button>
        </div>
        <div class="card-body">
            <div class="alert alert-info py-2 small">
                <strong>Bilgi:</strong> Hastalar TC Kimlik sırasına göre 20'şerli paketler halinde Denizli B.B. servisinden taranır. Vefat edenler otomatik ölenler listesine alınır.
            </div>
            
            <div id="progressArea" class="mb-3 d-none">
                <div class="d-flex justify-content-between mb-1 small fw-bold">
                    <span>İşlem Durumu: <span id="statusText">Hazır</span></span>
                    <span id="percentText">%0</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
            </div>

            <div class="table-responsive" style="max-height: 450px;">
                <table class="table table-sm table-hover border">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th>TC KİMLİK</th>
                            <th>AD SOYAD</th>
                            <th>ANNE ADI</th>
                            <th>BABA ADI</th>
                            <th>DURUM</th>
                        </tr>
                    </thead>
                    <tbody id="logBody">
                        <tr><td colspan="3" class="text-center text-muted italic">Henüz tarama başlatılmadı...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
let currentOffset = 0;
let totalFound = 0;
let totalRecords = 0; 

function startScan() {
    if(!confirm("Tüm aktif hastalar taranacak. Emin misiniz?")) return;
    
    currentOffset = 0;
    totalFound = 0;
    totalRecords = <?= $totalCount;?>;
    
    $("#logBody").empty();
    $("#progressArea").removeClass("d-none");
    $("#startBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Tarama Sürüyor...');
    
    runBatch();
}

function runBatch() {
    $.getJSON('index.php?controller=Patient&action=bulkDiedScan&offset=' + currentOffset, function(data) {
        
        if (data.processed > 0) {
        
            data.results.forEach(function(item) {
                let rowClass = item.oldu == 1 ? 'table-danger' : '';
                let statusIcon = item.oldu == 1 ? '<i class="fa-solid fa-skull me-2 small"></i>VEFAT ('+item.tarih+')' : '<i class="fa fa-check text-success me-2"></i>SAĞ';
                
                if(item.oldu == 1) {
                    totalFound++;
                    toastr.error(item.ad + " vefat etmiş!", "Sistem Tespiti", {timeOut: 2000});
                }

                $("#logBody").prepend(
                    `<tr class="${rowClass}">
                        <td>${item.tc}</td>
                        <td>${item.ad}</td>
                        <td>${item.anneAdi}</td>
                        <td>${item.babaAdi}</td>
                        <td>${statusIcon}</td>
                    </tr>`
                );
            });

            currentOffset = data.nextOffset;
            
            let displayProcessed = Math.min(currentOffset, totalRecords);
            $("#statusText").text(displayProcessed + " hasta kontrol edildi...");
            
            let percent = totalRecords > 0 ? Math.round((displayProcessed / totalRecords) * 100) : 0;
            $("#progressBar").css("width", percent + "%");
            $("#percentText").text("%" + percent);

            // Belediyeyi yormamak için 800ms bekle ve sonraki pakete geç
            setTimeout(runBatch, 800);
        } else {
            finishScan();
        }
    }).fail(function() {
        toastr.warning("Bir hata oluştu, 3 saniye sonra tekrar denenecek...");
        setTimeout(runBatch, 3000);
    });
}

function finishScan() {
    $("#startBtn").prop("disabled", false).html('<i class="fa fa-refresh me-1"></i> Yeniden Başlat');
    $("#statusText").html('<span class="text-success fw-bold">BİTTİ!</span> Toplam ' + totalFound + ' vefat tespit edildi.');
    toastr.success("Tarama başarıyla tamamlandı.");
}
</script>