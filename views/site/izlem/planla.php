<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white p-3">
            <h5 class="mb-0"><i class="fa-solid fa-calendar-plus me-2"></i>Yeni İzlem Planla</h5>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <strong>Hasta:</strong> <?= $patient->isim ?> <?= $patient->soyisim ?> (<?= $patient->tckimlik ?>)
            </div>

            <form action="index.php?controller=PlannedVisit&action=store" method="POST">
                <input type="hidden" name="hastatckimlik" value="<?= $patient->tckimlik ?>">
                <input type="hidden" name="plantarihi" value="<?= date('Y-m-d');?>"> 

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Planlanan Tarih ve Saat</label>
                        <input type="datetime" name="planlanantarih" class="form-control" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Öncelik Durumu</label>
                        <select name="oncelik" class="form-select">
                            <option value="1">Normal</option>
                            <option value="2">Orta (Öncelikli)</option>
                            <option value="3">Yüksek (Acil)</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Zaman Dilimi</label>
                        <select name="zaman" class="form-select">
                            <option value="1">Sabah</option>
                            <option value="2">Öğle</option>
                            <option value="3">Akşam</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small">Yapılacak İşlem</label>
                        <?= $list['islem'];?>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label fw-bold small">Planı Yapan</label>
                        <?= $list['personel'];?>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small">Ek Notlar</label>
                        <textarea name="aciklama" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="button" onclick="history.back()" class="btn btn-light px-4 me-2">Vazgeç</button>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">Planı Oluştur</button>
                </div>
            </form>
        </div>
    </div>
</div>