<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fa fa-plus-circle me-2 text-success"></i>Yeni Ekip Planla</h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=Ekip&action=store" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tarih</label>
                            <input type="date" name="tarih" class="form-control" value="<?= $_GET['tarih'] ?? date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Vardiya</label>
                                <select name="vardiya" class="form-select" required>
                                    <option value="0">Sabah (09:00)</option>
                                    <option value="1">Öğle (13:00)</option>
                                    <option value="2">Akşam (16:00)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ekip No</label>
                                <select name="ekip_no" class="form-select" required>
                                    <option value="1">1. Ekip</option>
                                    <option value="2">2. Ekip</option>
                                    <option value="3">3. Ekip</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Personel Seçimi (En az 2)</label>
                            <select name="user_ids[]" class="form-select select2-multiple" multiple required style="height: 200px;">
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user->id ?>"><?= $user->name ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Ctrl tuşuna basılı tutarak birden fazla seçim yapabilirsiniz.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Başlangıç Saati</label>
                            <input type="time" name="baslangic_saati" class="form-control" value="09:00">
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="index.php?controller=Ekip&action=index" class="btn btn-light">İptal</a>
                            <button type="submit" class="btn btn-primary px-4">Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>