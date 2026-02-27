<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-success">
                        <i class="fa-solid fa-user-plus me-2"></i><?= $pageTitle ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=User&action=store" method="POST">
                        <input type="hidden" name="id" value="">

                        <div class="row g-4">
                            <div class="col-12">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Kişisel Bilgiler</h6>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ad Soyad</label>
                                <input type="text" name="name" class="form-control" placeholder="Örn: Ahmet Yılmaz" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">TC Kimlik No</label>
                                <input type="text" name="tckimlikno" class="form-control" maxlength="11" placeholder="11 Haneli">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">E-Posta Adresi</label>
                                <input type="email" name="email" class="form-control" placeholder="ahmet@sirket.com" required>
                            </div>

                            <div class="col-12 mt-5">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Hesap ve Yetki Ayarları</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kullanıcı Adı</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Giriş Şifresi</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body py-2">
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" name="isadmin" id="isadminSwitch">
                                            <label class="form-check-label fw-bold" for="isadminSwitch">Yönetici (Admin) Yetkisi</label>
                                        </div>
                                        <small class="text-muted">Açılırsa kullanıcı tüm sisteme erişebilir.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body py-2">
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" name="activated" id="activatedSwitch" checked>
                                            <label class="form-check-label fw-bold" for="activatedSwitch">Hesabı Aktifleştir</label>
                                        </div>
                                        <small class="text-muted">Kapatılırsa kullanıcı sisteme giriş yapamaz.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                            <a href="index.php?controller=User&action=list" class="btn btn-light px-4 border">
                                <i class="fa-solid fa-arrow-left me-1"></i> Listeye Dön
                            </a>
                            <button type="submit" class="btn btn-success px-5 fw-bold">
                                <i class="fa-solid fa-save me-1"></i> Kullanıcıyı Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>