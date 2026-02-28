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
    
    // 2. Sıralama Mantığı
    $orderby  = $_GET['orderby'] ?? 'h.isim';
    $orderdir = (isset($_GET['orderdir']) && strtoupper($_GET['orderdir']) === 'DESC') ? 'DESC' : 'ASC';
    $ordering = $orderby . ' ' . $orderdir;

    // 3. Model İşlemleri
    $model = new Patient();
    // Toplam sayıyı alırken aramayı gönder (Pagination'ın bozulmaması için)
    $totalPatients = $model->countAllPassive($search); 
    
    // Listeyi alırken aramayı gönder
    $patients = $model->getAllPassive($limit, $offset, $ordering, $search);
    
    // 4. Link Yapılandırması
    // Sıralama ve limit bilgilerini pagelink'e eklemiyoruz çünkü 
    // PaginationHelper::render artık bunları $_GET üzerinden otomatik temizleyip ekliyor.
    $pagelink = "index.php?controller=Patient&action=listpassive";
    if (isset($search)) {
    $pagelink .= "&search=".$search;
    }
    
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
        $result = $model->store();

        if ($result) {
            header("Location: index.php?controller=Patient&action=listwaiting&msg=ok");
        } else {
            header("Location: index.php?controller=Patient&action=ilkkayit&msg=error");
        }
        exit;
    
    }

    public function edit() {
        $id = $_GET['id'];
        $patient = (new Patient())->getById($id);
        
        $districts = (new Address())->getDistricts();
        $hastaliklar = (new Hastalik())->getList(); 
        
        $guvence = (new Guvence())->getList(); 

        $patient->diger_adres = $data = json_decode($patient->diger_adres ?? '[]', true);
        include '../views/partials/header.php';
        include '../views/site/hasta/ekle.php';
        include '../views/partials/footer.php';
    }
    
    public function view() {
        $id = $_GET['id'];
        $hasta = (new Patient())->getById($id);
        
        $districts = (new Address())->getDistricts();
        $hastaliklar = (new Hastalik())->getList(); 
        
        $adres = (new Address())->getUserAddress($hasta->id);
        
        $guvence = (new Guvence())->getList(); 
        
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

        $hasta->diger_adres = json_decode($patient->diger_adres ?? '[]', true);
        include '../views/partials/header.php';
        include '../views/site/hasta/detail.php';
        include '../views/partials/footer.php';
    }
    
    public function store() {
        $model = new Patient();
        $data = $_POST;

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
        $result = $model->store();

        if ($result) {
            header("Location: index.php?controller=Patient&action=list&msg=ok");
        } else {
            header("Location: index.php?controller=Patient&action=create&msg=error");
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
}