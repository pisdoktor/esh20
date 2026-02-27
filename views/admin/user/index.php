<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-users-gear me-2 text-primary"></i><?= $pageTitle ?>
        </h4>
        <a href="index.php?controller=User&action=create" class="btn btn-primary shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Yeni Personel Ekle
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Personel</th>
                            <th>Kullanıcı Adı / TC</th>
                            <th>E-Posta</th>
                            <th class="text-center">Yetki</th>
                            <th class="text-center">Durum</th>
                            <th class="text-end pe-4">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): foreach ($items as $item): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="<?= !empty($item->image) ? $item->image : 'assets/img/default-avatar.png' ?>" 
                                             class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($item->name) ?></div>
                                            <small class="text-muted">ID: #<?= $item->id ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold"><?= htmlspecialchars($item->username) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($item->tckimlikno ?: '-') ?></div>
                                </td>
                                <td><?= htmlspecialchars($item->email) ?></td>
                                <td class="text-center">
                                    <?php if ($item->isadmin): ?>
                                        <span class="badge bg-danger-soft text-danger border border-danger-subtle px-3">Yönetici</span>
                                    <?php else: ?>
                                        <span class="badge bg-info-soft text-info border border-info-subtle px-3">Personel</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?= \App\Helpers\BadgeHelper::activationStatus($item->activated) ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="index.php?controller=User&action=adminEdit&id=<?= $item->id ?>" 
                                           class="btn btn-sm btn-outline-secondary" title="Düzenle">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="if(confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')) window.location.href='index.php?controller=User&action=delete&id=<?= $item->id ?>'">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Kayıtlı kullanıcı bulunamadı.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Soft Badge Styles */
    .bg-danger-soft { background-color: #f8d7da66; }
    .bg-info-soft { background-color: #cff4fc66; }
</style>