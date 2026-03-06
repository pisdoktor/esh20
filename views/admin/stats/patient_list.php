<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light small">
                <tr>
                    <th>AD SOYAD</th>
                    <th>TC KİMLİK</th>
                    <th>BÖLGE</th>
                    <th>KAYIT TARİHİ</th>
                    <th>YAŞ / D.TARİHİ</th>
                    <th class="text-end">İŞLEM</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows as $row): ?>
                <tr>
                    <td>
                        <div class="dropdown">
                            <a class="fw-bold text-primary text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <?= $row->isim.' '.$row->soyisim ?>
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="index.php?controller=Patient&action=view&id=<?= $row->id ?>"><i class="fa fa-eye me-2"></i>Göster</a></li>
                                <li><a class="dropdown-item" href="index.php?controller=Patient&action=edit&id=<?= $row->id ?>"><i class="fa fa-edit me-2"></i>Düzenle</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="index.php?controller=Izlem&action=new&tc=<?= $row->tckimlik ?>"><i class="fa fa-plus me-2"></i>Yeni İzlem</a></li>
                            </ul>
                        </div>
                    </td>
                    <td class="small"><?= $row->tckimlik ?></td>
                    <td><?= $row->mahalle ?> <span class="badge bg-success-subtle text-success"><?= $row->ilce ?></span></td>
                    <td><?= $row->kayityili ?> / <?= $row->kayitay ?></td>
                    <td>
                        <?php 
                            $yas = date('Y') - date('Y', strtotime($row->dogumtarihi));
                            echo $yas . ' Yaş (' . date('d.m.Y', strtotime($row->dogumtarihi)) . ')';
                        ?>
                    </td>
                    <td class="text-end pe-3">
                         <a href="tel:<?= $row->tel ?>" class="btn btn-sm btn-light"><i class="fa fa-phone"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>