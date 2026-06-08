<?php
    include '../conexao.php';
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION) || !$_SESSION['logado']) { header('Location:../index.php'); exit; }

    $where = "WHERE 1=1";
    $params = [];
    if (!empty($_GET['busca'])) {
        $where .= " AND (p.nome LIKE :busca OR p.descricao LIKE :busca)";
        $params[':busca'] = '%' . $_GET['busca'] . '%';
    }
    if (!empty($_GET['categoria_id'])) {
        $where .= " AND p.categoria_id = :categoria_id";
        $params[':categoria_id'] = $_GET['categoria_id'];
    }

    $sql = "SELECT p.*, c.nome AS categoria FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id $where";
    $stmt = $conexao->prepare($sql);
    $stmt->execute($params);

    $categorias = $conexao->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_OBJ);

    $prod = null;
    if (isset($_GET['id'])) {
        $s = $conexao->prepare("SELECT * FROM produtos WHERE id = :id");
        $s->bindParam(':id', $_GET['id']);
        $s->execute();
        $prod = $s->fetch(PDO::FETCH_OBJ);
    }
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-white text-dark d-flex flex-column min-vh-100">
<?php include '../navbar.php'; ?>
<div class="container flex-grow-1">
    <h4 class="mb-3">Produtos</h4>

    <div class="card bg-white border border-secondary mb-4">
        <div class="card-body">
            <form action="inserir.php" method="post">
                <input type="hidden" name="id" value="<?php echo $prod ? $prod->id : '' ?>">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="nome" class="form-control bg-white text-dark border-secondary" placeholder="Nome" value="<?php echo $prod ? $prod->nome : '' ?>" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="descricao" class="form-control bg-white text-dark border-secondary" placeholder="Descrição" value="<?php echo $prod ? $prod->descricao : '' ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="preco" class="form-control bg-white text-dark border-secondary" placeholder="Preço" value="<?php echo $prod ? $prod->preco : '' ?>" required>
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="estoque" class="form-control bg-white text-dark border-secondary" placeholder="Estoque" value="<?php echo $prod ? $prod->estoque : 0 ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="categoria_id" class="form-select bg-white text-dark border-secondary">
                            <option value="">Categoria</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?php echo $c->id ?>" <?php echo ($prod && $prod->categoria_id == $c->id) ? 'selected' : '' ?>>
                                    <?php echo $c->nome ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form method="get" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="busca" class="form-control bg-white text-dark border-secondary" placeholder="Buscar por nome ou descrição" value="<?php echo $_GET['busca'] ?? '' ?>">
        </div>
        <div class="col-md-4">
            <select name="categoria_id" class="form-select bg-white text-dark border-secondary">
                <option value="">Todas as categorias</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?php echo $c->id ?>" <?php echo (isset($_GET['categoria_id']) && $_GET['categoria_id'] == $c->id) ? 'selected' : '' ?>>
                        <?php echo $c->nome ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
        </div>
        <div class="col-md-1">
            <a href="index.php" class="btn btn-outline-secondary w-100">X</a>
        </div>
    </form>

    <table class="table table-white table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th><th>Nome</th><th>Categoria</th><th>Preço</th><th>Estoque</th><th colspan="2">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($linha = $stmt->fetch(PDO::FETCH_OBJ)): ?>
            <tr <?php echo $linha->estoque < 5 ? 'class="table-warning"' : '' ?>>
                <td><?php echo $linha->id ?></td>
                <td><?php echo $linha->nome ?></td>
                <td><?php echo $linha->categoria ?></td>
                <td>R$ <?php echo number_format($linha->preco, 2, ',', '.') ?></td>
                <td><?php echo $linha->estoque ?></td>
                <td><a href="index.php?id=<?php echo $linha->id ?>" class="btn btn-sm btn-warning">Editar</a></td>
                <td><a href="excluir.php?id=<?php echo $linha->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir?')">Excluir</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <small class="text-warning">⚠️ Linhas amarelas: estoque abaixo de 5 unidades.</small>
</div>
<?php include '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
