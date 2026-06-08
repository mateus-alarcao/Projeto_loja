<?php
    include '../conexao.php';
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION) || !$_SESSION['logado']) { header('Location:../index.php'); exit; }

    $where = "WHERE 1=1";
    $params = [];
    if (!empty($_GET['data_inicio']) && !empty($_GET['data_fim'])) {
        $where .= " AND DATE(v.criado_em) BETWEEN :data_inicio AND :data_fim";
        $params[':data_inicio'] = $_GET['data_inicio'];
        $params[':data_fim']    = $_GET['data_fim'];
    }

    $sql = "SELECT v.*, c.nome AS cliente, u.nome AS vendedor
            FROM vendas v
            LEFT JOIN clientes c ON v.cliente_id = c.id
            LEFT JOIN usuarios u ON v.usuario_id = u.id
            $where ORDER BY v.criado_em DESC";
    $stmt = $conexao->prepare($sql);
    $stmt->execute($params);

    $clientes = $conexao->query("SELECT * FROM clientes")->fetchAll(PDO::FETCH_OBJ);
    $produtos  = $conexao->query("SELECT * FROM produtos WHERE estoque > 0")->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { background-color: #ffffff; }
        .form-select, .form-control { background-color: #ffffff; color: #212529; border-color: #ced4da; }
        .form-select:focus, .form-control:focus { background-color: #ffffff; color: #212529; }
        .table { --bs-table-bg: #ffffff; --bs-table-striped-bg: #f2f2f2; --bs-table-hover-bg: #e9ecef; color: #212529; }
        thead th { background-color: #e9ecef; color: #212529; }
    </style>
</head>
<body class="text-dark d-flex flex-column min-vh-100">
<?php include '../navbar.php'; ?>
<div class="container flex-grow-1 py-3">
    <h4 class="mb-3">Vendas</h4>

    <div class="card border mb-4 shadow-sm">
        <div class="card-header bg-white border-bottom">Nova Venda</div>
        <div class="card-body">
            <form action="inserir.php" method="post">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select name="cliente_id" class="form-select" required>
                            <option value="">Selecione o cliente</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?php echo $c->id ?>"><?php echo $c->nome ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="produto_id" class="form-select" required>
                            <option value="">Selecione o produto</option>
                            <?php foreach ($produtos as $p): ?>
                                <option value="<?php echo $p->id ?>">
                                    <?php echo $p->nome ?> — R$ <?php echo number_format($p->preco, 2, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="quantidade" class="form-control" placeholder="Qtd" min="1" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <select name="forma_pagamento" class="form-select" required>
                            <option value="">Pagamento</option>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao_credito">Cartão de Crédito</option>
                            <option value="cartao_debito">Cartão de Débito</option>
                            <option value="pix">PIX</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="pendente">Pendente</option>
                            <option value="pago">Pago</option>
                            <option value="cancelado">Cancelado</option>
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
        <div class="col-md-3">
            <input type="date" name="data_inicio" class="form-control" value="<?php echo $_GET['data_inicio'] ?? '' ?>">
        </div>
        <div class="col-md-3">
            <input type="date" name="data_fim" class="form-control" value="<?php echo $_GET['data_fim'] ?? '' ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
        </div>
        <div class="col-md-1">
            <a href="index.php" class="btn btn-outline-secondary w-100">X</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th><th>Cliente</th><th>Vendedor</th><th>Total</th>
                    <th>Pagamento</th><th>Status</th><th>Data</th><th>Ação</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($linha = $stmt->fetch(PDO::FETCH_OBJ)): ?>
                <?php
                    $badges = ['pago' => 'success', 'pendente' => 'warning text-dark', 'cancelado' => 'danger'];
                    $b = $badges[$linha->status] ?? 'secondary';
                ?>
                <tr>
                    <td><?php echo $linha->id ?></td>
                    <td><?php echo $linha->cliente ?></td>
                    <td><?php echo $linha->vendedor ?></td>
                    <td>R$ <?php echo number_format($linha->total, 2, ',', '.') ?></td>
                    <td><?php echo $linha->forma_pagamento ?></td>
                    <td><span class="badge bg-<?php echo $b ?>"><?php echo $linha->status ?></span></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($linha->criado_em)) ?></td>
                    <td><a href="excluir.php?id=<?php echo $linha->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir venda?')">Excluir</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>