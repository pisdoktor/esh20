<?php
namespace App\Helpers;

/**
 * FormHelper - Form bileşenlerini standardize eden yardımcı sınıf.
 * Bootstrap 5 standartlarına uygun çıktı üretir.
 */
class FormHelper {

    /**
     * Veritabanı nesne listesini (Object List) key => value dizisine çevirir.
     * Joomla'nın mosHTML::makeOption mantığına benzer.
     * * @param array $list Veritabanı sonuç dizisi
     * @param string $valueField 'value' kısmına gelecek özellik (Örn: 'id')
     * @param string $textField 'text' kısmına gelecek özellik (Örn: 'isim')
     * @param bool $addPleaseSelect Başa 'Seçiniz' ekler mi?
     */
    public static function makeOptions($list, $valueField, $textField, $addPleaseSelect = false) {
        $options = [];
        
        if ($addPleaseSelect) {
            $options[''] = 'Seçiniz...';
        }

        if (is_array($list)) {
            foreach ($list as $item) {
                // Nesne ise ->, dizi ise [] olarak eriş
                $val = is_object($item) ? $item->$valueField : $item[$valueField];
                $text = is_object($item) ? $item->$textField : $item[$textField];
                $options[$val] = $text;
            }
        }
        return $options;
    }

    /**
     * Standart Input Alanı (Text, Number, Email, Date vb.)
     */
    public static function input($name, $label, $value = '', $type = 'text', $attr = []) {
        $attributes = self::parseAttributes($attr);
        return "
        <div class='form-group mb-3'>
            <label class='form-label small fw-bold text-uppercase text-muted' for='{$name}'>{$label}</label>
            <input type='{$type}' name='{$name}' id='{$name}' value='{$value}' {$attributes}>
        </div>";
    }

    /**
     * Select (Açılır Menü) Alanı
     * $options parametresi direkt dizi gelebilir veya $mapping ile ham liste gönderilebilir.
     */
    public static function select($name, $label, $options, $selected = '', $attr = []) {
    $attributes = self::parseAttributes($attr);
    
    // HTML ID'si için name içindeki [] işaretlerini temizleyelim (hastaliklar[] -> hastaliklar)
    $cleanId = str_replace(['[]', '[', ']'], '', $name);
    
    $html = "<div class='form-group mb-3'>";
    if ($label) $html .= "<label class='form-label small fw-bold text-uppercase text-muted' for='{$cleanId}'>{$label}</label>";
    
    $html .= "<select name='{$name}' id='{$cleanId}' {$attributes}>";
    
    // Eğer multiple değilse "Seçiniz" opsiyonunu ekle
    if (!str_contains($attributes, 'multiple')) {
        $html .= "<option value=''>Seçiniz...</option>";
    }

    foreach ($options as $val => $text) {
        // Nesne kontrolü (Mevcut mantığın)
        if (is_object($text) || is_array($text)) {
            $displayValue = isset($text->adi) ? $text->adi : (isset($text->id) ? $text->id : 'Tanımsız Nesne');
        } else {
            $displayValue = $text;
        }

        // --- ÇOKLU SEÇİM (ARRAY) KONTROLÜ BURADA ---
        $is_selected = false;
        if (is_array($selected)) {
            // Eğer $selected bir diziyse, mevcut $val bu dizinin içinde mi bak
            $is_selected = in_array((string)$val, array_map('strval', $selected));
        } else {
            // Eğer tekil değerse eski mantıkla devam et
            $is_selected = ((string)$val === (string)$selected && $selected !== '');
        }

        $sel = $is_selected ? 'selected' : '';
        $html .= "<option value='{$val}' {$sel}>{$displayValue}</option>";
    }

    $html .= "</select></div>";
    return $html;
}

    /**
     * Radio Listesi (Tekli Seçim Grubu)
     */
    public static function radioList($name, $label, $options, $selected = '', $inline = true, $mapping = null) {
        if ($mapping) {
            $options = self::makeOptions($options, $mapping['value'], $mapping['text']);
        }

        $class = $inline ? 'form-check-inline' : '';
        $html = "<div class='form-group mb-3'>";
        if ($label) $html .= "<label class='form-label d-block small fw-bold text-uppercase text-muted'>{$label}</label>";
        
        foreach ($options as $val => $text) {
            $id = $name . '_' . $val;
            $is_checked = (string)$val === (string)$selected ? 'checked' : '';
            $html .= "
            <div class='form-check {$class}'>
                <input class='form-check-input' type='radio' name='{$name}' id='{$id}' value='{$val}' {$is_checked}>
                <label class='form-check-label' for='{$id}'>{$text}</label>
            </div>";
        }
        $html .= "</div>";
        return $html;
    }

    /**
     * Checkbox Listesi (Çoklu Seçim Grubu)
     */
    public static function checkboxList($name, $label, $options, $selectedValues = [], $inline = true, $mapping = null) {
        if ($mapping) {
            $options = self::makeOptions($options, $mapping['value'], $mapping['text']);
        }

        $class = $inline ? 'form-check-inline' : '';
        $arrayName = (substr($name, -2) !== '[]') ? $name . '[]' : $name;
        
        $html = "<div class='form-group mb-3'>";
        if ($label) $html .= "<label class='form-label d-block small fw-bold text-uppercase text-muted'>{$label}</label>";
        
        foreach ($options as $val => $text) {
            $id = str_replace(['[', ']'], '', $name) . '_' . $val;
            // Seçili değerleri stringe çevirerek karşılaştırıyoruz (0/1 karmaşası olmaması için)
            $is_checked = in_array((string)$val, array_map('strval', (array)$selectedValues)) ? 'checked' : '';
            $html .= "
            <div class='form-check {$class}'>
                <input class='form-check-input' type='checkbox' name='{$arrayName}' id='{$id}' value='{$val}' {$is_checked}>
                <label class='form-check-label' for='{$id}'>{$text}</label>
            </div>";
        }
        $html .= "</div>";
        return $html;
    }

    /**
     * Textarea (Uzun Metin Alanı)
     */
    public static function textarea($name, $label, $value = '', $attr = []) {
        $attributes = self::parseAttributes($attr);
        $rows = $attr['rows'] ?? 3;
        return "
        <div class='form-group mb-3'>
            <label class='form-label small fw-bold text-uppercase text-muted' for='{$name}'>{$label}</label>
            <textarea name='{$name}' id='{$name}' class='form-control' rows='{$rows}' {$attributes}>{$value}</textarea>
        </div>";
    }

    /**
     * Bootstrap 5 Switch (Aç-Kapat Düğmesi)
     */
    public static function switch($name, $label, $checked = false, $value = '1') {
        $is_checked = $checked ? 'checked' : '';
        return "
        <div class='form-check form-switch mb-3'>
            <input class='form-check-input' type='checkbox' role='switch' name='{$name}' id='{$name}' value='{$value}' {$is_checked}>
            <label class='form-check-label small fw-bold text-uppercase text-muted' for='{$name}'>{$label}</label>
        </div>";
    }

    /**
     * İkonlu Input Grubu (Prepend/Append)
     */
    public static function inputGroup($name, $label, $value = '', $icon = 'fa-pencil', $type = 'text', $attr = []) {
        $attributes = self::parseAttributes($attr);
        return "
        <div class='form-group mb-3'>
            <label class='form-label small fw-bold text-uppercase text-muted' for='{$name}'>{$label}</label>
            <div class='input-group'>
                <span class='input-group-text'><i class='fa-solid {$icon}'></i></span>
                <input type='{$type}' name='{$name}' id='{$name}' class='form-control' value='{$value}' {$attributes}>
            </div>
        </div>";
    }

    /**
     * Gizli Alan (Hidden Input)
     */
    public static function hidden($name, $value) {
        return "<input type='hidden' name='{$name}' id='{$name}' value='{$value}'>";
    }

    /**
     * Nitelik dizisini HTML formatına çevirir
     */
    private static function parseAttributes($attr) {
        $str = '';
        foreach ($attr as $key => $val) {
            if ($key === 'rows') continue; // rows textarea metodunda işleniyor
            $str .= " {$key}='{$val}'";
        }
        return $str;
    }
    
    /**
 * Tarih giriş alanı oluşturur (Datepicker destekli)
 * * @param string $name İnput adı
 * @param string $label Etiket metni
 * @param string|null $value Mevcut değer
 * @param array $attributes Ekstra HTML nitelikleri
 * @return string
 */
/**
 * İkon destekli ve Datepicker uyumlu tarih giriş alanı
 */
public static function date($name, $label, $value = null, $attributes = []) {
    // 1. Sınıfları Hazırla
    $defaultClasses = 'form-control datepicker';
    if (isset($attributes['class'])) {
        $attributes['class'] .= ' ' . $defaultClasses;
    } else {
        $attributes['class'] = $defaultClasses;
    }

    // 2. Varsayılan Ayarlar
    $attributes['autocomplete'] = 'off';
    $attributes['placeholder']  = $attributes['placeholder'] ?? 'GG.AA.YYYY';

    // 3. HTML Çıktısı (Bootstrap Input Group Yapısı)
    $html = '<div class="mb-3">';
    $html .= '  <label for="' . $name . '" form-label small fw-bold text-uppercase text-muted>';
    $html .=    $label; // Başlığa küçük bir ikon
    $html .= '  </label>';
    $html .= '  <div class="input-group">'; // Hafif bir gölge ekledik
    $html .= '      <span class="input-group-text bg-light text-primary border-end-0">';
    $html .= '          <i class="fa-solid fa-calendar-alt"></i>'; // Giriş yanındaki ana ikon
    $html .= '      </span>';
    $html .= '      <input type="text" name="' . $name . '" id="' . $name . '" value="' . htmlspecialchars($value ?? '') . '"';
    
    // Diğer tüm öznitelikleri (attributes) döngüyle ekle
    foreach ($attributes as $key => $val) {
        $html .= ' ' . $key . '="' . htmlspecialchars($val) . '"';
    }
    
    $html .= '      >';
    $html .= '  </div>';
    $html .= '</div>';

    return $html;
}

/**
* @desc esh_adrestablosuna yeni veri girişi yapaken id değerini oluşturmak için
*/
function generateUUID() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // versiyon 4 ayarı
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // varyant ayarı
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
}