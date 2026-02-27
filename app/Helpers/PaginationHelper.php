<?php
namespace App\Helpers;

class PaginationHelper {

    /**
     * Bootstrap 5 uyumlu limit (sayfa başına kayıt) seçici üretir
     * * @param int $current_limit Mevcut seçili limit
     * @param string $base_url Linklerin başına gelecek URL
     * @return string HTML Çıktısı
     */
    public static function limitSelector($current_limit, $base_url) {
        $options = [5, 10, 15, 20, 25, 50, 100];
        $html = '<div class="d-flex align-items-center small text-muted">';
        $html .= '<span class="me-2">Göster:</span>';
        $html .= '<select class="form-select form-select-sm" style="width: auto;" onchange="location = this.value;">';
        
        foreach ($options as $opt) {
            $selected = ($current_limit == $opt) ? 'selected' : '';
            // Sayfa başına limit değiştiğinde genellikle 1. sayfadan başlamak istenir
            $url = "{$base_url}&limit={$opt}&page=1";
            $html .= "<option value='{$url}' {$selected}>{$opt}</option>";
        }
        
        $html .= '</select>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Özet Bilgi Metni (Örn: 50 kayıt arasından 11-20 gösteriliyor)
     */
    public static function infoText($total, $current_page, $limit) {
        if ($total == 0) return "Kayıt bulunamadı.";
        
        $from = (($current_page - 1) * $limit) + 1;
        $to = min($current_page * $limit, $total);
        
        return "Toplam <strong>{$total}</strong> kayıttan <strong>{$from}-{$to}</strong> arası gösteriliyor.";
    }

    /**
     * Gelişmiş Sayfalama (Özet bilgi ile birlikte)
     */
    public static function render($total, $current_page, $limit, $base_url) {
        $total_pages = ceil($total / $limit);
        if ($total_pages <= 1) {
            return '<div class="text-muted small mt-3">' . self::infoText($total, $current_page, $limit) . '</div>';
        }

        $url_with_limit = "{$base_url}&limit={$limit}";

        $html = '<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">';
        
        // Sol Taraf: Özet Bilgi
        $html .= '<div class="text-muted small mb-3 mb-md-0">';
        $html .= self::infoText($total, $current_page, $limit);
        $html .= '</div>';

        // Sağ Taraf: Sayfa Linkleri
        $html .= '<nav aria-label="Page navigation">';
        $html .= '<ul class="pagination pagination-sm mb-0">';

        // İlk ve Geri
        $disabled = ($current_page <= 1) ? 'disabled' : '';
        $html .= "<li class='page-item {$disabled}'><a class='page-link' href='{$url_with_limit}&page=1' title='İlk Sayfa'>&laquo;</a></li>";

        // Sayfa Numaraları (Akıllı Sınırlandırma)
        $start = max(1, $current_page - 5);
        $end = min($total_pages, $current_page + 5);

        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $current_page) ? 'active' : '';
            $html .= "<li class='page-item {$active}'><a class='page-link' href='{$url_with_limit}&page={$i}'>{$i}</a></li>";
        }

        // İleri ve Son
        $disabled = ($current_page >= $total_pages) ? 'disabled' : '';
        $html .= "<li class='page-item {$disabled}'><a class='page-link' href='{$url_with_limit}&page={$total_pages}' title='Son Sayfa'>&raquo;</a></li>";

        $html .= '</ul></nav></div>';

        return $html;
    }

    /**
     * Sayfaya Git (Jump to Page) - Özellikle çok kayıtlı tablolarda hayat kurtarır
     */
    public static function jumpToPage($total_pages, $base_url, $limit) {
        if ($total_pages < 5) return ""; // Az sayfa varsa gerek yok

        return '
        <div class="input-group input-group-sm ms-3" style="width: 120px;">
            <input type="number" id="jump_page" class="form-control" placeholder="Sfy..." min="1" max="'.$total_pages.'">
            <button class="btn btn-outline-secondary" type="button" onclick="const p = document.getElementById(\'jump_page\').value; if(p > 0 && p <= '.$total_pages.') location.href=\''.$base_url.'&limit='.$limit.'&page=\'+p;">Git</button>
        </div>';
    }
    
   /**
 * Tablo satırları için sayfalama uyumlu sıra numarası üretir
 * @param int $index Döngüdeki mevcut indis (0, 1, 2...)
 * @param int $page Mevcut sayfa numarası
 * @param int $limit Sayfa başına kayıt sınırı
 * @return int
 */
    public static function rowNumber($index, $page, $limit) {
        $limitstart = ($page - 1) * $limit;
        return $index + 1 + $limitstart;
    }
}