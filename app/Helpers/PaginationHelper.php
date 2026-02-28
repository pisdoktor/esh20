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
    /**
     * Bootstrap 5 uyumlu ve URL parametrelerini koruyan sayfalama render metodu
     */
    public static function render($total_items, $current_page, $limit, $base_url) {
        $total_pages = ceil($total_items / $limit);
        if ($total_pages <= 1) return "";

        // 1. Mevcut tüm GET parametrelerini al ve kopyala
        $params = $_GET;
        
        // 2. Sayfalama linklerinde değişecek olan parametreleri temizle
        // (Bu sayede linklerin üst üste binmesini önlüyoruz)
        unset($params['page']);
        $params['limit'] = $limit;

        // 3. Mevcut sıralama bilgilerini URL'de tutmaya devam et
        $params['orderby'] = $_GET['orderby'] ?? 'h.isim';
        $params['orderdir'] = $_GET['orderdir'] ?? 'ASC';

        // 4. Temel URL'yi oluştur (Sayfa numarası hariç kısım)
        // parse_url ile base_url'in içindeki path'i (index.php) alıyoruz
        $path = parse_url($base_url, PHP_URL_PATH) ?: 'index.php';
        $final_base_url = $path . '?' . http_build_query($params);

        $html = '<div class="d-flex align-items-center">';
        $html .= '<nav aria-label="Page navigation"><ul class="pagination pagination-sm mb-0 shadow-sm">';

        // İlk ve Geri
        $disabled = ($current_page <= 1) ? 'disabled' : '';
        $prev_page = $current_page - 1;
        $html .= "<li class='page-item {$disabled}'><a class='page-link' href='{$final_base_url}&page=1' title='İlk Sayfa'><i class='fa-solid fa-angles-left'></i></a></li>";
        $html .= "<li class='page-item {$disabled}'><a class='page-link' href='{$final_base_url}&page={$prev_page}' title='Geri'><i class='fa-solid fa-angle-left'></i></a></li>";

        // Sayfa Numaraları (Mevcut sayfanın 3 öncesi ve 3 sonrasını gösterir)
        $start = max(1, $current_page - 3);
        $end = min($total_pages, $current_page + 3);

        for ($i = $start; $i <= $end; $i++) {
            $active = ($current_page == $i) ? 'active' : '';
            $html .= "<li class='page-item {$active}'><a class='page-link' href='{$final_base_url}&page={$i}'>{$i}</a></li>";
        }

        // İleri ve Son
        $disabled = ($current_page >= $total_pages) ? 'disabled' : '';
        $next_page = $current_page + 1;
        $html .= "<li class='page-item {$disabled}'><a class='page-link' href='{$final_base_url}&page={$next_page}' title='İleri'><i class='fa-solid fa-angle-right'></i></a></li>";
        $html .= "<li class='page-item {$disabled}'><a class='page-link' href='{$final_base_url}&page={$total_pages}' title='Son Sayfa'><i class='fa-solid fa-angles-right'></i></a></li>";

        $html .= '</ul></nav>';
        
        // Hızlı Sayfaya Git (Jump to Page)
        if ($total_pages > 5) {
            $html .= self::jumpToPage($total_pages, $final_base_url);
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Sayfaya Git (Jump to Page) - Özellikle çok kayıtlı tablolarda hayat kurtarır
     */
    /**
     * Hızlı sayfa geçiş girişi
     */
    private static function jumpToPage($total_pages, $url) {
        return '
        <div class="input-group input-group-sm ms-3" style="width: 130px;">
            <input type="number" id="jump_page" class="form-control" placeholder="Sfy No" min="1" max="'.$total_pages.'">
            <button class="btn btn-outline-primary" type="button" onclick="const p = document.getElementById(\'jump_page\').value; if(p > 0 && p <= '.$total_pages.') location.href=\''.$url.'&page=\'+p;">Git</button>
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