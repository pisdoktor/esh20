<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-calendar-week me-2 text-primary"></i>Haftalık Operasyonel Planlama Çizelgesi</h5>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-sm btn-outline-dark"><i class="fa-solid fa-print me-1"></i> Yazdır</button>
                <a href="index.php?controller=Planning&action=index" class="btn btn-sm btn-primary">Planlama Listesi</a>
            </div>
        </div>
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 matris-table">
                    <thead>
                        <tr class="bg-light text-center">
                            <?php foreach ($gruplar as $g_isim => $g_kodlar): 
                                $sutunSayisi = isset($doluBolgeler[$g_isim]) ? count($doluBolgeler[$g_isim]) : 0;
                                if ($sutunSayisi == 0) continue;
                            ?>
                                <th colspan="<?= $sutunSayisi ?>" class="py-2 small text-uppercase fw-bold border-bottom-0">
                                    <?= $g_isim ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                        <tr class="text-center small fw-bold" style="background: #fdfdfd;">
                            <?php foreach ($gruplar as $g_isim => $g_kodlar): 
                                if (!isset($doluBolgeler[$g_isim])) continue;
                                $bolgeListesi = array_keys($doluBolgeler[$g_isim]);
                                sort($bolgeListesi);
                                foreach ($bolgeListesi as $bNo): ?>
                                    <th class="border-top-0"><?= $bNo ?>. BÖLGE</th>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($gruplar as $g_isim => $g_kodlar): 
                                if (!isset($doluBolgeler[$g_isim])) continue;
                                $bolgeListesi = array_keys($doluBolgeler[$g_isim]);
                                sort($bolgeListesi);
                                foreach ($bolgeListesi as $bNo): ?>
                                    <td class="p-0 align-top" style="min-width: 130px;">
                                        <?php if (isset($matris[$g_isim][$bNo])): foreach ($matris[$g_isim][$bNo] as $mah): ?>
                                            <div class="d-flex justify-content-between align-items-center p-2 border-bottom mahalle-row">
                                                <div style="line-height: 1.1;">
                                                    <div class="small fw-bold text-dark"><?= $mah->adi ?></div>
                                                    <div class="text-muted" style="font-size: 10px;"><?= $mah->ilce_adi ?></div>
                                                </div>
                                                <span class="badge bg-danger-subtle text-danger fw-bold rounded-pill" style="font-size: 10px;">
                                                    <?= $mah->hastasayisi ?>
                                                </span>
                                            </div>
                                        <?php endforeach; endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                    <tfoot class="bg-light fw-bold text-center">
                        <tr>
                            <?php foreach ($gruplar as $g_isim => $g_kodlar): 
                                if (!isset($doluBolgeler[$g_isim])) continue;
                                $bolgeListesi = array_keys($doluBolgeler[$g_isim]);
                                sort($bolgeListesi);
                                foreach ($bolgeListesi as $bNo): 
                                    $toplamH = 0;
                                    if (isset($matris[$g_isim][$bNo])) {
                                        foreach ($matris[$g_isim][$bNo] as $mah) $toplamH += (int)$mah->hastasayisi;
                                    }
                                ?>
                                    <td class="py-2 border-top-2">
                                        <div style="font-size: 9px; color:#aaa;">TOPLAM</div>
                                        <div class="text-primary"><?= $toplamH ?></div>
                                    </td>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .matris-table th, .matris-table td { border-color: #dee2e6 !important; }
    .mahalle-row:last-child { border-bottom: none !important; }
    .mahalle-row:hover { background-color: #fff9f9; }
    @media print {
        .no-print { display: none !important; }
        .container-fluid { width: 100% !important; padding: 0 !important; }
        .card { border: 0 !important; box-shadow: none !important; }
        body { background: white !important; font-size: 10px !important; }
    }
</style>