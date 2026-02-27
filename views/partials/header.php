<!DOCTYPE html>
<html lang="tr" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESH v2.0 | Evde Sağlık Hizmetleri</title>
    
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
<link rel="stylesheet" type="text/css" href="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.0/maps/maps.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.0/maps/maps-web.min.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.0/services/services-web.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.tr.min.js"></script>

<script src="<?= SITEURL;?>/public/assets/global.js"></script>
    
    <style>
        :root { --esh-blue: #0d6efd; }
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; display: flex; flex-direction: column; height: 100%; }
        main { flex-shrink: 0; }
        .navbar { z-index: 1030; }
        .dropdown-menu { z-index: 1040; border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); }
        .chosen-container-single .chosen-single { height: 38px; line-height: 34px; border: 1px solid #dee2e6; border-radius: 6px; }
    </style>
</head>
<body class="d-flex flex-column h-100">
<?php 
// Otomatik bildirimleri çalıştır
\App\Helpers\AlertHelper::display(); 
?>
<?php
// Değişkenleri globalden al
$cName = $GLOBALS['controllerName'] ?? 'Dashboard';
$aName = $GLOBALS['actionName'] ?? 'index';

// UIHelper'ı bu değişkenlerle çağır
\App\Helpers\UIHelper::renderTopMenu($cName, $aName);
?>

    <main class="flex-grow-1 py-4">