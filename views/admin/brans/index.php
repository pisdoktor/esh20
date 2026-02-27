<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-hospital-user me-2"></i>Tıbbi Branş Tanımları</h5>
                    <a href="index.php?controller=Brans&action=create" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i>Yeni Branş Ekle
                    </a>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Branş Adı</th>
                                    <th width="150" class="text-center">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($items)): foreach($items as $row): ?>
                                <tr>
                                    <td><?= $row->id ?></td>
                                    <td><strong><?= $row->bransadi ?></strong></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?controller=Brans&action=edit&id=<?= $row->id ?>" class="btn btn-outline-primary" title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="index.php?controller=Brans&action=delete&id=<?= $row->id ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Bu branşı silmek istediğinize emin misiniz?')" 
                                               title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Kayıtlı branş bulunamadı.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>