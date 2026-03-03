<?php
namespace App\Controllers;

use App\Models\Patient;
use App\Models\Address;
use App\Models\Hastalik;
use App\Models\Guvence;
use App\Models\Islem;
use App\Models\Visit;
use App\Models\PlannedVisit;

class PatientController {
    
    
    // Aktif hastaları listeler pasif=0
    public function listactive() { 
    // 1. Parametreleri Yakala
    $limit    = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset   = ($page - 1) * $limit;
    $search   = isset($_GET['search']) ? trim($_GET['search']) : ''; // Arama terimini al
    
    // 2. Sıralama Mantığı
    $orderby  = $_GET['orderby'] ?? 'h.isim';
    $orderdir = (isset($_GET['orderdir']) && strtoupper($_GET['orderdir']) === 'DESC') ? 'DESC' : 'ASC';
    $ordering = $orderby . ' ' . $orderdir;

    // 3. Model İşlemleri
    $model = new Patient();
    // Toplam sayıyı alırken aramayı gönder (Pagination'ın bozulmaması için)
    $totalPatients = $model->countAllActive($search); 
    
    // Listeyi alırken aramayı gönder
    $patients = $model->getAllActive($limit, $offset, $ordering, $search);
    
    // 4. Link Yapılandırması
    // Sıralama ve limit bilgilerini pagelink'e eklemiyoruz çünkü 
    // PaginationHelper::render artık bunları $_GET üzerinden otomatik temizleyip ekliyor.
    $pagelink = "index.php?controller=Patient&action=listactive";
    if (isset($search)) {
    $pagelink .= "&search=".$search;
    }
    
    $viewlink   = "index.php?controller=Patient&action=view&id=";
    $editlink   = "index.php?controller=Patient&action=edit&id=";
    $deletelink = "index.php?controller=Patient&action=delete&id="; 

    // 5. View'a Gönderilecek Ek Değişkenler
    $pageTitle = "Aktif Hasta Listesi";
    
    // View'da sortIcon() fonksiyonunun çalışması için $ordering değişkeni lazım
    // View dosyasına (liste.php) dahil ediyoruz
    include '../views/partials/header.php';
    include '../views/site/hasta/listactive.php';
    include '../views/partials/footer.php';
    }
    
    //Pasif hastaları listeler pasif=1
    public function listpassive() {
    // 1. Parametreleri Yakala
    $limit    = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset   = ($page - 1) * $limit;
    $search   = isset($_GET['search']) ? trim($_GET['search']) : ''; // Arama terimini al
    $reason   = isset($_GET['reason']) ? trim($_GET['reason']) : ''; // Yeni filtre
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
    $endDate   = isset($_GET['endDate']) ? $_GET['endDate'] : '';
    
    // 2. Sıralama Mantığı
    $orderby  = $_GET['orderby'] ?? 'h.isim';
    $orderdir = (isset($_GET['orderdir']) && strtoupper($_GET['orderdir']) === 'DESC') ? 'DESC' : 'ASC';
    $ordering = $orderby . ' ' . $orderdir;

    // 3. Model İşlemleri
    $model = new Patient();
    // Toplam sayıyı alırken aramayı gönder (Pagination'ın bozulmaması için)
    $totalPatients = $model->countAllPassive($search, $reason, $startDate, $endDate); 
    
    // Listeyi alırken aramayı gönder
    $patients = $model->getAllPassive($limit, $offset, $ordering, $search, $reason, $startDate, $endDate);
    
    $pasifListesi = [
        '1' => 'İyileşme',
        '2' => 'Vefat',
        '3' => 'İkamet Değişikliği',
        '4' => 'Tedaviyi Reddetme',
        '5' => 'Tedaviye Yanıt Alamama',
        '6' => 'Sonlandırmanın Talep Edilmesi',
        '7' => 'Tedaviye Personel Gerekmemesi',
        '8' => 'ESH Takibine Uygun Olmaması'
    ];
    
    // 4. Link Yapılandırması
    // Sıralama ve limit bilgilerini pagelink'e eklemiyoruz çünkü 
    // PaginationHelper::render artık bunları $_GET üzerinden otomatik temizleyip ekliyor.
    $pagelink = "index.php?controller=Patient&action=listpassive";
    if (isset($search)) { $pagelink .= "&search=".$search; }
    if (isset($reason)) { $pagelink .= "&reason=".$reason; }
    if (isset($startDate)) { $pagelink .= "&startDate=".$startDate; }
    if (isset($endDate)) { $pagelink .= "&endDate=".$endDate;}
    
    $viewlink   = "index.php?controller=Patient&action=view&id=";
    $editlink   = "index.php?controller=Patient&action=edit&id=";
    $deletelink = "index.php?controller=Patient&action=delete&id="; 

    // 5. View'a Gönderilecek Ek Değişkenler
    $pageTitle = "Pasif Hasta Listesi";
    
    // View'da sortIcon() fonksiyonunun çalışması için $ordering değişkeni lazım
    // View dosyasına (liste.php) dahil ediyoruz
    include '../views/partials/header.php';
    include '../views/site/hasta/listpassive.php';
    include '../views/partials/footer.php';
    }
    
    //Bekleyen hastaları listeler pasif=-3
    public function listwaiting() {
        // 1. Parametreleri Yakala
    $limit    = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset   = ($page - 1) * $limit;
    $search   = isset($_GET['search']) ? trim($_GET['search']) : ''; // Arama terimini al
    
    // 2. Sıralama Mantığı
    $orderby  = $_GET['orderby'] ?? 'h.isim';
    $orderdir = (isset($_GET['orderdir']) && strtoupper($_GET['orderdir']) === 'DESC') ? 'DESC' : 'ASC';
    $ordering = $orderby . ' ' . $orderdir;

        $model = new Patient();
        
        $totalPatients = $model->countAllWaiting($search); 
        $patients = $model->getAllWaiting($limit, $offset, $ordering, $search);
        $totalPages = ceil($totalPatients / $limit);
        
        $pagelink = "index.php?controller=Patient&action=listwaiting";
        $viewlink = "index.php?controller=Patient&action=bview&id=";
        $editlink = "index.php?controller=Patient&action=bedit&id=";
        $deletelink = "";

        $pageTitle = "Bekleyen Hasta Listesi";
        include '../views/partials/header.php';
        include '../views/site/hasta/listwaiting.php';
        include '../views/partials/footer.php';
    }
    
    //Ölen hastaları listeler pasif=-1
    public function listdied() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $model = new Patient();
        
        $totalPatients = $model->countAllDied(); 
        $patients = $model->getAllDied($limit, $offset);
        $totalPages = ceil($totalPatients / $limit);
        
        $pagelink = "index.php?controller=Patient&action=listdied";
        $viewlink = "index.php?controller=Patient&action=view&id=";
        $editlink = "";
        $deletelink = "";

        $pageTitle = "Ölen Hasta Listesi";
        include '../views/partials/header.php';
        include '../views/site/hasta/liste.php';
        include '../views/partials/footer.php';
    }
    
    //Bekleyen hasta detay gösterimi
    public function bview() {
    
    }
    
    //bekelyen hasta düzenlemesi
    public function bedit() {
        $id = $_GET['id'];
        $patient = (new Patient())->getById($id);
        
        $districts = (new Address())->getDistricts();
        
        $guvence = (new Guvence())->getList(); 

        $patient->diger_adres = $data = json_decode($patient->diger_adres ?? '[]', true);
        include '../views/partials/header.php';
        include '../views/site/hasta/bedit.php';
        include '../views/partials/footer.php';
    
    }
    
    //Araftaki hastaları listeler pasif=4
    public function listaraf() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $model = new Patient();
        
        $totalPatients = $model->countAllAraf(); 
        $patients = $model->getAllAraf($limit, $offset);
        $totalPages = ceil($totalPatients / $limit);
        
        $pagelink = "index.php?controller=Patient&action=listaraf";
        $viewlink = "index.php?controller=Patient&action=aview&id=";
        $editlink = "index.php?controller=Patient&action=aedit&id=";
        $deletelink = "index.php?controller=Patient&action=adelete&id=";

        $pageTitle = "Arafta Bekleyen Hasta Listesi";
        include '../views/partials/header.php';
        include '../views/site/hasta/liste.php';
        include '../views/partials/footer.php';
    } 
    
    //Silinen hastaları listeler pasif=5
    public function listdeleted() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $model = new Patient();
        
        $totalPatients = $model->countAllDeleted(); 
        $patients = $model->getAllDeleted($limit, $offset);
        $totalPages = ceil($totalPatients / $limit);
        
        $pagelink = "index.php?controller=Patient&action=listdeleted";
        $viewlink = "index.php?controller=Patient&action=dview&id=";
        $editlink = "index.php?controller=Patient&action=dedit&id=";
        $deletelink = "";

        $pageTitle = "Silinen Hasta Listesi";
        include '../views/partials/header.php';
        include '../views/site/hasta/liste.php';
        include '../views/partials/footer.php';
    }
    
    public function ilkkayit() {
        
        $districts = (new Address())->getDistricts(); 
        
        $hastaliklar = (new Hastalik())->getList();
        
        $guvence = (new Guvence())->getList();
        
        include '../views/partials/header.php';
        include '../views/site/hasta/ilkkayit.php';
        include '../views/partials/footer.php';
    }
    
    public function fsave() {
        $model = new Patient();
        $data = $_POST;
        
        $data['dogumtarihi'] = date('Y.m.d', strtotime($data['dogumtarihi']));
        $data['kayittarihi'] = date('Y.m.d', strtotime($data['kayittarihi']));
        $data['randevutarihi'] = date('Y.m.d', strtotime($data['randevutarihi']));
        
        $data['pasif'] = '-3';
    
        // Çoklu Adres İşleme
        if (isset($data['adres']) && is_array($data['adres'])) {
            $anaIndex = $data['ana_adres_index'] ?? 0;
            $yedekler = [];

            foreach ($data['adres'] as $idx => $val) {
                if ($idx == $anaIndex) {
                    // Ana adresi modelin ana kolonlarına yaz
                    $data['ilce'] = $val['ilce'] ?? null;
                    $data['mahalle'] = $val['mahalle'] ?? null;
                    $data['sokak'] = $val['sokak'] ?? null;
                    $data['kapino'] = $val['kapino'] ?? null;
                    $data['adres_aciklama'] = $val['adres_aciklama'] ?? null;
                } else {
                    $yedekler[] = $val;
                }
            }
            $data['diger_adres'] = json_encode($yedekler, JSON_UNESCAPED_UNICODE);
        }

        $model->bind($data);
        
        // Kayıt işlemi
        if ($model->store()) {
        $_SESSION['success'] = "Hasta ön kaydı başarıyla oluşturuldu.";
        header("Location: index.php?controller=Patient&action=listwaiting");
            } else {
        $_SESSION['error'] = "Veritabanı hatası: Kayıt tamamlanamadı.";
        header("Location: index.php?controller=Patient&action=ilkkayit");
        }
        
        exit;
    }

    public function edit() {
        $id = $_GET['id'];
        $patient = (new Patient())->getById($id);
        
        $districts = (new Address())->getDistricts();
        $hastaliklar = (new Hastalik())->getList(); 
        
        $guvence = (new Guvence())->getList();
        
          $barthelFields = [
    'barbeslenme' => [
        'label' => 'Beslenme', 'max' => 10, 
        'desc' => '0: Bağımlı, 5: Yardımla (kesme vb.), 10: Bağımsız'
    ],
    'barbanyo' => [
        'label' => 'Banyo', 'max' => 5, 
        'desc' => '0: Bağımlı, 5: Bağımsız'
    ],
    'barbakim' => [
        'label' => 'Kişisel Bakım', 'max' => 5, 
        'desc' => '0: Yardımla, 5: Bağımsız (yüz yıkama, diş fırçalama vb.)'
    ],
    'bargiyinme' => [
        'label' => 'Giyinme', 'max' => 10, 
        'desc' => '0: Bağımlı, 5: Yardımla, 10: Bağımsız (düğme, bağcık dahil)'
    ],
    'barbarsak' => [
        'label' => 'Bağırsak', 'max' => 10, 
        'desc' => '0: İnkontinan, 5: Arada kaza olur, 10: Kontrollü'
    ],
    'barmesane' => [
        'label' => 'Mesane', 'max' => 10, 
        'desc' => '0: İnkontinan, 5: Arada kaza olur, 10: Kontrollü'
    ],
    'bartuvalet' => [
        'label' => 'Tuvalet', 'max' => 10, 
        'desc' => '0: Bağımlı, 5: Yardımla, 10: Bağımsız'
    ],
    'bartransfer' => [
        'label' => 'Transfer', 'max' => 15, 
        'desc' => '0: Bağımlı, 5: İleri derece yardım, 10: Hafif yardım, 15: Bağımsız'
    ],
    'barmobilite' => [
        'label' => 'Mobilite', 'max' => 15, 
        'desc' => '0: İmmobil, 5: Tekerlekli sandalye, 10: Yardımla yürüme, 15: Bağımsız'
    ],
    'barmerdiven' => [
        'label' => 'Merdiven', 'max' => 10, 
        'desc' => '0: Bağımlı, 5: Yardımla, 10: Bağımsız'
    ]
];
        $barthelscore = (new Patient())->getBarthelScore();
        
        // En başa "Seçiniz" seçeneği ekleyelim (Obje olarak)
        $options[] = \App\Helpers\FormHelper::makeOption('', 'Hastalık Seçiniz');
    
        foreach($hastaliklar as $hastalik) {
        // makeOption zaten bir stdClass (obje) döndürür
        $options[] = \App\Helpers\FormHelper::makeOption($hastalik->id, $hastalik->icd.'-'.$hastalik->hastalikadi);
        }
        
        $lists = (new Address())->getAdresListeleri($patient);
        
        $patient->hastaliklar = explode(',', $patient->hastaliklar);
        
        $hast = \App\Helpers\FormHelper::selectList($options, 'hastaliklar[]', 'multiple="multiple"', 'value', 'text', $patient->hastaliklar);
        
        $boption[] = \App\Helpers\FormHelper::makeOption('', 'Bağımlılık Durumu');
        $boption[] = \App\Helpers\FormHelper::makeOption(1, 'Bağımsız');
        $boption[] = \App\Helpers\FormHelper::makeOption(2, 'Yarı Bağımlı');
        $boption[] = \App\Helpers\FormHelper::makeOption(3, 'Tam Bağımlı');
        
        $bopt = \App\Helpers\FormHelper::selectList($boption, 'bagimlilik', '', 'value', 'text', $patient->bagimlilik);
                
        $patient->diger_adres = json_decode($patient->diger_adres ?? '[]', true);
        include '../views/partials/header.php';
        include '../views/site/hasta/edit.php';
        include '../views/partials/footer.php';
    }
    
    public function view() {
        $id = $_GET['id'];
        $hasta = (new Patient())->getById($id);
        
        $anaadres = (new Address())->getUserAddress($hasta->id);
        
        $hasta->diger_adres = json_decode($hasta->diger_adres ?? '[]', true);
        
        $diger_adres = (new Address())->getUserOtherAddresses($hasta->diger_adres);
        
        $hastaliklar = (new Hastalik())->getUserHastaliklar($hasta->hastaliklar); 
        
        $guvence = (new Guvence())->getUserGuvence($hasta->guvence); 
        
        // Pasif Nedenleri ve İkonları
        $pasif_nedenleri_ikonlu = [
            '1' => ['label' => 'İyileşme', 'icon' => 'fa-person-walking-arrow-right', 'color' => 'text-success'],
            '2' => ['label' => 'Vefat', 'icon' => 'fa-cross', 'color' => 'text-secondary'],
            '3' => ['label' => 'İkamet Değişikliği (GÖÇ)', 'icon' => 'fa-truck-ramp-box', 'color' => 'text-primary'],
            '4' => ['label' => 'Tedaviyi Reddetme', 'icon' => 'fa-user-slash', 'color' => 'text-danger'],
            '5' => ['label' => 'Tedaviye Yanıt Alamama', 'icon' => 'fa-heart-crack', 'color' => 'text-warning'],
            '6' => ['label' => 'Sonlandırmanın Talep Edilmesi', 'icon' => 'fa-comment-slash', 'color' => 'text-info'],
            '7' => ['label' => 'Tedaviye Personel Gerekmemesi', 'icon' => 'fa-user-check', 'color' => 'text-success'],
            '8' => ['label' => 'ESH Takibine Uygun Olmaması', 'icon' => 'fa-house-circle-xmark', 'color' => 'text-danger']
        ];

        $pasifnedeni = "";
        if ($hasta->pasif) {
            if ($hasta->pasif == '1') { $pasifnedeni = $pasif_nedenleri_ikonlu[$hasta->pasifnedeni]['label'] ?? 'Tanımsız'; }
            else if ($hasta->pasif == '-1') { $pasifnedeni = 'Muhtemel Vefat'; }
            else if ($hasta->pasif == '5') { $pasifnedeni = 'Silinmiş Hasta'; }
            else if ($hasta->pasif == '-3') { $pasifnedeni = 'Bekleyen Hasta'; }
            else if ($hasta->pasif == '4') { $pasifnedeni = 'Arafta Hasta'; }
        }
        
    
        
        include '../views/partials/header.php';
        include '../views/site/hasta/detail.php';
        include '../views/partials/footer.php';
    }
    
    public function store() {
        
        $patient = new Patient();
        
        $patient->load($_POST['id']);
        
        
        $patient->dogumtarihi = date('Y.m.d', strtotime($_POST['dogumtarihi']));
        $patient->kayittarihi = date('Y.m.d', strtotime($_POST['kayittarihi']));
        
        if (isset($data['pasiftarihi'])) {
        $patient->pasiftarihi = date('Y.m.d', strtotime($_POST['pasiftarihi']));
        }
        
        $patient->hastaliklar = implode(',',$_POST['hastaliklar']);
        
        // 1. Yeni bir not girilmiş mi kontrol et
        if (!empty(trim($_POST['new_note']))) {
        $existingNotes = json_decode($patient->notes, true) ?: [];
        
        // Yeni notu dizinin başına ekle
        array_unshift($existingNotes, [
            'date'    => date('d.m.Y H:i'),
            'user'    => $_SESSION['name'] ?? 'Sistem',
            'message' => trim($_POST['new_note'])
        ]);
        
        // Güncel JSON'u ana veriye bas
        $patient->notes = json_encode($existingNotes, JSON_UNESCAPED_UNICODE);
        } else {
        // Yeni not yoksa, mevcut notları olduğu gibi koru (textarea boş olsa bile silinmez)
        $patient->notes = $patient->notes;
        }
        
        // Çoklu Adres İşleme
        if (isset($_POST['adres']) && is_array($_POST['adres'])) {
            $anaIndex = $_POST['ana_adres_index'] ?? 0;
            $yedekler = [];

            foreach ($_POST['adres'] as $idx => $val) {
                if ($idx == $anaIndex) {
                    // Ana adresi modelin ana kolonlarına yaz
                    $patient->ilce = $val['ilce'] ?? null;
                    $patient->mahalle = $val['mahalle'] ?? null;
                    $patient->sokak = $val['sokak'] ?? null;
                    $patient->kapino = $val['kapino'] ?? null;
                    $patient->adres_aciklama = $val['adres_aciklama'] ?? null;
                } else {
                    $yedekler[] = $val;
                }
            }
            $patient->diger_adres = json_encode($yedekler, JSON_UNESCAPED_UNICODE);
        }

       
        // Kayıt işlemi
        $result = $patient->store();

        if ($result) {
            $_SESSION['success'] = "Hasta bilgileri kaydedildi.";
            header("Location: index.php?controller=Patient&action=view&id=".$patient->id);
        } else {
            $_SESSION['error'] = "Veritabanı hatası: Hasta bilgileri kaydedilemedi";
            header("Location: index.php?controller=Patient&action=edit&id=".$patient->id);
        }
        exit;
    }
    
    public function checkTC() {
    // Güvenlik: TC gelmezse işlemi durdur
    if (!isset($_GET['tc'])) return;

    $tc = $_GET['tc'];
    $model = new Patient();
    
    // Algoritma kontrolü yap
    $isValid = $model->validateTc($tc);
    
    if ($isValid) {
        // Algoritma doğruysa veritabanında var mı diye bakabilirsin (opsiyonel)
        // $exists = $model->where('tckimlik', $tc)->first();
        
        echo '<span class="text-success small"><i class="fa-solid fa-check"></i> TC Kimlik Numarası Geçerli</span>';
    } else {
        echo '<span class="text-danger small"><i class="fa-solid fa-xmark"></i> Geçersiz TC Kimlik Numarası</span>';
    }
    
    // View dosyasının geri kalanının yüklenmemesi için sonlandırıyoruz
    exit; 
}

    public function died() {
    // 1. Güvenlik: TC parametresini al ve temizle
    $tc = $_GET['tc'];
    
    if (!$tc) {
        header('Content-Type: application/json');
        echo json_encode(['oldu' => 0, 'error' => 'TC gecersiz']);
        return 0;
    }

    // 2. Model üzerinden veriyi çek
    $model = new Patient();
    
    $row = $model->died($tc);
    
    
    $oldu = 0;
    $olumTarihi = null;

    // 3. Kontrol: Eğer hasta bulunduysa entegrasyonu çalıştır
    if ($row) {
        // Bir önceki adımda ValidationHelper içine yazmıştık
        $sonuc = \App\Helpers\ValidationHelper::checkDeathNotification($row);
        if ($sonuc) {
            $oldu = 1;
            $olumTarihi = $sonuc; // Tarihi de döndürebiliriz
        }
    }
    
    // 4. Çıktı: JSON formatında döndür
    header('Content-Type: application/json');
    echo json_encode([
        'oldu' => $oldu,
        'olumTarihi' => $olumTarihi
    ]);
    
    return $oldu;
}

    public function prepareNotes($existingJson, $newNote) {
    // 1. Mevcut notları çöz (Eğer boşsa boş dizi oluştur)
    $notesArray = json_decode($existingJson, true) ?: [];

    $newNote = trim($newNote);

    // 2. Eğer yeni bir not yazılmışsa ekle
    if (!empty($newNote)) {
        $notesArray[] = [
            'date'    => date('d.m.Y H:i'), // Otomatik tarih ve saat
            'user'    => $_SESSION['name'] ?? 'Sistem', // Opsiyonel: Notu yazan kişi
            'message' => htmlspecialchars($newNote)
        ];
    }

    // 3. Tekrar JSON'a çevir (Türkçe karakterleri koruyarak)
    return json_encode($notesArray, JSON_UNESCAPED_UNICODE);
    }
    
    public function updateNotes() {
        $id = (int)$_POST['id'];
        $new_note = $_POST['new_notes'];
        
        $patient = new Patient();
        $patient->load($id);
        
        $mevcutNotlar = $patient->notes;
        
        $new = $this->prepareNotes($mevcutNotlar, $_POST['new_note']);
    
        $patient->notes = $new;
        
        if ($patient->store()) {
        $_SESSION['success'] = "Hasta notu oluşturuldu.";
        header("Location: index.php?controller=Patient&action=view&id=".$patient->id);
            } else {
        $_SESSION['error'] = "Veritabanı hatası: Hasta notu kaydedilemedi.";
        header("Location: index.php?controller=Patient&action=view&id=".$patient->id);
        }
    }
    
    public function deleteNote() {
    $id = (int)$_POST['id'];
    $index = (int)$_POST['index'];

    // 1. Mevcut notları çek
    $patient = new Patient();
    $patient->load($id);
    
    $currentJson = $patient->notes;
    $notesArray = json_decode($currentJson, true);

    if (isset($notesArray[$index])) {
        // 2. Belirtilen index'teki notu sil
        unset($notesArray[$index]);

        // 3. Diziyi yeniden indexle (0,1,2 diye sıralı kalsın)
        $notesArray = array_values($notesArray);

        // 4. Yeni halini kaydet
        $newJson = json_encode($notesArray, JSON_UNESCAPED_UNICODE);
        $patient->notes = $newJson;
        
        if ($patient->store()) {
            echo json_encode(['success' => true, 'message' => 'Not kaydedildi']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Veritabanı hatası.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Not bulunamadı.']);
    }
    exit;
}
}