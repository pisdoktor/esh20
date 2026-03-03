<?php
namespace App\Helpers;

use stdClass;

/**
 * FormHelper - Form bileşenlerini standardize eden yardımcı sınıf.
 * Bootstrap 5, Chosen ve Datepicker standartlarına uygun çıktı üretir.
 */
class FormHelper {

    /**
     * Nitelik dizisini HTML formatına çevirir.
     */
    private static function parseAttributes(array $attr): string {
        $str = '';
        foreach ($attr as $key => $val) {
            if ($key === 'rows') continue;
            $str .= " " . htmlspecialchars($key) . "='" . htmlspecialchars((string)$val) . "'";
        }
        return $str;
    }

    /**
     * Veritabanı nesne listesini (Object List) key => value dizisine çevirir.
     */
    public static function makeOption(string $value, string $text = '', string $value_name = 'value', string $text_name = 'text'): stdClass {
        $obj = new stdClass();
        $obj->$value_name = $value;
        $obj->$text_name = trim($text) !== '' ? $text : $value;
        return $obj;
    }

    /**
     * Standart Input Alanı (Floating Label desteği eklendi)
     */
    public static function input($name, $label, $value = '', $type = 'text', $attr = []) {
        $attributes = self::parseAttributes($attr);
        $class = 'form-control ' . ($attr['class'] ?? '');
        return "
        <div class='form-floating mb-3'>
            <input type='{$type}' name='{$name}' id='{$name}' class='{$class}' value='{$value}' placeholder='{$label}' {$attributes}>
            <label for='{$name}' class='small fw-bold text-muted'>{$label}</label>
        </div>";
    }

    /**
     * Select (Açılır Menü) Alanı - Chosen Uyumluluğu eklendi
     */
    public static function selectList(array $arr, string $tag_name, string $tag_attribs, string $key='value', string $text='text', mixed $selected = null): string {
        // Chosen sınıfını otomatik ekle
        if (strpos($tag_attribs, 'class=') === false) {
            $tag_attribs .= ' class="form-select chosen-select"';
        } else {
            $tag_attribs = str_replace('class="', 'class="chosen-select ', $tag_attribs);
        }

        $html = "\n<select name=\"$tag_name\" id=\"$tag_name\" $tag_attribs>";
            
        foreach ($arr as $obj) {
            $k = (string)$obj->$key;
            $t = (string)$obj->$text;
            $extra = '';
            
            if (is_array($selected)) {
                if (in_array($k, array_map('strval', $selected), true)) {
                    $extra = ' selected="selected"';
                }
            } else {
                if ($selected !== null && $selected !== '' && (string)$k === (string)$selected) {
                    $extra = ' selected="selected"';
                } elseif (($selected === '' || $selected === null) && $k === '') {
                    $extra = ' selected="selected"';
                }
            }

            $html .= "\n\t<option value=\"" . htmlspecialchars($k) . "\"$extra>" . htmlspecialchars($t) . "</option>";
        }
        $html .= "\n</select>\n";

        return $html;
    }

    /**
     * Ay isimlerinden oluşan select listesi.
     */
    public static function monthSelectList(string $tag_name, string $tag_attribs, mixed $selected, bool $required): string {
        $arr = [];
        $arr[] = self::makeOption($required ? '' : '0', 'Bir Seçim Yapın');

        $months = [
            '01' => 'Ocak', '02' => 'Şubat', '03' => 'Mart', '04' => 'Nisan',
            '05' => 'Mayıs', '06' => 'Haziran', '07' => 'Temmuz', '08' => 'Ağustos',
            '09' => 'Eylül', '10' => 'Ekim', '11' => 'Kasım', '12' => 'Aralık'
        ];

        foreach ($months as $val => $name) {
            $arr[] = self::makeOption($val, $name);
        }

        return self::selectList($arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
    }

    /**
     * Evet/Hayır select listesi.
     */
    public static function yesnoSelectList(string $tag_name, string $tag_attribs, mixed $selected, string $yes = 'Evet', string $no = 'Hayır'): string {
        $arr = [
            self::makeOption('0', $no),
            self::makeOption('1', $yes),
        ];
        return self::selectList($arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
    }

    /**
     * Radio buton listesi.
     */
    public static function radioList(array $arr, string $tag_name, string $tag_attribs, string $key = 'value', string $text = 'text', mixed $selected = null): string {
        $html = "";
        foreach ($arr as $obj) {
            $k = (string)$obj->$key;
            $t = (string)$obj->$text;
            $id = $obj->id ?? $tag_name . $k;

            $extra = '';
            if (is_array($selected)) {
                $extra = in_array($k, $selected) ? ' checked="checked"' : '';
            } else {
                $extra = ((string)$k === (string)$selected ? ' checked="checked"' : '');
            }

            $html .= "\n\t<div class='form-check form-check-inline'>";
            $html .= "\n\t<input type=\"radio\" class=\"form-check-input\" name=\"$tag_name\" id=\"" . htmlspecialchars((string)$id) . "\" value=\"" . htmlspecialchars($k) . "\"$extra $tag_attribs />";
            $html .= "\n\t<label class=\"form-check-label small\" for=\"" . htmlspecialchars((string)$id) . "\">" . htmlspecialchars($t) . "</label>";
            $html .= "\n\t</div>";
        }
        return $html;
    }

    /**
     * Checkbox Listesi (Çoklu Seçim Grubu)
     */
    public static function checkboxList(array $arr, string $tag_name, string $tag_attribs, string $key = 'value', string $text = 'text', mixed $selected = null): string {
        $html = "";
        foreach ($arr as $obj) {
            $k = (string)$obj->$key;
            $t = (string)$obj->$text;
            $id = $obj->id ?? $tag_name . $k;

            $extra = '';
            if (is_array($selected)) {
                if (in_array($k, array_map('strval', $selected), true)) {
                    $extra = ' checked="checked"';
                }
            } else {
                $extra = ((string)$k === (string)$selected ? ' checked="checked"' : '');
            }

            $html .= "\n\t<div class='form-check form-check-inline'>";
            $html .= "\n\t<input type=\"checkbox\" class=\"form-check-input\" name=\"{$tag_name}[]\" id=\"" . htmlspecialchars((string)$id) . "\" value=\"" . htmlspecialchars($k) . "\"$extra $tag_attribs />";
            $html .= "\n\t<label class=\"form-check-label small\" for=\"" . htmlspecialchars((string)$id) . "\">" . htmlspecialchars($t) . "</label>";
            $html .= "\n\t</div>";
        }
        return $html;
    }

    /**
     * Gruplandırılmış (OptGroup) select listesi.
     */
    public static function selectOptGroup(array $arr, string $tag_name, string $tag_attribs, string $key = 'value', string $text = 'text', mixed $selected = null): string {
        $html = "\n<select class=\"form-control chosen-select\" name=\"$tag_name\" $tag_attribs>";
        $html .= "\n<option value=\"\">Bir Seçim Yapın</option>";

        $groups = [];
        foreach ($arr as $option) {
            $groups[$option->groupname][$option->id] = $option->name;
        }

        foreach ($groups as $label => $options) {
            $html .= "\n<optgroup label=\"" . htmlspecialchars((string)$label) . "\">";
            foreach ($options as $id => $name) {
                $extra = (string)$id === (string)$selected ? ' selected="selected"' : '';
                $html .= "\n\t<option value=\"" . htmlspecialchars((string)$id) . "\"$extra>" . htmlspecialchars((string)$name) . "</option>";
            }
            $html .= "\n</optgroup>\n";
        }

        $html .= "\n</select>\n";
        return $html;
    }

    /**
     * Tamsayı aralığından select listesi oluşturur.
     */
    public static function integerSelectList(int $start, int $end, int $inc, string $tag_name, string $tag_attribs, mixed $selected, bool $required, string $format = ""): string {
        $arr = [];
        $arr[] = self::makeOption($required ? '' : '0', 'Bir Seçim Yapın');

        for ($i = $start; $i <= $end; $i += $inc) {
            $fi = $format ? sprintf($format, $i) : (string)$i;
            $arr[] = self::makeOption($fi, $fi);
        }

        return self::selectList($arr, $tag_name, $tag_attribs, 'value', 'text', $selected);
    }

    /**
     * Textarea (Uzun Metin Alanı)
     */
    public static function textarea($name, $label, $value = '', $attr = []) {
        $attributes = self::parseAttributes($attr);
        $rows = $attr['rows'] ?? 3;
        return "
        <div class='form-group mb-3'>
            <label class='form-label small fw-bold text-muted' for='{$name}'>{$label}</label>
            <textarea name='{$name}' id='{$name}' class='form-control' rows='{$rows}' {$attributes}>{$value}</textarea>
        </div>";
    }

    /**
     * Bootstrap 5 Switch (Aç-Kapat Düğmesi)
     */
    public static function switch($name, $label, $checked = false, $value = '1') {
        $is_checked = $checked ? 'checked' : '';
        return "
        <div class='form-check form-switch mb-3 custom-switch'>
            <input class='form-check-input' type='checkbox' role='switch' name='{$name}' id='{$name}' value='{$value}' {$is_checked}>
            <label class='form-check-label small fw-bold text-muted' for='{$name}'>{$label}</label>
        </div>";
    }

    /**
     * İkonlu Input Grubu (Prepend/Append)
     */
    public static function inputGroup($name, $label, $value = '', $icon = 'fa-pencil', $type = 'text', $attr = []) {
        $attributes = self::parseAttributes($attr);
        return "
        <div class='form-group mb-3'>
            <label class='form-label small fw-bold text-muted' for='{$name}'>{$label}</label>
            <div class='input-group shadow-sm'>
                <span class='input-group-text bg-light'><i class='fa-solid {$icon} text-primary'></i></span>
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
     * İkon destekli ve Datepicker uyumlu tarih giriş alanı
     */
    public static function date($name, $label, $value = null, $attributes = []) {
        $displayValue = ($value && $value != '0000-00-00') ? date('d.m.Y', strtotime($value)) : '';
        
        $attrStr = self::parseAttributes($attributes);
        return "
        <div class='mb-3'>
            <label for='{$name}' class='form-label small fw-bold text-muted'>{$label}</label>
            <div class='input-group shadow-sm'>
                <span class='input-group-text bg-light text-primary border-end-0'>
                    <i class='fa-solid fa-calendar-days'></i>
                </span>
                <input type='text' name='{$name}' id='{$name}' class='form-control datepicker' 
                       value='{$displayValue}' placeholder='GG.AA.YYYY' autocomplete='off' maxlength='10' {$attrStr}>
            </div>
        </div>";
    }

    /**
     * UUID Oluşturucu
     */
    public static function generateUUID() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}