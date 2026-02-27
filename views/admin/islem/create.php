<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-success"><i class="fas fa-plus-circle me-2"></i>Yeni İşlem Tanımla</h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=Islem&action=store" method="POST">
                        <div class="mb-4">
                            <label for="islemadi" class="form-label fw-bold text-secondary">Uygulanan İşlem / Müdahale Adı</label>
                            <input type="text" class="form-control form-control-lg" id="islemadi" name="islemadi" 
                                   placeholder="Örn: Pansuman, Kateter Değişimi" required autofocus>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-4">
                            <a href="index.php?controller=Islem&action=index" class="btn btn-outline-secondary px-4">Geri Dön</a>
                            <button type="submit" class="btn btn-success px-5">Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>