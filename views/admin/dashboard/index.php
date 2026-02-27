<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 border-0 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Havuzdaki Toplam Rapor</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_reports'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-medical fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 border-0 border-start border-4 border-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sistemde Kayıtlı Hasta</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['registered_patients'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 border-0 border-start border-4 border-info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Yenilenen Raporlar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['renewed_reports'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sync-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 border-0 border-start border-4 border-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Aktif Personel</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_users'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-nurse fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Branşlara Göre Rapor Yoğunluğu</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Branş Adı</th>
                                    <th class="text-end">Rapor Sayısı</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($stats['brans_dist'] as $b): ?>
                                <tr>
                                    <td><?= htmlspecialchars($b->brans ?: 'Belirtilmemiş') ?></td>
                                    <td class="text-end fw-bold"><?= $b->count ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Hızlı Erişim</h6>
                </div>
                <div class="card-body text-center">
                    <a href="index.php?controller=Erapor&action=create" class="btn btn-outline-primary m-2 p-3" style="width: 140px;">
                        <i class="fas fa-plus d-block mb-2"></i> Rapor Ekle
                    </a>
                    <a href="index.php?controller=User&action=list" class="btn btn-outline-secondary m-2 p-3" style="width: 140px;">
                        <i class="fas fa-users d-block mb-2"></i> Kullanıcılar
                    </a>
                    <a href="index.php?controller=Brans&action=index" class="btn btn-outline-info m-2 p-3" style="width: 140px;">
                        <i class="fas fa-tags d-block mb-2"></i> Branşlar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>