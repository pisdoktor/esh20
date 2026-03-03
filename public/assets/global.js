// 1. Toastr Ayarları
toastr.options = {
    "escapeHtml": false,
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "3000"
};

// 2. Datepicker Global İkon Değişimleri
// Not: Bootstrap-datepicker sürümüne göre "templates" kullanımı daha garantidir.
const datepickerTemplates = {
    leftArrow: '<i class="fa-solid fa-chevron-left"></i>',
    rightArrow: '<i class="fa-solid fa-chevron-right"></i>'
};

$(document).ready(function() {

    // --- Tarih Maskeleme Fonksiyonu ---
    $('.datepicker').on('input', function(e) {
        // Sadece rakamları al
        let input = e.target.value.replace(/\D/g, ''); 
        let value = '';

        if (input.length > 0) {
            // Gün
            value += input.substring(0, 2);
            if (input.length > 2) {
                // Ay
                value += '.' + input.substring(2, 4);
            }
            if (input.length > 4) {
                // Yıl
                value += '.' + input.substring(4, 8);
            }
        }
        
        // Kullanıcı silme yaparken noktayı silmekte zorlanmaması için kontrol
        e.target.value = value.substring(0, 10); 
    });

    // --- Datepicker Başlatma ---
    $('.datepicker').datepicker({
        format: 'dd.mm.yyyy',
        language: 'tr',
        autoclose: true,
        todayHighlight: true,
        forceParse: false,
        templates: datepickerTemplates // İkonları buradan bağladık
    }).on('changeDate', function(e) {
        // Tarih seçildiğinde maskeleme ile çakışmaması için tetikleme
        $(this).datepicker('hide');
    });

});

$(document).ready(function() {
    // Tüm Tooltip'leri aktifleştir
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});