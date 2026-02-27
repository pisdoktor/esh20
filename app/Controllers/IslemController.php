<?php
namespace App\Controllers;

use App\Models\Islem;

/**
 * Tıbbi İşlem Tanımları Controller (Admin Paneli)
 * esh_islemler tablosundaki verilerin yönetimini sağlar.
 */
class IslemController {
    
    /**
     * İşlem Listesi
     * Görünüm: views/admin/islem/index.php
     */
    public function index() {
        $model = new Islem();
        $items = $model->getList();
        
        $pageTitle = "Tıbbi İşlem ve Müdahale Tanımları";
        
        include '../views/partials/header.php';
        include '../views/admin/islem/index.php';
        include '../views/partials/footer.php';
    }

    /**
     * Yeni İşlem Ekleme Formu
     * Görünüm: views/admin/islem/create.php
     */
    public function create() {
        include '../views/partials/header.php';
        include '../views/admin/islem/create.php';
        include '../views/partials/footer.php';
    }

    /**
     * İşlem Düzenleme Formu
     * Görünüm: views/admin/islem/edit.php
     */
    public function edit() {
        $id = $_GET['id'] ?? null;
        $model = new Islem();
        
        if ($id && $model->load($id)) {
            $item = $model;
            include '../views/partials/header.php';
            include '../views/admin/islem/edit.php';
            include '../views/partials/footer.php';
        } else {
            $_SESSION['error'] = "Düzenlenecek işlem kaydı bulunamadı!";
            header("Location: index.php?controller=Islem&action=index");
            exit;
        }
    }

    /**
     * Kaydetme ve Güncelleme (Store)
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new Islem();
            
            // Update kontrolü için ID var mı bak
            if (!empty($_POST['id'])) {
                $model->load($_POST['id']);
            }
            
            $model->bind($_POST);
            
            if ($model->store()) {
                $_SESSION['success'] = "İşlem tanımı başarıyla kaydedildi.";
            } else {
                $_SESSION['error'] = "Kayıt sırasında teknik bir hata oluştu!";
            }
            
            header("Location: index.php?controller=Islem&action=index");
            exit;
        }
    }

    /**
     * İşlem Silme
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        $model = new Islem();
        
        if ($id && $model->load($id)) {
            // Eğer bu işlem daha önce bir ziyarette (Visit) kullanılmışsa silme hatası verebilir (İlişkisel Bütünlük)
            if ($model->delete()) {
                $_SESSION['success'] = "İşlem tanımı sistemden kaldırıldı.";
            } else {
                $_SESSION['error'] = "Bu işlem silinemez! Geçmiş ziyaret kayıtlarında kullanılmış görünüyor.";
            }
        }
        
        header("Location: index.php?controller=Islem&action=index");
        exit;
    }
}