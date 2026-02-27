<?php
namespace App\Controllers;

use App\Models\User;

class UserController {
    // ==========================================================
    // SITE / KULLANICI BÖLÜMÜ (Kendi Profilini Yönetme)
    // ==========================================================

    /**
     * Kullanıcının kendi profil sayfasını gösterir
     */
    public function index() {
        $userId = $_SESSION['user_id']; // Sadece oturumdaki ID
        $userModel = new User();
        $userModel->load($userId);
        
        $user = $userModel;
        include '../views/partials/header.php';
        include '../views/site/user/index.php'; 
        include '../views/partials/footer.php';
    }

    /**
     * Kullanıcının kendi profilini düzenleme formu
     */
    public function edit() {
        $userId = $_SESSION['user_id'];
        $userModel = new User();
        $userModel->load($userId);
        
        $user = $userModel;
        include '../views/partials/header.php';
        include '../views/site/user/edit.php';
        include '../views/partials/footer.php';
    }

    /**
     * Kullanıcının kendi bilgilerini güncellemesi (POST)
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $userModel = new User();
            $userModel->load($userId);

            // Şifre güncelleme kontrolü
            if (!empty($_POST['new_password'])) {
                if ($_POST['new_password'] === $_POST['confirm_password']) {
                    $_POST['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                } else {
                    $_SESSION['error'] = "Şifreler uyuşmuyor!";
                    header("Location: index.php?controller=User&action=edit");
                    exit;
                }
            }

            // Kritik alanları (adminlik gibi) kullanıcı kendisi değiştiremesin diye temizliyoruz
            unset($_POST['isadmin']);
            unset($_POST['activated']);

            if ($userModel->updateProfile($userId, $_POST)) {
                $_SESSION['success'] = "Profil bilgileriniz güncellendi.";
            } else {
                $_SESSION['error'] = "Güncelleme sırasında bir hata oluştu.";
            }
            
            header("Location: index.php?controller=User&action=index");
            exit;
        }
    }

    // ==========================================================
    // ADMIN BÖLÜMÜ (Tüm Kullanıcıları Yönetme)
    // ==========================================================

    /**
     * Admin: Tüm kullanıcıların listesi
     */
    public function list() {
        $model = new User();
        $items = $model->getList();
        $pageTitle = "Kullanıcı / Personel Yönetimi";
        
        include '../views/partials/header.php';
        include '../views/admin/user/index.php';
        include '../views/partials/footer.php';
    }

    /**
     * Admin: Yeni kullanıcı ekleme formu
     */
    public function create() {
        $pageTitle = "Yeni Personel Ekle";
        include '../views/partials/header.php';
        include '../views/admin/user/create.php';
        include '../views/partials/footer.php';
    }

    /**
     * Admin: Bir kullanıcıyı düzenleme formu (Dışarıdan ID alır)
     */
    public function adminEdit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=User&action=list");
            exit;
        }

        $userModel = new User();
        $userModel->load($id);
        $user = $userModel;
        
        include '../views/partials/header.php';
        include '../views/admin/user/edit.php'; // Admin için ayrı bir edit view kullanman daha iyi olur
        include '../views/partials/footer.php';
    }

    /**
     * Admin: Kullanıcı kaydetme veya güncelleme işlemi
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new User();
            
            if (!empty($_POST['id'])) {
                $model->load($_POST['id']);
            }

            // Şifre işlemleri
            if (!empty($_POST['password'])) {
                $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else if (!empty($_POST['id'])) {
                unset($_POST['password']);
            }

            // Checkbox kontrolleri (Admin paneli olduğu için bunlar serbest)
            $_POST['isadmin'] = isset($_POST['isadmin']) ? 1 : 0;
            $_POST['activated'] = isset($_POST['activated']) ? 1 : 0;

            $model->bind($_POST);
            
            if ($model->store()) {
                $_SESSION['success'] = "Kullanıcı başarıyla kaydedildi.";
            } else {
                $_SESSION['error'] = "Hata oluştu!";
            }
            
            header("Location: index.php?controller=User&action=list");
            exit;
        }
    }
    
    public function image() {
        // Oturumdaki kullanıcı ID'sini al
        $userId = $_SESSION['user_id'];
        
        $userModel = new User();
        // BaseModel'den gelen load() metodu ile kullanıcıyı yükle
        $userModel->load($userId);
        
        // View dosyasında kullanmak üzere $user değişkenine ata
        $user = $userModel; 
        
        $temp_image = $_SESSION['temp_photo'] ?? null;
        include '../views/partials/header.php';
        include '../views/site/user/photo.php';
        include '../views/partials/footer.php';
    }
    

public function upload() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Sadece resim formatlarına izin ver
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $_SESSION['error'] = "Sadece JPG ve PNG dosyaları yüklenebilir.";
            header("Location: index.php?controller=User&action=image");
            exit;
        }

        $tempName = 'temp_' . $_SESSION['user_id'] . '.' . $ext;
        $tempPath = '../public/uploads/temp/' . $tempName;

        if (!is_dir('../public/uploads/temp/')) {
            mkdir('../public/uploads/temp/', 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $tempPath)) {
            // Geçici resim yolunu session'a atıyoruz ki Jcrop görebilsin
            $_SESSION['temp_image'] = $tempPath;
            header("Location: index.php?controller=User&action=image");
        } else {
            $_SESSION['error'] = "Dosya geçici dizine taşınamadı.";
            header("Location: index.php?controller=User&action=index");
        }
        exit;
    }
}

public function cropsave() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['temp_image'])) {
        $src = $_SESSION['temp_image'];
        $info = getimagesize($src);
        $type = $info[2];

        // Kaynak resmi oluştur
        $img = ($type == IMAGETYPE_JPEG) ? imagecreatefromjpeg($src) : imagecreatefrompng($src);
        
        // Jcrop'tan gelen koordinatlar
        $x = (int)$_POST['x'];
        $y = (int)$_POST['y'];
        $w = (int)$_POST['w'];
        $h = (int)$_POST['h'];

        // 300x300 boyutunda yeni bir boş resim oluştur
        $targ_w = 300;
        $targ_h = 300;
        $dest = imagecreatetruecolor($targ_w, $targ_h);

        // PNG şeffaflık ayarı
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
        }

        // Kesme ve Yeniden Boyutlandırma
        imagecopyresampled($dest, $img, 0, 0, $x, $y, $targ_w, $targ_h, $w, $h);

        // Nihai klasöre kaydet
        $finalName = 'user_' . $_SESSION['user_id'] . '_' . time() . '.jpg';
        $finalPath = '../public/uploads/profile/' . $finalName;

        if (!is_dir('../public/uploads/profile/')) {
            mkdir('../public/uploads/profile/', 0777, true);
        }

        imagejpeg($dest, $finalPath, 90);

        // Veritabanını güncelle
        $user = new User();
        $user->load($_SESSION['user_id']);
        $user->image = $finalPath;
        $user->store();

        // Temizlik: Geçici dosyayı ve session'ı sil
        if (file_exists($src)) unlink($src);
        unset($_SESSION['temp_image']);

        $_SESSION['success'] = "Profil resminiz başarıyla güncellendi.";
        header("Location: index.php?controller=User&action=index");
        exit;
    }
}
}