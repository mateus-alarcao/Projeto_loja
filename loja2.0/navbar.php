<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION) || $_SESSION['logado'] == false) {
        header('Location:../index.php');
        exit;
    }
?>
<style>
    .navbar-nav .nav-link {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        transition: background-color 0.2s ease, color 0.2s ease;
        color: #212529;
    }
    .navbar-nav .nav-link:hover {
        background-color: #e9ecef;
        color: #000;
    }
    .navbar-nav .nav-link.active {
        background-color: #dee2e6;
        font-weight: 500;
        color: #000;
    }
</style>
<nav class="navbar navbar-expand-lg bg-white border-bottom border-secondary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../produtos/index.php">StudioTone</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto gap-1">
                <li class="nav-item"><a class="nav-link" href="../produtos/index.php">Produtos</a></li>
                <li class="nav-item"><a class="nav-link" href="../categorias/index.php">Categorias</a></li>
                <li class="nav-item"><a class="nav-link" href="../clientes/index.php">Clientes</a></li>
                <li class="nav-item"><a class="nav-link" href="../vendas/index.php">Vendas</a></li>
                <li class="nav-item"><a class="nav-link" href="../relatorio/index.php">Relatório</a></li>
            </ul>
            <span class="navbar-text text-secondary me-3" style="font-size:0.9rem;">
                <?php echo $_SESSION['nome']; ?> <span class="badge bg-secondary"><?php echo $_SESSION['perfil']; ?></span>
            </span>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm">Sair</a>
        </div>
    </div>
</nav>
<script>
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        if (link.href === window.location.href) {
            link.classList.add('active');
        }
    });
</script>