<?php
namespace App\Helpers;

class BadgeHelper {

    /**
     * Genel bir Badge (Etiket) oluşturur
     */
    public static function render($text, $type = 'secondary', $pill = false) {
        $class = $pill ? 'rounded-pill' : '';
        return "<span class='badge bg-{$type} {$class} text-uppercase' style='font-size: 0.75rem;'>{$text}</span>";
    }

    /**
     * Durum (Aktif/Pasif) için renkli etiket
     */
    public static function status($val) {
        if ($val == 0) {
            return self::render('Aktif', 'success', true);
        }
        return self::render('Pasif', 'danger', true);
    }

    /**
     * Cinsiyet için renkli etiket
     */
    public static function gender($val) {
        $val = strtoupper($val);
        if ($val == '1') {
            return self::render('<i class="fa-solid fa-mars me-1"></i>Erkek', 'info');
        } elseif ($val == '2') {
            return self::render('<i class="fa-solid fa-venus me-1"></i>Kadın', 'danger'); // Pembe tonu için danger veya custom
        }
        return self::render('Belirsiz', 'secondary');
    }

    /**
     * İzlem Öncelik Durumu
     */
    public static function priority($val) {
        switch ($val) {
            case 1: return self::render('Düşük', 'info');
            case 2: return self::render('Normal', 'primary');
            case 3: return self::render('Yüksek', 'warning');
            case 4: return self::render('Acil', 'danger');
            default: return self::render('Belirsiz', 'secondary');
        }
    }

    /**
     * Yaş grubuna göre otomatik etiket (DateHelper ile koordineli)
     */
    public static function ageGroup($birthDate) {
        $age = DateHelper::calculateAge($birthDate);
        if ($age === '-') return self::render('-', 'secondary');

        if ($age < 18) return self::render($age . ' (Çocuk)', 'info');
        if ($age >= 65) return self::render($age . ' (Yaşlı/Riskli)', 'danger');
        
        return self::render($age . ' (Yetişkin)', 'success');
    }

    /**
     * Yapıldı mı/Yapılmadı mı durumu
     */
    public static function isCompleted($val) {
        if ($val == 1) {
            return "<span class='text-success'><i class='fa-solid fa-check-double me-1'></i>Yapıldı</span>";
        }
        return "<span class='text-warning'><i class='fa-solid fa-clock me-1'></i>Bekliyor</span>";
    }
    
    /**
     * Kullanıcı Yetki Durumu (isadmin)
     */
    public static function adminStatus($val) {
        if ($val == 1) {
            return self::render('<i class="fa-solid fa-user-shield me-1"></i>Admin', 'dark', true);
        }
        return self::render('<i class="fa-solid fa-user me-1"></i>Personel', 'secondary', true);
    }

    /**
     * Hesap Aktivasyon Durumu (activated)
     */
    public static function activationStatus($val) {
        if ($val == 1) {
            return self::render('Onaylı', 'success');
        }
        return self::render('Beklemede', 'warning');
    }
}