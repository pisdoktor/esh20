<div class="container-fluid mt-4">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold text-danger"><i class="fas fa-calendar-alt me-2"></i>Planlanmış İzlem Listesi</h5>
            <div class="btn-group">
                <a href="index.php?controller=PlannedVisit&action=create" class="btn btn-primary btn-sm">
                    <i class="fas fa-calendar-plus me-1"></i> Yeni Plan Oluştur
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-2 mb-4 p-3 bg-light rounded">
                <input type="hidden" name="controller" value="PlannedVisit">
                <input type="hidden" name="action" value="index">
                
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Hasta adı veya TC Kimlik..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- Tüm Planlar --</option>
                        <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Bekleyen Planlar</option>
                        <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Tamamlanmış Planlar</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-danger w-100">Sorgula</button>
                </div>
                <div class="col-md-2 text-end">
                     <a href="index.php?controller=PlannedVisit&action=pindex" class="btn btn-outline-secondary w-100">Temizle</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">Plan Tarihi</th>
                            <th>Hasta / Telefon</th>
                            <th>Bölge</th>
                            <th>Açıklama / Not</th>
                            <th class="text-center">Durum</th>
                            <th class="text-end pe-3">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($plans)): ?>
                            <?php foreach ($plans as $p): 
                                $bugun = date('Y-m-d');
                                $gecikme = ($p->durum == 0 && $p->planlanan_tarih < $bugun) ? 'table-danger' : '';
                            ?>
                            <tr class="<?= $gecikme ?>">
                                <td class="fw-bold">
                                    <i class="far fa-clock me-1 text-muted"></i>
                                    <?= date('d.m.Y', strtotime($p->planlanan_tarih)) ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= $p->isim . ' ' . $p->soyisim ?></div>
                                    <small class="text-muted"><i class="fas fa-phone-alt me-1"></i><?= $p->telefon ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info fw-normal"><?= $p->ilce ?></span>
                                    <div class="small text-muted mt-1"><?= $p->mahalle ?></div>
                                </td>
                                <td class="small text-truncate" style="max-width: 200px;">
                                    <?= $p->notlar ?: '<span class="text-muted italic">Not yok</span>' ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($p->durum == 1): ?>
                                        <span class="badge rounded-pill bg-success px-3">Tamamlandı</span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-warning text-dark px-3">Bekliyor</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <?php if ($p->durum == 0): ?>
                                        <a href="index.php?controller=Visit&action=create&plan_id=<?= $p->id ?>" class="btn btn-sm btn-success" title="Ziyareti Gerçekleştir">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="index.php?controller=PlannedVisit&action=edit&id=<?= $p->id ?>" class="btn btn-sm btn-outline-primary shadow-sm mx-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="if(confirm('Planı silmek istediğinize emin misiniz?')) window.location.href='index.php?controller=PlannedVisit&action=delete&id=<?= $p->id ?>'" class="btn btn-sm btn-outline-danger shadow-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">Kriterlere uygun planlanmış izlem bulunamadı.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="d-flex justify-content-center mt-4">
                <nav>
                    <ul class="pagination pagination-sm">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="index.php?controller=PlannedVisit&action=pindex&page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>