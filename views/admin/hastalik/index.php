<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary"><i class="fas fa-microscope me-2"></i>Hastalık ve Tanı Kütüphanesi</h5>
            <a href="index.php?controller=Hastalik&action=create" class="btn btn-success btn-sm">
                <i class="fas fa-plus me-1"></i>Yeni Tanı Ekle
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ICD Kodu</th>
                            <th>Hastalık / Tanı Adı</th>
                            <th>Kategori</th>
                            <th width="120" class="text-center">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $row): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?= $row->icd ?></span></td>
                            <td><strong><?= $row->hastalikadi ?></strong></td>
                            <td><span class="text-muted small"><?= $row->kategori_adi ?></span></td>
                            <td class="text-center">
                                <a href="index.php?controller=Hastalik&action=edit&id=<?= $row->id ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <a href="index.php?controller=Hastalik&action=delete&id=<?= $row->id ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Silinsin mi?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>