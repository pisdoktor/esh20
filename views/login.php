<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESH v2.0 | Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; }
        .login-card { width: 400px; border: none; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; }
        .login-header { background: #0d6efd; color: white; padding: 40px 20px; text-align: center; }
        .login-header i { font-size: 3rem; margin-bottom: 10px; }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #eee; }
        .btn-login { border-radius: 10px; padding: 12px; font-weight: 600; letter-spacing: 0.5px; transition: 0.3s; }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="login-header">
            <i class="fa-solid fa-house-medical-flag"></i>
            <h4 class="mb-0 fw-bold">ESH PANEL</h4>
            <small class="opacity-75">Evde Sağlık Hizmetleri v2.0</small>
        </div>
        <div class="card-body p-4">
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger d-flex align-items-center small">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=Auth&action=doLogin" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Kullanıcı Adı</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="username" class="form-control border-start-0 shadow-none" placeholder="Kullanıcı adınız" required autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Şifre</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 shadow-none" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-login mt-3 shadow-sm">
                    Giriş Yap <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                </button>
            </form>
        </div>
        <div class="card-footer bg-white border-0 pb-4 text-center">
            <small class="text-muted">Yardım için sistem yöneticisine başvurun.</small>
        </div>
    </div>
</body>
</html>