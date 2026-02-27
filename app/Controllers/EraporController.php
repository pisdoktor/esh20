<?php
namespace App\Controllers;

use App\Models\Erapor;

/**
 * e-Rapor Havuzu Controller (Admin Paneli)
 * Dışarıdan gelen rapor başvurularını ve sistemdeki hasta eşleşmelerini yönetir.
 */
class EraporController {
    
    /**
     * e-Rapor Havuzu Listesi
     * Görünüm: views/admin/erapor/index.php
     */
    public function index() {
        $model = new Erapor();
        
        // Tüm raporları getir
        $reports = $model->getAllReports();
        
        // Her bir rapor için sistemde kayıtlı hasta olup olmadığını kontrol et
        // matchWithSystem() metodu modelde 'kayitlimi' sütununu otomatik günceller
        foreach ($reports as $report) {
            $rObj = new Erapor();
            if ($rObj->load($report->id)) {
                $rObj->matchWithSystem();
            }
        }

        $pageTitle = "e-Rapor Başvuru Havuzu";
        
        include '../views/partials/header.php';
        include '../views/site/erapor/index.php';
        include '../views/partials/footer.php';
    }
    
    /**
     * Yeni Rapor Verisi Giriş Formu
     * Görünüm: views/site/erapor/create.php
     */
    public function create() {
        // Branşları model üzerinden çekiyoruz
        // Not: App\Models\Brans modelinizin olduğunu varsayıyorum
        $bransModel = new \App\Models\Brans();
        $branslar = $bransModel->getList(); // Tüm branşları getiren fonksiyon

        $pageTitle = "Yeni Rapor Kaydı";
        
        include '../views/partials/header.php';
        include '../views/site/erapor/create.php';
        include '../views/partials/footer.php';
    }

    /**
     * Rapor Detaylarını Görüntüle
     * Görünüm: views/admin/erapor/view.php
     */
    public function view() {
        $id = $_GET['id'] ?? null;
        $model = new Erapor();
        
        if ($id && $model->load($id)) {
            $item = $model;
            include '../views/partials/header.php';
            include '../views/site/erapor/view.php';
            include '../views/partials/footer.php';
        } else {
            $_SESSION['error'] = "Rapor detayı bulunamadı!";
            header("Location: index.php?controller=Erapor&action=index");
            exit;
        }
    }

    /**
     * Raporu Havuzdan Sil
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        $model = new Erapor();
        
        if ($id && $model->load($id)) {
            if ($model->delete()) {
                $_SESSION['success'] = "Rapor kaydı havuzdan kaldırıldı.";
            } else {
                $_SESSION['error'] = "Silme işlemi sırasında bir hata oluştu.";
            }
        }
        
        header("Location: index.php?controller=Erapor&action=index");
        exit;
    }

    /**
     * Manuel Eşleştirme (Opsiyonel)
     * Eğer TC tutmuyorsa ama isimden eşleşme yapılabiliyorsa kullanılır.
     */
    public function markAsProcessed() {
        $id = $_GET['id'] ?? null;
        $model = new Erapor();
        
        if ($id && $model->load($id)) {
            $model->kayitlimi = 1;
            $model->store();
            $_SESSION['success'] = "Rapor işlendi olarak işaretlendi.";
        }
        
        header("Location: index.php?controller=Erapor&action=index");
        exit;
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new Erapor();
            
            // Yeni kayıt için ID'yi temizle (BaseModel uyumu için)
            if (isset($_POST['id']) && $_POST['id'] === '') {
                unset($_POST['id']);
            }

            // Formdan gelen verileri modele bağla
            $model->bind($_POST);
            
            // Veritabanına kaydet
            if ($model->store()) {
                $_SESSION['success'] = "Rapor verisi başarıyla havuzuna eklendi.";
            } else {
                $_SESSION['error'] = "Kayıt sırasında bir hata oluştu!";
            }
            
            header("Location: index.php?controller=Erapor&action=index");
            exit;
        }
    }
}