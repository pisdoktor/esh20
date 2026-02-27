<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fa-solid fa-circle-user me-2"></i>Profil Bilgilerim
                    </h5>
                    <a href="index.php?controller=User&action=edit" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-user-pen me-1"></i> Düzenle
                    </a>
                </div>

                <div class="card-body text-center py-5">
                    <div class="position-relative d-inline-block mb-4">
                        <div class="profile-img-container">
                            <img src="<?= !empty($user->image) ? $user->image : 'assets/img/default-avatar.png' ?>" 
                                 class="rounded-circle border border-4 border-white shadow-sm" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                            
                            <a href="index.php?controller=User&action=image" 
                               class="btn btn-primary position-absolute bottom-0 end-0 rounded-circle shadow-sm btn-sm"
                               title="Resmi Güncelle">
                                <i class="fa-solid fa-camera"></i>
                            </a>
                        </div>
                    </div>

                    <h3 class="fw-bold mb-1"><?= htmlspecialchars($user->name) ?></h3>
                    <p class="text-muted mb-4 small">@<?= htmlspecialchars($user->username) ?></p>

                    <div class="list-group list-group-flush text-start border rounded-3 mx-lg-4">
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-secondary small"><i class="fa-solid fa-id-card me-2"></i>TC Kimlik No</span>
                            <span class="fw-semibold text-dark"><?= !empty($user->tckimlikno) ? htmlspecialchars($user->tckimlikno) : '-' ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-secondary small"><i class="fa-solid fa-envelope me-2"></i>E-Posta</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($user->email) ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-secondary small"><i class="fa-solid fa-calendar-day me-2"></i>Kayıt Tarihi</span>
                            <span class="text-dark small"><?= date('d.m.Y', strtotime($user->registerDate)) ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-secondary small"><i class="fa-solid fa-clock-rotate-left me-2"></i>Son Giriş</span>
                            <span class="text-muted small"><?= $user->lastvisit ? date('d.m.Y H:i', strtotime($user->lastvisit)) : 'İlk Giriş' ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-light border-0 py-3 text-center">
                    <small class="text-muted italic">Hesap bilgileriniz güvenli bir şekilde saklanmaktadır.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-img-container {
        position: relative;
        transition: transform 0.3s ease;
    }
    .profile-img-container:hover {
        transform: scale(1.05);
    }
    .list-group-item {
        border-color: rgba(0,0,0,0.05);
    }
</style>