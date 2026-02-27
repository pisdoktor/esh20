<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                    <a href="index.php?controller=User&action=index" class="btn btn-sm btn-light me-3">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <h5 class="mb-0 fw-bold text-dark">Profil Bilgilerimi Düzenle</h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="index.php?controller=User&action=update" method="POST">
                        <input type="hidden" name="id" value="<?= $user->id ?>">

                        <div class="row g-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-0"><i class="fa-solid fa-address-card me-2"></i>Genel Bilgiler</h6>
                                <hr class="mt-2 mb-0 opacity-10">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Ad Soyad</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user->name) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">E-Posta Adresi</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user->email) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">TC Kimlik No</label>
                                <input type="text" name="tckimlikno" class="form-control" value="<?= htmlspecialchars($user->tckimlikno) ?>" maxlength="11">
                                <div class="form-text small">Kimlik doğrulama işlemleri için gereklidir.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Kullanıcı Adı</label>
                                <input type="text" name="username" class="form-control bg-light" value="<?= htmlspecialchars($user->username) ?>" readonly title="Kullanıcı adı değiştirilemez">
                                <div class="form-text small text-muted">Kullanıcı adınızı değiştiremezsiniz.</div>
                            </div>

                            <div class="col-12 mt-5">
                                <h6 class="text-danger fw-bold mb-0"><i class="fa-solid fa-shield-halved me-2"></i>Güvenlik Ayarları</h6>
                                <hr class="mt-2 mb-0 opacity-10">
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info py-2 small border-0 mb-0">
                                    <i class="fa-solid fa-circle-info me-1"></i> Şifrenizi değiştirmek istemiyorsanız aşağıdaki alanları boş bırakın.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Yeni Şifre</label>
                                <input type="password" name="new_password" class="form-control" placeholder="******">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Yeni Şifre (Tekrar)</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="******">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                            <a href="index.php?controller=User&action=index" class="btn btn-light px-4">İptal</a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold">
                                Değişiklikleri Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>