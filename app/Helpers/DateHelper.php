<?php
namespace App\Helpers;

use DateTime;

class DateHelper {

    /**
     * Doğum tarihinden yaş hesaplar
     * @param string $birthday (Y-m-d formatında)
     * @return int|string
     */
    public static function calculateAge($birthday) {
        if (!$birthday || $birthday == '0000-00-00') return '-';

        $birthDate = new DateTime($birthday);
        $today = new DateTime('today');
        
        // diff metodu iki tarih arasındaki farkı bir nesne olarak döner
        return $birthDate->diff($today)->y;
    }

    /**
     * Veritabanı formatını (Y-m-d) Türkçe formatına (d.m.Y) çevirir
     */
    public static function toTr($date) {
        if (!$date || $date == '0000-00-00') return '-';
        return date('d.m.Y', strtotime($date));
    }

    /**
     * "2 saat önce", "5 gün önce" gibi insan odaklı zaman formatı
     */
    public static function timeAgo($datetime) {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) return 'Az önce';
        $mins = round($diff / 60);
        if ($mins < 60) return $mins . ' dakika önce';
        $hours = round($diff / 3600);
        if ($hours < 24) return $hours . ' saat önce';
        $days = round($diff / 86400);
        if ($days < 30) return $days . ' gün önce';
        
        return self::toTr($datetime);
    }
}