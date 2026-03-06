<div class="container-fluid mt-4">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
            <h5 class="m-0 fw-bold text-primary"><i class="fas fa-hand-holding-medical me-2"></i>Pansuman Planlama</h5>
        </div>
        <form method="GET" action="index.php" class="row g-2 mb-4 p-3 bg-light rounded shadow-sm">
    <input type="hidden" name="controller" value="Pansuman">
    <input type="hidden" name="action" value="index">
    
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" name="search" class="form-control border-start-0" 
                   placeholder="Hasta Adı, Soyadı veya TC Kimlik No ile ara..." 
                   value="<?= htmlspecialchars($search ?? '') ?>">
        </div>
    </div>
    
    <div class="col-md-3">
        <select name="filter_day" class="form-select border-primary-subtle">
            <option value="">-- Tüm Günler --</option>
            <?php 
            foreach($gunler as $val => $label): 
                $selected = (isset($_GET['filter_day']) && $_GET['filter_day'] == $val) ? 'selected' : '';
            ?>
                <option value="<?= $val ?>" <?= $selected ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm">
            <i class="fas fa-filter me-1"></i> Sorgula
        </button>
        <a href="index.php?controller=Pansuman&action=index" class="btn btn-outline-secondary shadow-sm" title="Temizle">
            <i class="fas fa-sync-alt"></i>
        </a>
    </div>
</form>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th style="width: 20%;">Hasta Bilgisi</th>
                            <th style="width: 50%;">Uygulama Günleri (Seçmek için tıklayın)</th>
                            <th style="width: 20%;">Zaman</th>
                            <th style="width: 10%;" class="text-end">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Günler ve Zamanlar tanımları Controller'dan gelmiyorsa burada tanımlıyoruz
                        $gunler = [1=>'Pzt', 2=>'Sal', 3=>'Çar', 4=>'Per', 5=>'Cum', 6=>'Cmt', 7=>'Paz'];
                        
                        foreach ($rows as $row): 
                            $hastaGunleri = !empty($row->pgunleri) ? explode(',', $row->pgunleri) : [];
                            $hastaGunleri = array_map('trim', $hastaGunleri);
                        ?>
                        <form action="index.php?controller=Pansuman&action=saveDays" method="post">
                            <input type="hidden" name="id" value="<?= $row->id ?>">
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row->isim.' '.$row->soyisim) ?></div>
                                    <small class="text-muted"><?= $row->tckimlik ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm w-100 pansuman-group" role="group">
                                        <?php foreach($gunler as $val => $label): 
                                            $isActive = in_array((string)$val, $hastaGunleri);
                                            $btnId = "btn-{$row->id}-{$val}";
                                        ?>
                                            <input type="checkbox" 
                                                   class="btn-check pansuman-check" 
                                                   name="pgunleri[]" 
                                                   value="<?= $val ?>" 
                                                   id="<?= $btnId ?>" 
                                                   <?= $isActive ? 'checked' : '' ?> 
                                                   onclick="toggleBtnColor(this)"
                                                   autocomplete="off">
                                            
                                            <label class="btn <?= $isActive ? 'btn-primary' : 'btn-outline-secondary' ?> pansuman-label" 
                                                   for="<?= $btnId ?>" 
                                                   style="min-width: 42px; transition: all 0.2s;">
                                                <?= $label ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td>
                                    <select name="pzaman" class="form-select form-select-sm border-primary-subtle">
                                        <option value="0" <?= (string)$row->pzaman === '0' ? 'selected' : '' ?>>Sabah</option>
                                        <option value="1" <?= (string)$row->pzaman === '1' ? 'selected' : '' ?>>Öğle</option>
                                        <option value="2" <?= (string)$row->pzaman === '2' ? 'selected' : '' ?>>Akşam</option>
                                    </select>
                                </td>
                                <td class="text-end">
                                    <button type="submit" class="btn btn-sm btn-success px-3 shadow-sm w-100">
                                        <i class="fas fa-save me-1"></i> Kaydet
                                    </button>
                                </td>
                            </tr>
                        </form>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="card-footer bg-white border-0 py-3">
    <div class="d-flex justify-content-between align-items-center">
        <div class="small text-muted">
            Toplam <strong><?= $total ?></strong> pansuman hastası listeleniyor.
        </div>
        
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Sayfalama">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="index.php?controller=Pansuman&action=index&page=<?= $page-1 ?>&search=<?= $search ?>">Geri</a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="index.php?controller=Pansuman&action=index&page=<?= $i ?>&search=<?= $search ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="index.php?controller=Pansuman&action=index&page=<?= $page+1 ?>&search=<?= $search ?>">İleri</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>
<script>
/**
 * Checkbox tıklandığında butonun rengini anlık değiştiren fonksiyon
 */
function toggleBtnColor(element) {
    const label = document.querySelector(`label[for="${element.id}"]`);
    if (element.checked) {
        label.classList.remove('btn-outline-secondary');
        label.classList.add('btn-primary');
        label.style.fontWeight = 'bold';
    } else {
        label.classList.remove('btn-primary');
        label.classList.add('btn-outline-secondary');
        label.style.fontWeight = 'normal';
    }
}
</script>

<style>
    /* Daha belirgin seçim efektleri */
    .pansuman-label {
        border-color: #dee2e6 !important;
    }
    .btn-check:checked + .pansuman-label {
        background-color: #0d6efd !important;
        color: white !important;
        border-color: #0d6efd !important;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
    }
    .btn-outline-secondary:hover {
        background-color: #f8f9fa !important;
        color: #0d6efd !important;
    }
    /* Satır üzerine gelince hafif renk */
    tr:hover {
        background-color: rgba(13, 110, 253, 0.02);
    }
</style>