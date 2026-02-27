<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-6"><h4><i class="fa-solid fa-arrows-down-to-people"></i> Ekip Yönetimi</h4></div>
            <div class="col-xs-6 text-right"> 
                <div class="btn-group">
                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-file-pdf"></i> PDF ÇIKTI <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right shadow">
                        <li><a href="javascript:void(0)" onclick='preparePDF("gunluk")'>Günlük Plan</a></li>
                        <li><a href="javascript:void(0)" onclick='preparePDF("haftalik")'>Haftalık Plan</a></li>
                        <li><a href="javascript:void(0)" onclick='preparePDF("aylik")'>Aylık Plan</a></li>
                    </ul>
                </div>
                
                <a href="index.php?controller=Ekip&action=edit&tarih=<?= date('Y-m-d') ?>" class="btn btn-success btn-sm">
                    <i class="fa fa-plus"></i> YENİ PLAN
                </a>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th>Tarih</th>
                    <th>Ekip Sayısı</th>
                    <th>Saatler</th>
                    <th>Personel Özeti</th>
                    <th class="text-center">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(!empty($items)) {
                    foreach($items as $row) {
                        // Personel özetini model üzerinden çekiyoruz (Senin mantığınla)
                        $ekipModel = new \App\Models\Ekip();
                        $ekipModel->user_ids = $row->all_user_ids; // Modelde GROUP_CONCAT ile çekilmeli
                        $p_ozet = $ekipModel->getTeamMemberNames();
                        if(strlen($p_ozet) > 60) $p_ozet = mb_substr($p_ozet, 0, 60) . '...';
                        
                        $tarih_tr = date('d.m.Y', strtotime($row->tarih));
                ?>
                    <tr>
                        <td><strong><?php echo $tarih_tr; ?></strong></td>
                        <td><span class="label label-info"><?php echo $row->ekip_sayisi; ?> Ekip</span></td>
                        <td><small><?php echo $row->saatler; ?></small></td>
                        <td><small class="text-muted"><?php echo $p_ozet; ?></small></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="index.php?controller=Ekip&action=edit&tarih=<?php echo $row->tarih; ?>" class="btn btn-primary btn-xs" title="Düzenle"><i class="fa fa-edit"></i></a>
                                <a href="javascript:if(confirm('Bu tarihteki TÜM ekipler silinsin mi?')) window.location='index.php?controller=Ekip&action=deleteDay&tarih=<?php echo $row->tarih; ?>';" class="btn btn-danger btn-xs" title="Sil"><i class="fa fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center p-4 text-muted'>Kayıt bulunamadı.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>

<script type="text/javascript">
/**
 * Senin gönderdiğin PDF motoru
 */
function generatePDF(data, title) {
    if(!data || data.length === 0) { alert("Yazdırılacak veri bulunamadı!"); return; }
    
    var body = [[
        { text: 'TARİH', style: 'tableHeader' },
        { text: 'VARDİYA', style: 'tableHeader' },
        { text: 'SAAT', style: 'tableHeader' },
        { text: 'EKİP', style: 'tableHeader' },
        { text: 'PERSONEL LİSTESİ', style: 'tableHeader' }
    ]];

    let sonTarih = ""; 

    data.forEach(function(row) {
        if (sonTarih !== "" && sonTarih !== row.tarih) {
            body.push([
                { text: ' ', colSpan: 5, fillColor: '#34495e', margin: [0, 2, 0, 2] }, 
                {}, {}, {}, {}
            ]);
        }

        body.push([
            { text: row.tarih, alignment: 'center' },
            { text: row.vardiya_label, alignment: 'center' },
            { text: row.saat, alignment: 'center' },
            { text: row.ekip, alignment: 'center' },
            { text: row.personeller }
        ]);
        sonTarih = row.tarih;
    });

    var docDefinition = {
        pageSize: 'A4',
        pageOrientation: 'landscape',
        pageMargins: [30, 60, 30, 40],
        header: function(currentPage, pageCount) {
            return {
                text: title.toUpperCase(),
                style: 'pageHeaderStyle',
                margin: [30, 20, 0, 0]
            };
        },
        content: [
            { text: 'OPERASYONEL EKİP PERSONEL PLANI', style: 'mainTitle' },
            {
                table: {
                    headerRows: 1,
                    widths: [75, 75, 60, 75, '*'],
                    body: body
                },
                layout: {
                    hLineWidth: function (i, node) { return (i === 0 || i === node.table.body.length) ? 2 : 1; },
                    vLineWidth: function (i) { return 1; },
                    hLineColor: function (i) { return '#aaa'; },
                    vLineColor: function (i) { return '#aaa'; }
                }
            }
        ],
        styles: {
            pageHeaderStyle: { fontSize: 9, bold: true, color: '#7f8c8d' },
            mainTitle: { fontSize: 18, bold: true, alignment: 'center', margin: [0, 0, 0, 20] },
            tableHeader: { bold: true, fontSize: 11, color: 'white', fillColor: '#2c3e50', alignment: 'center', margin: [0, 5, 0, 5] }
        },
        defaultStyle: { fontSize: 10 }
    };

    pdfMake.createPdf(docDefinition).download(title + '.pdf');
}

/**
 * PDF verisini AJAX ile Controller'dan çeker (Senin getEkiplerJSON mantığın)
 */
function preparePDF(mod) {
    let activeDate = "<?= $tarih ?>"; 
    // AJAX ile veri çekme (MVC yapısına uygun)
    $.getJSON('index.php', {
        controller: 'Ekip',
        action: 'getEkiplerJSON',
        mod: mod,
        date: activeDate
    }, function(response) {
        if(response && response.length > 0) {
            generatePDF(response, mod.toUpperCase() + "_Ekip_Plani_" + activeDate);
        } else {
            alert("Bu aralık için veri bulunamadı.");
        }
    });
}
</script>