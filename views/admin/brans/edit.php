<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-edit me-2"></i>Branş Düzenle</h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=Brans&action=store" method="POST">
                        <input type="hidden" name="id" value="<?= $item->id ?>">

                        <div class="mb-3">
                            <label for="bransadi" class="form-label fw-bold">Branş Adı</label>
                            <input type="text" class="form-control" id="bransadi" name="bransadi" 
                                   value="<?= htmlspecialchars($item->bransadi) ?>" required>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-3">
                            <a href="index.php?controller=Brans&action=index" class="btn btn-light border">İptal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sync-alt me-1"></i>Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>