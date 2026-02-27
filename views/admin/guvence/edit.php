<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-edit me-2"></i>Güvence Bilgisini Güncelle</h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=Guvence&action=store" method="POST">
                        
                        <input type="hidden" name="id" value="<?= $item->id ?>">

                        <div class="mb-4">
                            <label for="guvenceadi" class="form-label fw-bold text-secondary">Güvence Adı</label>
                            <input type="text" class="form-control form-control-lg" id="guvenceadi" name="guvenceadi" 
                                   value="<?= htmlspecialchars($item->guvenceadi) ?>" required>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-4">
                            <a href="index.php?controller=Guvence&action=index" class="btn btn-light border px-4">İptal</a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-sync-alt me-2"></i>Değişiklikleri Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>