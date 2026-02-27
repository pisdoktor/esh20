<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-success"><i class="fas fa-plus-circle me-2"></i>Yeni Tanı/Hastalık Ekle</h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=Hastalik&action=store" method="POST">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-secondary">ICD-10 Kodu</label>
                                <input type="text" name="icd" class="form-control form-control-lg" placeholder="Örn: I10" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold text-secondary">Hastalık / Tanı Adı</label>
                                <input type="text" name="hastalikadi" class="form-control form-control-lg" placeholder="Hastalığın tam adını yazınız" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Hastalık Kategorisi</label>
                            <select name="cat" class="form-select" required>
                                <option value="">Kategori Seçiniz...</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat->id ?>"><?= $cat->name ?> (<?= $cat->icd_range ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-muted small">Tanı koduna uygun kategoriyi seçtiğinizden emin olun.</div>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-4">
                            <a href="index.php?controller=Hastalik&action=index" class="btn btn-outline-secondary px-4">Vazgeç</a>
                            <button type="submit" class="btn btn-success px-5">
                                <i class="fas fa-save me-2"></i>Kütüphaneye Ekle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>