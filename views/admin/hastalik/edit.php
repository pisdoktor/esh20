<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-edit me-2"></i>Tanı Bilgilerini Güncelle</h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=Hastalik&action=store" method="POST">
                        
                        <input type="hidden" name="id" value="<?= $item->id ?>">

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-secondary">ICD-10 Kodu</label>
                                <input type="text" name="icd" class="form-control" value="<?= htmlspecialchars($item->icd) ?>" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold text-secondary">Hastalık / Tanı Adı</label>
                                <input type="text" name="hastalikadi" class="form-control" value="<?= htmlspecialchars($item->hastalikadi) ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Hastalık Kategorisi</label>
                            <select name="cat" class="form-select" required>
                                <option value="">Kategori Seçiniz...</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat->id ?>" <?= ($item->cat == $cat->id) ? 'selected' : '' ?>>
                                        <?= $cat->name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-4">
                            <a href="index.php?controller=Hastalik&action=index" class="btn btn-light border px-4">İptal</a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-sync-alt me-2"></i>Değişiklikleri Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>