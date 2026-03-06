<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0 fw-bold text-dark">
        <i class="fa-solid fa-route me-2 text-primary"></i>Bölge Planlama
    </h5>
    
    <form method="GET" action="index.php" class="d-flex gap-2">
        <input type="hidden" name="controller" value="Planning">
        <input type="hidden" name="action" value="index">
        
        <div class="input-group input-group-sm">
            <label class="input-group-text bg-light" for="ilceSelect">İlçe:</label>
            <select name="ilce" id="ilceSelect" class="form-select" onchange="this.form.submit()" style="width: 180px;">
                <option value="0">-- Tüm İlçeler --</option>
                <?php foreach($districts as $d): ?>
                    <option value="<?= $d->id ?>" <?= ($ilce_id == $d->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d->adi) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <?php if($ilce_id > 0): ?>
            <a href="index.php?controller=Planning&action=index" class="btn btn-sm btn-outline-danger" title="Filtreyi Temizle">
                <i class="fa-solid fa-xmark"></i>
            </a>
        <?php endif; ?>
    </form>
</div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small text-uppercase text-muted">
                        <tr>
                            <th class="ps-4">İlçe / Mahalle</th>
                            <th>Bölge</th>
                            <th>Ziyaret Günleri</th>
                            <th class="text-end pe-4">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rows as $row): 
                            $secili_gunler = !empty($row->gun) ? explode(',', $row->gun) : [];
                            $secili_gunler = array_map('trim', $secili_gunler);
                        ?>
                        <form action="index.php?controller=Planning&action=save" method="POST">
                            <input type="hidden" name="id" value="<?= $row->id ?>">
                            <input type="hidden" name="current_ilce" value="<?= $ilce_id ?>">
                            <input type="hidden" name="current_page" value="<?= $page ?>">
                            <tr>
                                <td class="ps-4">
                                    <div class="small text-muted"><?= $row->ilce_adi ?></div>
                                    <div class="fw-bold"><?= $row->adi ?></div>
                                </td>
                                <td>
                                    <input type="number" name="bolge" class="form-control form-control-sm shadow-sm" 
                                           value="<?= $row->bolge ?>" style="width: 70px;">
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm shadow-sm" role="group">
                                        <?php foreach($gunler as $val => $label): 
                                            $isChecked = in_array((string)$val, $secili_gunler);
                                            $uid = "p-{$row->id}-{$val}";
                                        ?>
                                            <input type="checkbox" class="btn-check" name="gun[]" value="<?= $val ?>" 
                                                   id="<?= $uid ?>" <?= $isChecked ? 'checked' : '' ?> 
                                                   onclick="toggleColor(this)" autocomplete="off">
                                            <label class="btn <?= $isChecked ? 'btn-primary' : 'btn-outline-secondary' ?> border" 
                                                   for="<?= $uid ?>" style="min-width: 40px; font-size: 11px;"><?= $label ?></label>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <button type="submit" class="btn btn-sm btn-success shadow-sm">
                                        <i class="fa-solid fa-save"></i>
                                    </button>
                                </td>
                            </tr>
                        </form>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">Toplam <strong><?= $total ?></strong> mahalle kayıtlı.</div>
                <?php if($totalPages > 1): ?>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="index.php?controller=Planning&action=index&page=<?= $page-1 ?>&ilce=<?= $ilce_id ?>">Geri</a>
                        </li>
                        <?php 
                        // Çok fazla sayfa varsa sadece belli bir aralığı gösterelim (Opsiyonel)
                        for($i=1; $i<=$totalPages; $i++): 
                        ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="index.php?controller=Planning&action=index&page=<?= $i ?>&ilce=<?= $ilce_id ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="index.php?controller=Planning&action=index&page=<?= $page+1 ?>&ilce=<?= $ilce_id ?>">İleri</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function toggleColor(el) {
    const label = document.querySelector(`label[for="${el.id}"]`);
    if (el.checked) {
        label.classList.replace('btn-outline-secondary', 'btn-primary');
    } else {
        label.classList.replace('btn-primary', 'btn-outline-secondary');
    }
}
</script>