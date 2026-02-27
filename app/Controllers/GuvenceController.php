<?php
namespace App\Controllers;

use App\Models\Guvence;

/**
 * Sağlık Güvencesi Yönetimi Controller (Admin Paneli)
 * esh_guvence tablosundaki verilerin yönetimini sağlar.
 */
class GuvenceController {
    
    /**
     * Güvence Listesi
     * Görünüm: views/admin/guvence/index.php
     */
    public function index() {
        $model = new Guvence();
        $items = $model->getList();
        
        $pageTitle = "Sağlık Güvencesi Tanımları";
        
        include '../views/partials/header.php';
        include '../views/admin/guvence/index.php';
        include '../views/partials/footer.php';
    }

    /**
     * Yeni Güvence Ekleme Formu
     * Görünüm: views/admin/guvence/create.php
     */
    public function create() {
        include '../views/partials/header.php';
        include '../views/admin/guvence/create.php';
        include '../views/partials/footer.php';
    }

    /**
     * Güvence Düzenleme Formu
     * Görünüm: views/admin/guvence/edit.php
     */
    public function edit() {
        $id = $_GET['id'] ?? null;
        $model = new Guvence();
        
        if ($id && $model->load($id)) {
            $item = $model;
            include '../views/partials/header.php';
            include '../views/admin/guvence/edit.php';
            include '../views/partials/footer.php';
        } else {
            $_SESSION['error'] = "Güvence kaydı bulunamadı!";
            header("Location: index.php?controller=Guvence&action=index");
            exit;
        }
    }

    /**
     * Kaydetme ve Güncelleme (Store)
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $model = new Guvence();
        
        // Eğer id boş gelmişse onu $_POST dizisinden tamamen sil
        if (isset($_POST['id']) && $_POST['id'] === '') {
            unset($_POST['id']);
        }
        
        // Eğer ID doluysa mevcut kaydı yükle (Update için)
        if (!empty($_POST['id'])) {
            $model->load($_POST['id']);
        }
        
        $model->bind($_POST);
        
        if ($model->store()) {
            $_SESSION['success'] = "Kayıt başarılı.";
        }
            
            header("Location: index.php?controller=Guvence&action=index");
            exit;
        }
    }

    /**
     * Güvence Silme
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        $model = new Guvence();
        
        if ($id && $model->load($id)) {
            if ($model->delete()) {
                $_SESSION['success'] = "Güvence tanımı başarıyla silindi.";
            } else {
                $_SESSION['error'] = "Bu güvence silinemez! Hastalarla ilişkili olabilir.";
            }
        }
        
        header("Location: index.php?controller=Guvence&action=index");
        exit;
    }
}