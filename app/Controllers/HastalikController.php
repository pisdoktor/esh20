<?php
namespace App\Controllers;

use App\Models\Hastalik;
use App\Models\HastalikCat;

/**
 * Hastalık Kütüphanesi Controller (Admin Paneli)
 * esh_hastaliklar ve esh_hastalikcat tablolarını yönetir.
 */
class HastalikController {
    
    /**
     * Hastalık Listesi
     * Görünüm: views/admin/hastalik/index.php
     */
    public function index() {
        $model = new Hastalik();
        // Modelde daha önce hazırladığımız, kategorileri de getiren JOIN'li fonksiyonu kullanıyoruz
        $items = $model->getDetailedList(); 
        
        $pageTitle = "Hastalık ve Tanı Kütüphanesi";
        
        include '../views/partials/header.php';
        include '../views/admin/hastalik/index.php';
        include '../views/partials/footer.php';
    }

    /**
     * Yeni Hastalık Ekleme Formu
     * Görünüm: views/admin/hastalik/create.php
     */
    public function create() {
        $catModel = new HastalikCat();
        $categories = $catModel->getList(); // Dropdown'da listelenecek kategoriler
        
        include '../views/partials/header.php';
        include '../views/admin/hastalik/create.php';
        include '../views/partials/footer.php';
    }

    /**
     * Hastalık Düzenleme Formu
     * Görünüm: views/admin/hastalik/edit.php
     */
    public function edit() {
        $id = $_GET['id'] ?? null;
        $model = new Hastalik();
        
        if ($id && $model->load($id)) {
            $catModel = new HastalikCat();
            $categories = $catModel->getList(); // Kategorileri de çekiyoruz ki düzenleme ekranında seçilebilsin
            
            $item = $model;
            include '../views/partials/header.php';
            include '../views/admin/hastalik/edit.php';
            include '../views/partials/footer.php';
        } else {
            $_SESSION['error'] = "Hastalık kaydı bulunamadı!";
            header("Location: index.php?controller=Hastalik&action=index");
            exit;
        }
    }

    /**
     * Kaydetme ve Güncelleme (Store)
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new Hastalik();
            
            if (!empty($_POST['id'])) {
                $model->load($_POST['id']);
            }
            
            $model->bind($_POST);
            
            if ($model->store()) {
                $_SESSION['success'] = "Hastalık/Tanı bilgileri başarıyla güncellendi.";
            } else {
                $_SESSION['error'] = "Hastalık kaydedilirken bir hata oluştu!";
            }
            
            header("Location: index.php?controller=Hastalik&action=index");
            exit;
        }
    }

    /**
     * Hastalık Silme
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        $model = new Hastalik();
        
        if ($id && $model->load($id)) {
            // Silme işlemi
            if ($model->delete()) {
                $_SESSION['success'] = "Hastalık kütüphaneden kalıcı olarak silindi.";
            } else {
                $_SESSION['error'] = "Bu hastalık silinemez! Hastalarla ilişkili olabilir.";
            }
        }
        
        header("Location: index.php?controller=Hastalik&action=index");
        exit;
    }
}