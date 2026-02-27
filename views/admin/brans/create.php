<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-success"><i class="fas fa-plus-circle me-2"></i>Yeni Branş Tanımla</h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=Brans&action=store" method="POST">
                        <div class="mb-3">
                            <label for="bransadi" class="form-label fw-bold">Branş Adı</label>
                            <input type="text" class="form-control" id="bransadi" name="bransadi" placeholder="Örn: Nöroloji" required autofocus>
                            <div class="form-text">Hastanelerdeki resmi tıbbi birim adını yazınız.</div>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-3">
                            <a href="index.php?controller=Brans&action=index" class="btn btn-light border">Vazgeç</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>