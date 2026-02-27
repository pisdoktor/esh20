<?php
    $secili_dizi = [];
    $kayitli_saatler = []; 
    $vardiya_ekip_sayilari = [0 => 2, 1 => 2, 2 => 2];

    if(!empty($mevcutlar)) {
        foreach($mevcutlar as $m) {
            $secili_dizi[$m->vardiya][$m->ekip_no] = explode(',', $m->user_ids);
            $kayitli_saatler[$m->vardiya] = $m->baslangic_saati;
            if($m->ekip_no > $vardiya_ekip_sayilari[$m->vardiya]) $vardiya_ekip_sayilari[$m->vardiya] = $m->ekip_no;
        }
    }

    $vardiyalar = [
        0 => ['label' => 'SABAH VARDİYASI', 'color' => '#f39c12', 'icon' => 'fa-sun', 'bg' => '#fef9f1', 'def_time' => '09:00'],
        1 => ['label' => 'ÖĞLE VARDİYASI', 'color' => '#3498db', 'icon' => 'fa-cloud-sun', 'bg' => '#f1f9fe', 'def_time' => '13:00'],
        2 => ['label' => 'AKŞAM VARDİYASI', 'color' => '#2c3e50', 'icon' => 'fa-moon', 'bg' => '#f4f6f7', 'def_time' => '16:00']
    ];

    $user_options = "";
    foreach($users as $user) { $user_options .= '<option value="'.$user->id.'">'.$user->name.'</option>'; }
?>
<style>
    .vardiya-panel { border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); background: #fff; }
    .vardiya-header { padding: 12px 15px; color: #fff; font-weight: bold; border-radius: 7px 7px 0 0; }
    .vardiya-body { padding: 15px; min-height: 250px; border-radius: 0 0 7px 7px; }
    .ekip-box { background: rgba(255,255,255,0.9); border: 1px solid rgba(0,0,0,0.1); padding: 12px; margin-bottom: 15px; border-radius: 6px; position: relative; }
    .remove-ekip { position: absolute; top: 8px; right: 10px; color: #d9534f; cursor: pointer; z-index: 10; font-size: 16px; }
    .chosen-container .chosen-results li.disabled-result { opacity: 0.3 !important; filter: grayscale(1); cursor: not-allowed !important; }
</style>

<div class="panel panel-primary">
    <div class="panel-heading"><h4><i class="fa-solid fa-arrows-down-to-people"></i> Personel Ekip Ataması</h4></div>
    <div class="container-fluid py-4">
        <form action="index.php?controller=Ekip&action=saveDaily" method="post">
            <div class="text-center mb-4">
                <div class="datepicker-container">
    <i class="fa fa-calendar-alt"></i> <strong>Mesai Tarihi:</strong> 
    <input type="text" name="tarih" id="planTarihi" value="<?= date('d.m.Y', strtotime($date)); ?>" readonly style="border:none; font-weight:bold; text-align:center; width:110px; cursor:pointer;">
</div>
            </div>

            <div class="row">
                <?php foreach($vardiyalar as $vKey => $vVal): 
                    $display_time = $kayitli_saatler[$vKey] ?? $vVal['def_time'];
                ?>
                <div class="col-md-4 vardiya-grubu" data-vardiya-id="<?= $vKey ?>">
                    <div class="vardiya-panel">
                        <div class="vardiya-header" style="background:<?= $vVal['color'] ?>;">
                            <i class="fa <?= $vVal['icon'] ?>"></i> <?= $vVal['label'] ?>
                        </div>
                        <div class="vardiya-body" id="vardiya-container-<?= $vKey ?>" style="background:<?= $vVal['bg'] ?>;">
                            <div class="form-group">
                                <label class="small fw-bold">BAŞLANGIÇ SAATİ</label>
                                <input type="time" name="saatler[<?= $vKey ?>]" class="form-control" value="<?= substr($display_time,0,5) ?>">
                            </div>
                            <hr>
                            <div class="ekip-listesi">
                                <?php for($eIdx = 1; $eIdx <= $vardiya_ekip_sayilari[$vKey]; $eIdx++): ?>
                                <div class="ekip-box">
                                    <?php if($eIdx > 1): ?><i class="fa fa-times-circle remove-ekip" onclick="silEkip(this, <?= $vKey ?>)"></i><?php endif; ?>
                                    <label class="small"><i class="fa fa-users"></i> <?= $eIdx ?>. Ekip</label>
                                    <select name="ekipler[<?= $vKey ?>][<?= $eIdx ?>][]" class="form-control chosen-select ekip-select" multiple>
                                        <?php foreach($users as $u): 
                                            $s = (isset($secili_dizi[$vKey][$eIdx]) && in_array($u->id, $secili_dizi[$vKey][$eIdx])) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $u->id ?>" <?= $s ?>><?= $u->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endfor; ?>
                            </div>
                            <button type="button" class="btn btn-default btn-xs btn-block border-dashed" style="color:<?= $vVal['color'] ?>;" onclick="ekipEkle(<?= $vKey ?>)">
                                <i class="fa fa-plus-circle"></i> YENİ EKİP EKLE
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success px-5 fw-bold"><i class="fa fa-save"></i> PLANI KAYDET</button>
            </div>
        </form>
    </div>
</div>

<script>
var userOptions = '<?= $user_options ?>';

function updateDisabledOptions(vardiyaId) {
    var vardiyaGrubu = jQuery('.vardiya-grubu[data-vardiya-id="' + vardiyaId + '"]');
    var allSelects = vardiyaGrubu.find('.ekip-select');
    var selectedValues = [];
    allSelects.each(function() {
        var vals = jQuery(this).val();
        if (vals) selectedValues = selectedValues.concat(vals);
    });
    allSelects.each(function() {
        var currentSelect = jQuery(this);
        var currentVals = currentSelect.val() || [];
        currentSelect.find('option').each(function() {
            var val = jQuery(this).val();
            if (selectedValues.includes(val) && !currentVals.includes(val)) {
                jQuery(this).attr('disabled', 'disabled');
            } else { jQuery(this).removeAttr('disabled'); }
        });
        currentSelect.trigger("chosen:updated");
    });
}

function ekipEkle(vKey) {
    var container = jQuery('#vardiya-container-' + vKey + ' .ekip-listesi');
    var nextNo = container.find('.ekip-box').length + 1;
    var html = '<div class="ekip-box"><i class="fa fa-times-circle remove-ekip" onclick="silEkip(this, '+vKey+')"></i>' +
               '<label class="small"><i class="fa fa-users"></i> ' + nextNo + '. Ekip</label>' +
               '<select name="ekipler['+vKey+']['+nextNo+'][]" class="form-control chosen-select ekip-select" multiple>' + userOptions + '</select></div>';
    container.append(html);
    container.find('.ekip-select').last().chosen({width: "100%"});
    updateDisabledOptions(vKey);
}

function silEkip(obj, vKey) { jQuery(obj).parent().remove(); updateDisabledOptions(vKey); }

jQuery(document).ready(function($) {
    // Datepicker'ı başlatırken ayarlarını net yapalım
    $('#planTarihi').datepicker({
        format: 'dd.mm.yyyy',
        autoclose: true,
        todayHighlight: true,
        language: 'tr' // Türkçe dil desteği varsa
    }).on('changeDate', function(e) {
        // e.date nesnesini manuel olarak YYYY-MM-DD formatına çeviriyoruz
        var secilenTarih = e.format('yyyy-mm-dd');
        
        // Mevcut Controller ve Action parametrelerini koruyarak yönlendir
        // Bu senin sistemindeki yönlendirme yapısıdır:
        window.location.href = 'index.php?controller=Ekip&action=edit&tarih=' + secilenTarih;
    });

    // Eğer datepicker kütüphanesinde sorun varsa, yedeğe alalım:
    // Inputa tıklandığında takvimin açılmasını garanti et
    $('#planTarihi').on('click', function() {
        $(this).datepicker('show');
    });
});
</script>