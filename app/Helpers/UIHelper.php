<?php
namespace App\Helpers;

class UIHelper {
    /**
     * Üst menüyü render eder
     */
    public static function renderTopMenu($currentController, $currentAction) {
        ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="index.php?controller=Dashboard&action=index">
                    <i class="fa-solid fa-house-medical me-2 text-primary"></i>ESH PANEL
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#eshNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="eshNavbar">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentController == 'Dashboard' && $currentAction == 'index') ? 'active' : '' ?>" 
                               href="index.php?controller=Dashboard&action=index">Anasayfa</a>
                        </li>

                        <li class="nav-item dropdown <?= ($currentController == 'Patient') ? 'active' : '' ?>">
                            <a class="nav-link dropdown-toggle" href="#" id="hastaMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Hasta İşlemleri
                            </a>
                            <ul class="dropdown-menu shadow border-0" aria-labelledby="hastaMenu">
                                <li>
                                    <a class="dropdown-item" href="index.php?controller=Patient&action=listactive">
                                        <i class="fa-solid fa-user-check text-success me-2"></i>Aktif Hasta Listesi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="index.php?controller=Patient&action=listpassive">
                                        <i class="fa-solid fa-user-slash text-secondary me-2"></i>Pasif Hasta Listesi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="index.php?controller=Patient&action=listwaiting">
                                        <i class="fa-solid fa-user-clock text-info me-2"></i>Bekleyen Hasta Listesi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="index.php?controller=Patient&action=ilkkayit">
                                        <i class="fa-solid fa-user-plus text-warning me-2"></i>Yeni Hasta İlk Kayıt
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="izlemMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                İzlem İşlemleri
                            </a>
                            <ul class="dropdown-menu shadow border-0" aria-labelledby="izlemMenu">
                            <li>
                                    <a class="dropdown-item" href="index.php?controller=Visit&action=index">
                                        <i class="fa-solid fa-user-check text-success me-2"></i>Aktif İzlem Listesi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="index.php?controller=PlannedVisit&action=index">
                                        <i class="fa-solid fa-user-slash text-secondary me-2"></i>Planlanmış İzlem Listesi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="index.php?controller=Pansuman&action=index">
                                        <i class="fa-solid fa-user-slash text-secondary me-2"></i>Pansuman Listesi
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($currentController == 'Erapor') ? 'active fw-bold text-info' : '' ?>" 
                               href="index.php?controller=Erapor&action=index">
                               <i class="fa-solid fa-file-waveform me-1"></i> e-Rapor Havuzu
                            </a>
                        </li>

                        <?php if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == true): ?>
<li class="nav-item dropdown position-static"> <a class="nav-link dropdown-toggle bg-warning text-dark rounded px-3 ms-lg-2" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user-shield me-1"></i> Yönetim
    </a>
    
    <ul class="dropdown-menu shadow-lg border-0 w-100 mt-0 p-4" aria-labelledby="adminDropdown" style="min-width: 600px;">
        <li>
            <div class="row">
                <div class="col-md-4">
                    <div class="mt-3">
                        <a class="dropdown-item fw-bold bg-light rounded <?= ($currentAction == 'admin') ? 'active' : 'text-primary' ?>" href="index.php?controller=Dashboard&action=admin">
                            <i class="fas fa-chart-line me-2"></i>Admin Dashboard
                        </a>
                    </div>
                    <h6 class="dropdown-header text-dark fw-bold border-bottom mb-2">
                        <i class="fas fa-stethoscope me-1"></i> Tıbbi Tanımlamalar
                    </h6>
                    <a class="dropdown-item" href="index.php?controller=Brans&action=index">Branş Yönetimi</a>
                    <a class="dropdown-item" href="index.php?controller=Hastalik&action=index">Hastalık Kütüphanesi</a>
                    <a class="dropdown-item" href="index.php?controller=Islem&action=index">İşlem Tanımları</a>
                    
                    
                </div>

                <div class="col-md-4 border-start">
                    <h6 class="dropdown-header text-dark fw-bold border-bottom mb-2">
                        <i class="fas fa-cogs me-1"></i> Sistem Ayarları
                    </h6>
                    <a class="dropdown-item <?= ($currentController == 'Ekip') ? 'active' : '' ?>" href="index.php?controller=Ekip&action=index">Ekip Planlama</a>
                    <a class="dropdown-item" <?= ($currentController == 'Guvence') ? 'active' : '' ?> href="index.php?controller=Guvence&action=index">Güvence Türleri</a>
                    <a class="dropdown-item" <?= ($currentController == 'User') ? 'active' : '' ?> href="index.php?controller=User&action=list">Kullanıcı Yönetimi</a>
                </div>

                <div class="col-md-4 border-start">
                    <h6 class="dropdown-header text-dark fw-bold border-bottom mb-2">
                        <i class="fas fa-hospital-user me-1"></i> Hasta İşlemleri
                    </h6>
                    <a class="dropdown-item" href="index.php?controller=Patient&action=listdeleted">
                        <i class="fa-solid fa-trash-can me-2 small"></i>Silinen Hastalar
                    </a>
                    <a class="dropdown-item" href="index.php?controller=Patient&action=listdied">
                        <i class="fa-solid fa-skull me-2 small"></i>Ölen Hastalar
                    </a>
                    <a class="dropdown-item" href="index.php?controller=Patient&action=listaraf">
                        <i class="fa-solid fa-hourglass-half me-2 small"></i>Araftaki Hastalar
                    </a>
                    <a class="dropdown-item" href="index.php?controller=Patient&action=scan">
                        <i class="fa-solid fa-hourglass-half me-2 small"></i>Toplu Vefat Taraması
                    </a>
                </div>
            </div>
        </li>
    </ul>
</li>
<?php endif; ?>
                    </ul>

                    <ul class="navbar-nav ms-auto">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center border border-secondary rounded-pill px-3" href="#" 
           id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-circle-user fs-5 me-2 text-primary"></i>
            <span><?= $_SESSION['username'] ?? 'Kullanıcı' ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenu">
            <li>
                <a class="dropdown-item py-2" href="index.php?controller=User&action=index">
                    <i class="fa-solid fa-user-circle me-2 text-secondary"></i>Profilim
                </a>
            </li>
            <li>
                <a class="dropdown-item py-2" href="index.php?controller=User&action=edit">
                    <i class="fa-solid fa-user-gear me-2 text-secondary"></i>Profil Ayarları
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger fw-bold py-2" href="index.php?controller=Auth&action=logout">
                    <i class="fa-solid fa-power-off me-2"></i>Güvenli Çıkış
                </a>
            </li>
        </ul>
    </li>
</ul>
                </div>
            </div>
        </nav>
        <?php
    }
    
    /**
 * Tablo başlıkları için sıralama ikonunu render eder
 */
/**
 * Tablo başlıkları için sıralama ikonunu ve yönünü yönetir
 */
public static function sortIcon($field, $ordering) {
    // Mevcut sıralamayı parçala (Örn: "h.isim ASC")
    $bits = explode(' ', $ordering);
    $currentField = $bits[0];
    $currentDir = isset($bits[1]) ? strtoupper($bits[1]) : 'ASC';

    // Varsayılan ikon (sıralama yoksa)
    $icon = ' <i class="fa-solid fa-sort text-muted opacity-25"></i>';
    $nextDir = 'ASC'; // Hiç sıralanmamışsa ilk tıklama ASC olsun

    if ($field == $currentField) {
        if ($currentDir == 'ASC') {
            $icon = ' <i class="fa-solid fa-sort-up text-primary"></i>';
            $nextDir = 'DESC'; // ASC ise bir sonraki tıklama DESC olsun
        } else {
            $icon = ' <i class="fa-solid fa-sort-down text-primary"></i>';
            $nextDir = 'ASC'; // DESC ise bir sonraki tıklama ASC olsun
        }
    }

    // Hem ikonu hem de bir sonraki yönü dizi olarak döndürüyoruz
    return ['icon' => $icon, 'nextDir' => $nextDir];
}
}