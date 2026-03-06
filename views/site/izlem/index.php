<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tüm İzlem Kayıtları</h6>
            <a href="index.php?controller=Visit&action=create" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> Yeni İzlem Ekle
            </a>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3 mb-4">
                <input type="hidden" name="controller" value="Visit">
                <input type="hidden" name="action" value="index">
                
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="TC No veya İsim ile ara..." value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- Tüm Durumlar --</option>
                        <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Gerçekleşen (Yapıldı)</option>
                        <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Planlanan (Yapılmadı)</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrele</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover shadow-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Tarih</th>
                            <th>Hasta Bilgisi</th>
                            <th>TC Kimlik</th>
                            <th>Yapılan İşlem</th>
                            <th>Adres (İlçe/Mah)</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($visits)): ?>
                            <?php foreach ($visits as $v): ?>
                            <tr>
                                <td><?= date('d.m.Y', strtotime($v->izlemtarihi)) ?></td>
                                <td><strong><?= $v->isim . ' ' . $v->soyisim ?></strong></td>
                                <td><?= $v->hastatckimlik ?></td>
                                <td><?= $v->islemadi ?: 'Belirtilmemiş' ?></td>
                                <td><?= $v->ilce ?> / <?= $v->mahalle ?></td>
                                <td>
                                    <?php if ($v->yapildimi == 1): ?>
                                        <span class="badge bg-success">Yapıldı</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Yapılmadı</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?controller=Visit&action=edit&id=<?= $v->id ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <button onclick="if(confirm('Silmek istediğinize emin misiniz?')) window.location.href='index.php?controller=Visit&action=delete&id=<?= $v->id ?>'" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">Kayıt bulunamadı.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="index.php?controller=Visit&action=index&page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>