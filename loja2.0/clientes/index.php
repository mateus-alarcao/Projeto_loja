<?php
    include '../conexao.php';
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION) || !$_SESSION['logado']) { header('Location:../index.php'); exit; }

    $consulta = $conexao->query("SELECT * FROM clientes");

    $cli = null;
    if (isset($_GET['id'])) {
        $s = $conexao->prepare("SELECT * FROM clientes WHERE id = :id");
        $s->bindParam(':id', $_GET['id']);
        $s->execute();
        $cli = $s->fetch(PDO::FETCH_OBJ);
    }
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-white text-dark d-flex flex-column min-vh-100">
<?php include '../navbar.php'; ?>
<div class="container flex-grow-1">
    <h4 class="mb-3">Clientes</h4>
    <div class="card bg-white border border-secondary mb-4">
        <div class="card-body">
            <form action="inserir.php" method="post">
                <input type="hidden" name="id" value="<?php echo $cli ? $cli->id : '' ?>">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="nome" class="form-control bg-white text-dark border-secondary" placeholder="Nome" value="<?php echo $cli ? $cli->nome : '' ?>" required>
                    </div>
                    <div class="col-md-3">
                        <input type="email" name="email" class="form-control bg-white text-dark border-secondary" placeholder="E-mail" value="<?php echo $cli ? $cli->email : '' ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="telefone" class="form-control bg-white text-dark border-secondary" placeholder="Telefone" value="<?php echo $cli ? $cli->telefone : '' ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="cpf" class="form-control bg-white text-dark border-secondary" placeholder="CPF (só números)" maxlength="11" value="<?php echo $cli ? $cli->cpf : '' ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-white table-bordered table-striped table-hover">
        <thead>
            <tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Telefone</th><th>CPF</th><th colspan="2">Ações</th></tr>
        </thead>
        <tbody>
        <?php while ($linha = $consulta->fetch(PDO::FETCH_OBJ)): ?>
            <tr>
                <td><?php echo $linha->id ?></td>
                <td><?php echo $linha->nome ?></td>
                <td><?php echo $linha->email ?></td>
                <td><?php echo $linha->telefone ?></td>
                <td><?php echo $linha->cpf ?></td>
                <td><a href="index.php?id=<?php echo $linha->id ?>" class="btn btn-sm btn-warning">Editar</a></td>
                <td><a href="excluir.php?id=<?php echo $linha->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir?')">Excluir</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
