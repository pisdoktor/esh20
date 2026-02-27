<?php
namespace App\Controllers;

use App\Models\Address;

class AddressController {
    
    public function getSubAddresses() {
    if (ob_get_length()) ob_clean(); // Önceki çıktıları temizle
    header('Content-Type: application/json');
    
    $parentId = isset($_GET['parent_id']) ? $_GET['parent_id'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';

    if (!$parentId || !$type) {
        echo json_encode([]);
        exit;
    }

    $addressModel = new \App\Models\Address();
    $data = $addressModel->getSubs($parentId, $type);
if (empty($data)) {
    // Hangi sorgu başarısız?
    header('Content-Type: text/plain');
    die("Sorgu sonucu bos! Parent: $parentId, Type: $type");
}
    
    // Veri gerçekten gelmiş mi bak, gelmediyse boş dizi dön
    echo json_encode($data ? $data : []);
    exit;
}
}