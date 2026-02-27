<div class="container mt-4">
    <div class="card shadow-sm border-0 col-md-8 mx-auto">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0"><i class="fas fa-file-medical me-2"></i>Yeni Rapor Kaydı Girişi</h5>
        </div>
        <div class="card-body p-4">
            <form action="index.php?controller=Erapor&action=store" method="POST">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-danger">Hasta T.C. Kimlik No *</label>
                        <input type="text" name="hastatckimlik" class="form-control" maxlength="11" required placeholder="11 haneli TC No">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Cep Telefonu</label>
                        <input type="tel" name="ceptel1" class="form-control" placeholder="05XX XXX XX XX">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Adı</label>
                        <input type="text" name="isim" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Soyadı</label>
                        <input type="text" name="soyisim" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Sistemde Kayıtlı mı?</label>
                        <select name="kayitlimi" class="form-select">
                            <option value="0">Hayır (Yeni Kayıt)</option>
                            <option value="1">Evet (Mevcut Hasta)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Rapor Yenilendi mi?</label>
                        <select name="yenilendimi" class="form-select">
                            <option value="0">Hayır (İlk Rapor)</option>
                            <option value="1">Evet (Yenileme)</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Rapor Branşı / Türü</label>
                        <select name="brans" class="form-select" required>
                            <option value="">Seçiniz...</option>
                            <?php foreach($branslar as $b): ?>
                                <option value="<?= htmlspecialchars($b->bransadi) ?>"><?= htmlspecialchars($b->bransadi) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Rapor Tarihi</label>
                        <input type="date" name="basvurutarihi" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Notlar / Neden</label>
                    <textarea name="neden" class="form-control" rows="3" placeholder="Notlarınızı yazın..."></textarea>
                </div>

                <div class="d-flex justify-content-between border-top pt-3">
                    <a href="index.php?controller=Erapor&action=index" class="btn btn-outline-secondary px-4">Vazgeç</a>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-2"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>