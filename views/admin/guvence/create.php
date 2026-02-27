<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-success"><i class="fas fa-plus-circle me-2"></i>Yeni Güvence Türü Tanımla</h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=Guvence&action=store" method="POST">
                        <div class="mb-4">
                            <label for="guvenceadi" class="form-label fw-bold text-secondary">Güvence Adı</label>
                            <input type="text" class="form-control form-control-lg" id="guvenceadi" name="guvenceadi" 
                                   placeholder="Örn: SGK (4A), Bağ-Kur, Özel Sigorta" required autofocus>
                            <div class="form-text small">Hastaların kayıtlarında seçilecek güvence adını giriniz.</div>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-4">
                            <a href="index.php?controller=Guvence&action=index" class="btn btn-outline-secondary px-4">Listeye Dön</a>
                            <button type="submit" class="btn btn-success px-5">
                                <i class="fas fa-save me-2"></i>Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>