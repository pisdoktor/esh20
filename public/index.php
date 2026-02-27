<?php
/**
 * ANA GİRİŞ NOKTASI (ROUTER)
 */
session_start();

// 1. Ayarları ve Autoloader'ı yükle
require_once '../config/config.php';

spl_autoload_register(function ($class) {
    // Proje kök dizini (Namespace'lerin başladığı yer)
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    // Sınıf ismi 'App\' ile başlıyor mu?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Geri kalan kısmı al (Örn: Controllers\PatientController)
    $relative_class = substr($class, $len);

    // Ters slaşları düz slaş yap ve sonuna .php ekle
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Dosya varsa yükle
    if (file_exists($file)) {
        require $file;
    }
});

// 2. URL'den parametreleri al (Varsayılan: Dashboard/index)
$controllerReq = isset($_GET['controller']) ? $_GET['controller'] : '';
$actionReq     = isset($_GET['action'])     ? $_GET['action']     : '';


// Yeni yapıya uygun gelmişse (controller=Patient&action=list gibi)
$controllerName = $controllerReq ? ucfirst($controllerReq) : 'Dashboard';
$actionName     = $actionReq ? $actionReq : 'index';
    
// BURAYI EKLE: Global olarak tanımla ki her yerden erişilsin
$GLOBALS['controllerName'] = $controllerName;
$GLOBALS['actionName'] = $actionName;

// Auth controller ve login işlemleri hariç her yerde giriş kontrolü yap
$isLoginAction = ($controllerName == 'Auth' && in_array($actionName, ['login', 'doLogin']));

if (!isset($_SESSION['user_id']) && !$isLoginAction) {
    header("Location: index.php?controller=Auth&action=login");
    exit;
}

// 4. Sınıfı ve Metodu çalıştır
$controllerClass = "\\App\\Controllers\\" . $controllerName . "Controller";

if (class_exists($controllerClass)) {
    $controllerInstance = new $controllerClass();
    
    if (method_exists($controllerInstance, $actionName)) {
        // Metodu çalıştır
        $controllerInstance->$actionName();
    } else {
        header("HTTP/1.0 404 Not Found");
        die("Hata: <b>{$actionName}</b> metodu <b>{$controllerClass}</b> içinde bulunamadı!");
    }
} else {
    header("HTTP/1.0 404 Not Found");
    die("Hata: <b>{$controllerClass}</b> sınıfı sistemde kayıtlı değil!");
}