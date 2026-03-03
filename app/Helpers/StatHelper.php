<?php
namespace App\Helpers;

use App\Models\Stat; // Modelimizi dahil ettik

class StatHelper {
    /**
     * Bugün doğum günü olan hastaları listeler
     * Bootstrap 5 ve FontAwesome 6 uyumlu
     */
    public static function dogumGunuListesi() {
        
        $statModel = new Stat();
        $rows = $statModel->getTodaysBirthdays();

        if (empty($rows)) {
            return '<div class="alert alert-light border shadow-sm"><i class="fa-solid fa-circle-info me-2 text-info"></i>Bugün doğum günü olan hasta bulunmamaktadır.</div>';
        }

        ob_start(); // Çıktıyı tampona alıyoruz
        ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="fa-solid fa-cake-candles me-2"></i>Bugün Doğum Günü Olanlar
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="bg-light">
                        <tr>
                            <th>Hasta Adı</th>
                            <th>TC Kimlik</th>
                            <th>Bölge</th>
                            <th>Yaş</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): 
                            $isimRenk = ($row->cinsiyet == '1' || $row->cinsiyet == 'E') ? '#0d6efd' : '#dc3545';
                            $yas = \App\Helpers\DateHelper::calculateAge($row->dogumtarihi); // Yas bulma helper'ını çağırdık
                        ?>
                        <tr>
                            <td>
                                <a href="index.php?controller=Patient&action=view&id=<?= $row->id; ?>" class="text-decoration-none fw-semibold" style="color: <?= $isimRenk; ?>;">
                                    <?= $row->isim . " " . $row->soyisim; ?>
                                </a>
                            </td>
                            <td class="text-muted"><?= $row->tckimlik; ?></td>
                            <td>
                                <span class="small d-block text-truncate" style="max-width: 150px;"><?= $row->mahalle; ?></span>
                                <span class="badge bg-success-subtle text-success fw-normal"><?= $row->ilce; ?></span>
                            </td>
                            <td><span class="badge bg-primary rounded-pill"><?= $yas; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        return ob_get_clean(); // Tamponu döndür
    }
    
    public static function yasGruplariGrafigi() {
    $model = new \App\Models\Stat();
    $rows = $model->getAgeGroups();

    // Veri Şablonu
    $template = ['g01' => 0, 'g22' => 0, 'g318' => 0, 'g1945' => 0, 'g4665' => 0, 'g6685' => 0, 'g86' => 0];
    $stats = ['E' => $template, 'K' => $template];

    foreach ($rows as $r) {
        // Cinsiyet kontrolü (1/2 veya E/K uyumu)
        $c = ($r->cinsiyet == '1' || $r->cinsiyet == 'E') ? 'E' : 'K';
        foreach ($template as $key => $val) {
            $stats[$c][$key] = (int)$r->$key;
        }
    }

    $labels = [
        'g01' => '0-1 Aylık', 'g22' => '2 Ay-2 Yaş', 'g318' => '3-18 Yaş', 
        'g1945' => '19-45 Yaş', 'g4665' => '46-65 Yaş', 'g6685' => '66-85 Yaş', 'g86' => '86 Yaş+'
    ];

    ob_start();
    ?>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fa-solid fa-chart-column me-2 text-primary"></i>Yaş Grupları Dağılımı (v4)
            </h6>
        </div>
        <div class="card-body">
            <div style="height: 320px; position: relative;">
                <canvas id="ageChartV4"></canvas>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-sm table-hover align-middle border-top small">
                    <thead class="table-light">
                        <tr>
                            <th>Yaş Aralığı</th>
                            <th class="text-center">Kadın</th>
                            <th class="text-center">Erkek</th>
                            <th class="text-center">Toplam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $tK = $tE = 0;
                        foreach($labels as $key => $label): 
                            $vK = $stats['K'][$key]; $vE = $stats['E'][$key];
                            $tK += $vK; $tE += $vE;
                        ?>
                        <tr>
                            <td class="fw-medium"><?= $label ?></td>
                            <td class="text-center text-danger"><?= $vK ?></td>
                            <td class="text-center text-primary"><?= $vE ?></td>
                            <td class="text-center fw-bold text-dark"><?= ($vK + $vE) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td>GENEL TOPLAM</td>
                            <td class="text-center"><?= $tK ?></td>
                            <td class="text-center"><?= $tE ?></td>
                            <td class="text-center bg-primary text-white"><?= ($tK + $tE) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
    // Sayfada birden fazla grafik varsa çakışmaması için IIFE (Anlık Fonksiyon) kullanıyoruz
    (function() {
        const dataE = <?= json_encode(array_values($stats['E'])) ?>;
        const dataK = <?= json_encode(array_values($stats['K'])) ?>;
        const labels = <?= json_encode(array_values($labels)) ?>;

        new Chart(document.getElementById('ageChartV4'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Erkek',
                        data: dataE,
                        backgroundColor: '#0d6efd', // Bootstrap 5 Primary
                        borderRadius: 6,           // v4 ile gelen yumuşak köşeler
                        borderSkipped: false,
                        barPercentage: 0.8,
                        categoryPercentage: 0.6
                    },
                    {
                        label: 'Kadın',
                        data: dataK,
                        backgroundColor: '#dc3545', // Bootstrap 5 Danger
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.8,
                        categoryPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'x', // Yatay yapmak istersen 'y' yapabilirsin
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true, // Kare yerine yuvarlak gösterge
                            pointStyle: 'circle',
                            padding: 20,
                            font: { family: 'Inter', size: 12 }
                        }
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        callbacks: {
                            label: function(context) {
                                return ` ${context.dataset.label}: ${context.raw} Hasta`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }, // X ekseni çizgilerini gizle (daha temiz görünüm)
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        ticks: { stepSize: 1, font: { size: 11 } }
                    }
                }
            }
        });
    })();
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Bu ayki izlem sıklığını gösteren dashboard widget'ı
 */
public static function izlemSikligiWidget() {
    $model = new \App\Models\Stat();
    $data = $model->getMonthlyFollowUpStats();

    $toplamHasta = (int)$data->toplamhasta;
    $toplamIzlem = (int)$data->toplamizlem;
    $siklik = ($toplamHasta > 0) ? round($toplamIzlem / $toplamHasta, 2) : 0;

    ob_start();
    ?>
    <div class="card shadow-sm border-0 mb-4 border-start border-success border-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-success">
                <i class="fa-solid fa-list-check me-2"></i>Bu Ayki İzlem Sıklığı
            </h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-borderless mb-0 align-middle">
                <thead class="bg-light small text-uppercase text-muted">
                    <tr>
                        <th class="ps-3 py-2">Toplam Hasta</th>
                        <th class="py-2">Toplam İzlem</th>
                        <th class="pe-3 py-2">Sıklık</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-3 py-3">
                            <span class="h4 mb-0 fw-bold text-dark"><?= $toplamHasta ?></span>
                        </td>
                        <td class="py-3">
                            <span class="h4 mb-0 fw-bold text-primary"><?= $toplamIzlem ?></span>
                        </td>
                        <td class="pe-3 py-3 text-end">
                            <span class="badge bg-warning text-dark fs-6 rounded-pill px-3 py-2">
                                <i class="fa-solid fa-calculator me-1"></i> <?= $siklik ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="card-footer bg-white border-0 py-2">
                <small class="text-muted italic">
                    * Mevcut ay içerisindeki tamamlanmış izlem verileridir.
                </small>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
/**
 * Bu ay izlenen yaş grupları widget'ı
 */
public static function izlenenYasGruplariWidget() {
    $model = new \App\Models\Stat();
    $data = $model->getMonthlyFollowUpAgeGroups();

    // Toplamı hesapla
    $toplam = (int)$data->g01 + (int)$data->g22 + (int)$data->g318 + (int)$data->g1945 + (int)$data->g4665 + (int)$data->g6685 + (int)$data->g86;

    $gruplar = [
        ['label' => '0-1 Yaş / Aylık', 'deger' => $data->g01],
        ['label' => '2 Yaş', 'deger' => $data->g22],
        ['label' => '3-18 Yaş', 'deger' => $data->g318],
        ['label' => '19-45 Yaş', 'deger' => $data->g1945],
        ['label' => '46-65 Yaş', 'deger' => $data->g4665],
        ['label' => '66-85 Yaş', 'deger' => $data->g6685],
        ['label' => '86 Yaş ve Üzeri', 'deger' => $data->g86]
    ];

    ob_start();
    ?>
    <div class="card shadow-sm border-0 mb-4 border-top border-danger border-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-danger">
                <i class="fa-solid fa-users-viewfinder me-2"></i>Bu Ay İzlenen Yaş Grupları
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="ps-3">Yaş Aralığı</th>
                            <th class="text-end pe-3">Hasta Sayısı</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gruplar as $grup): ?>
                        <tr>
                            <td class="ps-3"><?= $grup['label'] ?></td>
                            <td class="text-end pe-3 fw-bold"><?= (int)$grup['deger'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-danger fw-bold">
                        <tr>
                            <td class="ps-3 text-uppercase">Toplam</td>
                            <td class="text-end pe-3"><?= $toplam ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

public static function genelIstatistikWidget() {
    $model = new \App\Models\Stat();
    $data = $model->getGeneralStats();
    
    // Hatanın çözümü: Veri nesne mi ve property var mı kontrolü
    $newMale   = isset($data->new->new_male) ? (int)$data->new->new_male : 0;
    $newFemale = isset($data->new->new_female) ? (int)$data->new->new_female : 0;
    $totalNew  = $newMale + $newFemale;

    $exitMale   = isset($data->exit->exit_male) ? (int)$data->exit->exit_male : 0;
    $exitFemale = isset($data->exit->exit_female) ? (int)$data->exit->exit_female : 0;
    $totalExit  = $exitMale + $exitFemale;

    ob_start(); ?>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-chart-line me-2 text-primary"></i>Genel Sistem Özeti</h6>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="small text-uppercase text-muted fw-bold mb-1">Toplam Ulaşılan Hasta</div>
                    <div class="h3 mb-0 fw-bold text-dark"><?= number_format($data->general->total_reached, 0, ',', '.') ?></div>
                </li>
                
                <li class="list-group-item p-3">
                    <div class="small text-uppercase text-muted fw-bold mb-2">Aktif Kayıtlı Hasta</div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-warning text-dark px-3 py-2">Toplam: <?= $data->general->active_total ?></span>
                        <span class="badge bg-primary px-3 py-2">Erkek: <?= $data->general->active_male ?></span>
                        <span class="badge bg-danger px-3 py-2">Kadın: <?= $data->general->active_female ?></span>
                    </div>
                </li>

                <li class="list-group-item p-3 bg-light-subtle">
                    <div class="small text-uppercase text-muted fw-bold mb-2">Bu Ay Takibe Başlananlar</div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-outline-dark border text-dark px-3 py-2">Toplam: <?= $totalNew ?></span>
                        <span class="badge bg-primary-subtle text-primary px-2 py-2">E: <?= $data->new->new_male ?></span>
                        <span class="badge bg-danger-subtle text-danger px-2 py-2">K: <?= $data->new->new_female ?></span>
                    </div>
                </li>

                <li class="list-group-item p-3">
                    <div class="small text-uppercase text-muted fw-bold mb-2">Bu Ay Takipten Çıkarılanlar</div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-outline-dark border text-dark px-3 py-2">Toplam: <?= $totalExit ?></span>
                        <span class="badge bg-secondary px-2 py-2">E: <?= $data->exit->exit_male ?></span>
                        <span class="badge bg-secondary px-2 py-2">K: <?= $data->exit->exit_female ?></span>
                    </div>
                </li>

                <li class="list-group-item p-3 border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-uppercase text-muted fw-bold">Tam Bağımlı Hasta</div>
                            <div class="h4 mb-0 fw-bold text-indigo"><?= $data->general->fully_dependent ?></div>
                        </div>
                        <div class="icon-shape bg-indigo-subtle text-indigo p-3 rounded">
                            <i class="fa-solid fa-bed-pulse fs-4"></i>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <?php return ob_get_clean();
}
/**
 * Bu ayki hastaların takipten çıkarılma nedenlerini ve sayılarını döner.
 */
public function getExitReasons() {
    $query = "SELECT pasifnedeni, COUNT(id) AS sayi 
              FROM {$this->_tbl} 
              WHERE pasif = 1 
              AND pasiftarihi >= DATE_FORMAT(NOW() ,'%Y-%m-01') 
              AND pasiftarihi <= LAST_DAY(NOW()) 
              GROUP BY pasifnedeni";

    return $this->db->setQuery($query)->loadObjectList();
}
/**
 * Takipten çıkarılma nedenlerini gösteren pasta grafik ve tablo widget'ı
 */
public static function cikarilmaNedenleriWidget() {
    $model = new \App\Models\Stat();
    $rows = $model->getExitReasons();

    // Sabit Neden Ayarları
    $nedenAyarlar = [
        '1' => ['isim' => 'İyileşme', 'renk' => '#FF6384'],
        '2' => ['isim' => 'Vefat', 'renk' => '#36A2EB'],
        '3' => ['isim' => 'İkamet Değişikliği', 'renk' => '#FFCE56'],
        '4' => ['isim' => 'Tedaviyi Reddetme', 'renk' => '#4BC0C0'],
        '5' => ['isim' => 'Tedaviye Yanıt Alamama', 'renk' => '#9966FF'],
        '6' => ['isim' => 'Sonlandırmanın Talep Edilmesi', 'renk' => '#FF9F40'],
        '7' => ['isim' => 'Tedaviye Personel Gerekmemesi', 'renk' => '#C9CBCF'],
        '8' => ['isim' => 'ESH Takibine Uygun Olmaması', 'renk' => '#2ECC71']
    ];

    $labels = []; $data = []; $colors = []; $toplam = 0;
    $tableRows = [];

    foreach ($rows as $row) {
        $id = $row->pasifnedeni;
        if (isset($nedenAyarlar[$id])) {
            $labels[] = $nedenAyarlar[$id]['isim'];
            $data[]   = (int)$row->sayi;
            $colors[] = $nedenAyarlar[$id]['renk'];
            $toplam  += (int)$row->sayi;
            
            $tableRows[] = [
                'isim' => $nedenAyarlar[$id]['isim'],
                'sayi' => $row->sayi,
                'renk' => $nedenAyarlar[$id]['renk']
            ];
        }
    }

    ob_start();
    ?>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-danger">
                <i class="fa-solid fa-chart-pie me-2"></i>Takipten Çıkarılma Nedenleri
            </h6>
        </div>
        <div class="card-body">
            <?php if ($toplam > 0): ?>
                <div style="position: relative; height:200px;" class="mb-4">
                    <canvas id="exitReasonChart"></canvas>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10px;"></th>
                                <th>Neden</th>
                                <th class="text-end">Hasta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tableRows as $tr): ?>
                                <tr>
                                    <td><span class="badge p-1 rounded-circle" style="background-color:<?= $tr['renk'] ?>;">&nbsp;</span></td>
                                    <td><?= $tr['isim'] ?></td>
                                    <td class="text-end fw-bold"><?= $tr['sayi'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2">BU AY TOPLAM</td>
                                <td class="text-end text-danger"><?= $toplam ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted small">
                    <i class="fa-solid fa-folder-open d-block mb-2 fs-3"></i>
                    Bu ay henüz takipten çıkarılan hasta bulunmuyor.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($toplam > 0): ?>
    <script>
    (function() {
        new Chart(document.getElementById('exitReasonChart'), {
            type: 'doughnut', // Pie yerine Doughnut daha modern durur
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: <?= json_encode($data) ?>,
                    backgroundColor: <?= json_encode($colors) ?>,
                    hoverOffset: 10,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', // Simit inceliği
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let percentage = ((value / <?= $toplam ?>) * 100).toFixed(1);
                                return ` ${label}: ${value} Hasta (%${percentage})`;
                            }
                        }
                    }
                }
            }
        });
    })();
    </script>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}
}