<?php
/**
 * ESH v2.0 Yapılandırma Dosyası
 */

// Veritabanı Bilgileri
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'DDH123**?');
define('DB_NAME', 'esh');
define('DB_PREFIX', 'esh_');

// Uygulama Temel Yolları (Kritik Eksiklik)
// Bu sayede her yerden ana dizine güvenle erişebiliriz
define('ROOT_PATH', realpath(dirname(__DIR__))); 
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', ROOT_PATH . '/views');

// Uygulama URL Ayarları
define('SITEURL', 'http://localhost'); // Canlıya geçince burayı güncellersin
define('ASSETS_URL', SITEURL . '/public/assets'); 

// Zaman Ayarı (Aktifleştirildi)
define('TIMEZONE', 'Europe/Istanbul');
date_default_timezone_set(TIMEZONE);

// Hata Raporlama
error_reporting(E_ALL);
ini_set('display_errors', 1);

// TomTom API Key
define('TOMTOM_KEY', '6Nw0E48SHVaoOxvAWxFVRYArWkTsUMeB');
// config.php
define('START_LAT', '37.7744'); // Örnek Denizli koordinatı
define('START_LNG', '29.0875');
define('START_NAME', 'DDH Evde Bakım');
/**
 * Oturum Güvenliği
 * Session çalınmasını önlemek için temel ayarlar
 */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    // session_start(); // Genelde index.php'de başlatılır ama burada da durabilir
}