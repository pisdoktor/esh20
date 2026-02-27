<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fa-solid fa-user-gear me-2"></i>Personel Düzenle
                    </h5>
                    <span class="badge bg-light text-dark border">ID: #<?= $user->id ?></span>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=User&action=store" method="POST">
                        <input type="hidden" name="id" value="<?= $user->id ?>">

                        <div class="row g-4">
                            <div class="col-12">
                                <h6 class="text-muted text-uppercase small fw-bold mb-0 border-bottom pb-2">Kişisel Bilgiler</h6>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Ad Soyad</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user->name) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">TC Kimlik No</label>
                                <input type="text" name="tckimlikno" class="form-control" maxlength="11" value="<?= htmlspecialchars($user->tckimlikno) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">E-Posta Adresi</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user->email) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Kullanıcı Adı</label>
                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user->username) ?>" required>
                            </div>

                            <div class="col-12 mt-5">
                                <h6 class="text-muted text-uppercase small fw-bold mb-0 border-bottom pb-2">Hesap Yönetimi ve Yetkiler</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-danger">Şifreyi Güncelle</label>
                                <input type="password" name="password" class="form-control" placeholder="Değiştirmek istemiyorsanız boş bırakın">
                                <div class="form-text text-muted small">Şifre alanı boş bırakılırsa mevcut şifre korunur.</div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Yönetici Yetkisi</label>
                                <div class="form-check form-switch p-2 ps-5 border rounded bg-light">
                                    <input class="form-check-input" type="checkbox" name="isadmin" id="isadminSwitch" <?= $user->isadmin ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold" for="isadminSwitch">Admin</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Hesap Durumu</label>
                                <div class="form-check form-switch p-2 ps-5 border rounded bg-light">
                                    <input class="form-check-input" type="checkbox" name="activated" id="activatedSwitch" <?= $user->activated ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold text-success" for="activatedSwitch">Aktif</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                            <a href="index.php?controller=User&action=list" class="btn btn-light px-4 border">
                                <i class="fa-solid fa-arrow-left me-1"></i> Listeye Dön
                            </a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold">
                                <i class="fa-solid fa-save me-1"></i> Değişiklikleri Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>