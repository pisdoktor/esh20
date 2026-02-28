<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    /**
     * Giriş Sayfasını Gösterir
     */
    public function login() {
        // Eğer zaten giriş yapılmışsa direkt dashboard'a gönder
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=Dashboard&action=index");
            exit;
        }
        include '../views/login.php';
    }

    /**
     * Giriş İşlemini Doğrular (doLogin)
     */
    public function doLogin() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        
        // 1. Kullanıcıyı bul
        if ($userModel->loadByUsername($username)) {
            // 2. Şifre kontrolü (Sorunun çözümü burada)
            if (password_verify($password, $userModel->password)) {
                // 3. Giriş başarılı, session başla
                if ($userModel->activated) {
                $_SESSION['user_id'] = $userModel->id;
                $_SESSION['name'] = $userModel->name;
                $_SESSION['username'] = $userModel->username;
                $_SESSION['isadmin'] = $userModel->isadmin;
                
                $_SESSION['success'] = 'Giriş yapıldı';
                
                $userModel->updateVisitDate($userModel->id); // Giriş tarihini güncelle
                header("Location: index.php?controller=Dashboard");
                exit;
                
                } else {
                    $_SESSION['error'] = 'Hesabınız henüz aktive edilmemiş';
                    header("Location: index.php?controller=Auth&action=login");
                    exit;
                }
            }
        }
        
        $_SESSION['error'] = "Kullanıcı adı veya şifre hatalı!";
        header("Location: index.php?controller=Auth&action=login");
    }

    /**
     * Oturumu Kapatır (logout)
     */
    public function logout() {
        session_destroy();
        header("Location: index.php?controller=Auth&action=login");
        exit;
    }
}