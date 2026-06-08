<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudioTone — Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-white d-flex flex-column min-vh-100">

<div class="container d-flex justify-content-center align-items-center flex-grow-1" style="min-height:90vh">
    <div class="card bg-white border border-secondary shadow" style="width:380px">
        <div class="card-body p-4">
            <h4 class="card-title text-center mb-4 text-dark">🎹 StudioTone</h4>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-danger">E-mail ou senha inválidos.</div>
            <?php endif; ?>

            <form action="logar.php" method="post">
                <div class="mb-3">
                    <label class="form-label text-secondary">E-mail</label>
                    <input type="email" name="email" class="form-control bg-white text-dark border-secondary" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-secondary">Senha</label>
                    <input type="password" name="senha" class="form-control bg-white text-dark border-secondary" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Entrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="py-3 bg-white border-top border-secondary">
    <div class="container text-center">
        <span class="text-secondary" style="font-size:0.85rem;">
        StudioTone &copy; <?php echo date('Y'); ?> — Todos os direitos reservados
        </span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
