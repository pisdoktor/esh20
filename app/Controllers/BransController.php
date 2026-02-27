<?php
namespace App\Controllers;

use App\Models\Brans;

/**
 * Branş Yönetimi Controller (Admin Paneli)
 * esh_branslar tablosundaki verilerin yönetimini sağlar.
 */
class BransController {
    
    public function __construct() {
    if (!isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != true) {
        $_SESSION['error'] = "Bu alana erişim yetkiniz bulunmamaktadır!";
        header("Location: index.php");
        exit;
    }
}
    
    /**
     * Branş Listesi
     * Görünüm: views/admin/brans/index.php
     */
    public function index() {
        $model = new Brans();
        $items = $model->getList();
        
        $pageTitle = "Tıbbi Branş Tanımları";
        
        include '../views/partials/header.php';
        include '../views/admin/brans/index.php'; // Klasör yapısı güncellendi
        include '../views/partials/footer.php';
    }

    /**
     * Yeni Branş Ekleme Formu
     * Görünüm: views/admin/brans/create.php
     */
    public function create() {
        include '../views/partials/header.php';
        include '../views/admin/brans/create.php';
        include '../views/partials/footer.php';
    }

    /**
     * Branş Düzenleme Formu
     * Görünüm: views/admin/brans/edit.php
     */
    public function edit() {
        $id = $_GET['id'] ?? null;
        $model = new Brans();
        
        if ($id && $model->load($id)) {
            $item = $model; // View dosyasında kullanılacak veri
            include '../views/partials/header.php';
            include '../views/admin/brans/edit.php';
            include '../views/partials/footer.php';
        } else {
            $_SESSION['error'] = "Düzenlenecek kayıt bulunamadı!";
            header("Location: index.php?controller=Brans&action=index");
            exit;
        }
    }

    /**
     * Kaydetme ve Güncelleme
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new Brans();
            
            // Hidden input 'id' varsa mevcut kaydı yükle (UPDATE için)
            if (!empty($_POST['id'])) {
                $model->load($_POST['id']);
            }
            
            // POST verilerini nesneye aktar
            $model->bind($_POST);
            
            if ($model->store()) {
                $_SESSION['success'] = "İşlem başarıyla tamamlandı.";
            } else {
                $_SESSION['error'] = "Veri kaydedilirken bir hata oluştu!";
            }
            
            header("Location: index.php?controller=Brans&action=index");
            exit;
        }
    }

    /**
     * Branş Silme
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        $model = new Brans();
        
        if ($id && $model->load($id)) {
            if ($model->delete()) {
                $_SESSION['success'] = "Branş başarıyla silindi.";
            } else {
                $_SESSION['error'] = "Bu branş silinemez! Başka verilerle bağlantısı olabilir.";
            }
        }
        
        header("Location: index.php?controller=Brans&action=index");
        exit;
    }
}