<div class="container py-4 text-center">
    <?php if (isset($_SESSION['temp_image'])): ?>
        <h4>Resmi Kırp</h4>
        <div class="mb-4">
            <img src="<?= $_SESSION['temp_image'] ?>" id="cropbox" style="max-width: 100%;">
        </div>
        
        <form action="index.php?controller=User&action=cropsave" method="post">
            <input type="hidden" id="x" name="x" />
            <input type="hidden" id="y" name="y" />
            <input type="hidden" id="w" name="w" />
            <input type="hidden" id="h" name="h" />
            <button type="submit" class="btn btn-success">Seçimi Kaydet</button>
            <a href="index.php?controller=User&action=index" class="btn btn-secondary">İptal</a>
        </form>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/0.9.15/css/jquery.Jcrop.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/0.9.15/js/jquery.Jcrop.min.js"></script>
        
        <script>
        $(function(){
            $('#cropbox').Jcrop({
                aspectRatio: 1, // Kare seçim
                onSelect: updateCoords,
                setSelect: [0, 0, 300, 300]
            });
        });

        function updateCoords(c) {
            $('#x').val(c.x);
            $('#y').val(c.y);
            $('#w').val(c.w);
            $('#h').val(c.h);
        };
        </script>

    <?php else: ?>
        <div class="card p-5 shadow-sm mx-auto" style="max-width: 500px;">
            <i class="fa-solid fa-image fa-3x text-muted mb-3"></i>
            <h5>Yeni Profil Resmi Yükle</h5>
            <form action="index.php?controller=User&action=upload" method="post" enctype="multipart/form-data">
                <input type="file" name="image" class="form-control mb-3" required>
                <button type="submit" class="btn btn-primary w-100">Resmi Yükle ve Devam Et</button>
            </form>
        </div>
    <?php endif; ?>
</div>