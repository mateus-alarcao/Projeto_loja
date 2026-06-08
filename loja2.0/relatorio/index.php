<?php
    include '../conexao.php';
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION) || !$_SESSION['logado']) { header('Location:../index.php'); exit; }

    $data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
    $data_fim    = $_GET['data_fim']    ?? date('Y-m-d');

    $resumo = $conexao->prepare("
        SELECT COUNT(*) AS total_vendas, SUM(total) AS faturamento, AVG(total) AS ticket_medio
        FROM vendas WHERE DATE(criado_em) BETWEEN :inicio AND :fim AND status != 'cancelado'
    ");
    $resumo->execute([':inicio' => $data_inicio, ':fim' => $data_fim]);
    $r = $resumo->fetch(PDO::FETCH_OBJ);

    $top = $conexao->prepare("
        SELECT p.nome, SUM(iv.quantidade) AS qtd, SUM(iv.quantidade * iv.preco_unitario) AS receita
        FROM itens_venda iv
        JOIN produtos p ON iv.produto_id = p.id
        JOIN vendas v ON iv.venda_id = v.id
        WHERE DATE(v.criado_em) BETWEEN :inicio AND :fim AND v.status != 'cancelado'
        GROUP BY p.id, p.nome ORDER BY qtd DESC LIMIT 5
    ");
    $top->execute([':inicio' => $data_inicio, ':fim' => $data_fim]);
    $topRows = $top->fetchAll(PDO::FETCH_OBJ);

    $catVendas = $conexao->prepare("
        SELECT c.nome AS categoria, SUM(iv.quantidade) AS qtd, SUM(iv.quantidade * iv.preco_unitario) AS receita
        FROM itens_venda iv
        JOIN produtos p ON iv.produto_id = p.id
        JOIN categorias c ON p.categoria_id = c.id
        JOIN vendas v ON iv.venda_id = v.id
        WHERE DATE(v.criado_em) BETWEEN :inicio AND :fim AND v.status != 'cancelado'
        GROUP BY c.id, c.nome
    ");
    $catVendas->execute([':inicio' => $data_inicio, ':fim' => $data_fim]);
    $catRows = $catVendas->fetchAll(PDO::FETCH_OBJ);

    $estoque = $conexao->query("SELECT nome, estoque FROM produtos WHERE estoque < 5 ORDER BY estoque ASC");
    $estoqueRows = $estoque->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; color: #212529; }
        .card { background-color: #ffffff; color: #212529; }
        .form-control { background-color: #ffffff; color: #212529; border-color: #ced4da; }
        .form-control:focus { background-color: #ffffff; color: #212529; }
        .table { --bs-table-bg: #ffffff; --bs-table-striped-bg: #f2f2f2; --bs-table-hover-bg: #e9ecef; color: #212529; }
        thead th { background-color: #e9ecef; color: #212529; }


        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; color: #000 !important; }
            .card { border: 1px solid #ccc !important; background: #fff !important; color: #000 !important; }
            table { color: #000 !important; }
            h4, h5, small { color: #000 !important; }
            .print-header { display: block !important; }
        }
        .print-header { display: none; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<?php include '../navbar.php'; ?>
<div class="container flex-grow-1 py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Relatório de Vendas</h4>
        <button class="btn btn-outline-secondary btn-sm no-print" onclick="gerarPDF()">🖨️ Gerar PDF</button>
    </div>

    <!-- Cabeçalho visível só no PDF -->
    <div class="print-header mb-3">
        <h5>StudioTone — Relatório de Vendas</h5>
        <p>Período: <?php echo date('d/m/Y', strtotime($data_inicio)) ?> até <?php echo date('d/m/Y', strtotime($data_fim)) ?></p>
    </div>

    <form method="get" class="row g-2 mb-4 no-print">
        <div class="col-md-3">
            <input type="date" name="data_inicio" class="form-control" value="<?php echo $data_inicio ?>">
        </div>
        <div class="col-md-3">
            <input type="date" name="data_fim" class="form-control" value="<?php echo $data_fim ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
        </div>
    </form>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white text-center p-3 shadow-sm">
                <small class="text-white-100">Total Faturado</small>
                <h4 class="mb-0">R$ <?php echo number_format($r->faturamento ?? 0, 2, ',', '.') ?></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white text-center p-3 shadow-sm">
                <small class="text-white-100">Número de Vendas</small>
                <h4 class="mb-0"><?php echo $r->total_vendas ?? 0 ?></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white text-center p-3 shadow-sm">
                <small class="text-white-100">Ticket Médio</small>
                <h4 class="mb-0">R$ <?php echo number_format($r->ticket_medio ?? 0, 2, ',', '.') ?></h4>
            </div>
        </div>
    </div>

    <h5>Produtos Mais Vendidos</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-4 align-middle">
            <thead>
                <tr><th>Produto</th><th>Quantidade</th><th>Receita</th></tr>
            </thead>
            <tbody>
            <?php foreach ($topRows as $linha): ?>
                <tr>
                    <td><?php echo $linha->nome ?></td>
                    <td><?php echo $linha->qtd ?></td>
                    <td>R$ <?php echo number_format($linha->receita, 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h5>Vendas por Categoria</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-4 align-middle">
            <thead>
                <tr><th>Categoria</th><th>Quantidade</th><th>Faturamento</th></tr>
            </thead>
            <tbody>
            <?php foreach ($catRows as $linha): ?>
                <tr>
                    <td><?php echo $linha->categoria ?></td>
                    <td><?php echo $linha->qtd ?></td>
                    <td>R$ <?php echo number_format($linha->receita, 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h5>⚠️ Alerta de Estoque Baixo (menos de 5 unidades)</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-4 align-middle" style="--bs-table-bg: #fff3cd; --bs-table-striped-bg: #ffeaa7;">
            <thead>
                <tr><th>Produto</th><th>Estoque</th></tr>
            </thead>
            <tbody>
            <?php foreach ($estoqueRows as $linha): ?>
                <tr>
                    <td><?php echo $linha->nome ?></td>
                    <td><?php echo $linha->estoque ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function gerarPDF() {
        window.print();
    }
</script>
</body>
</html>