<?php
    session_start();
    if (empty($_SESSION) || $_SESSION['logado'] == false) {
        header('Location:../index.php');
        exit;
    }

    include '../conexao.php';

    $cliente_id = $_POST['cliente_id'];
    $produto_id = $_POST['produto_id'];
    $quantidade = $_POST['quantidade'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $status = $_POST['status'];

    // Busca id do usuário logado pelo email da sessão
    $u = $conexao->prepare("SELECT id FROM usuarios WHERE email = :email");
    $u->bindParam(':email', $_SESSION['email']);
    $u->execute();
    $usr = $u->fetch(PDO::FETCH_OBJ);
    $usuario_id = $usr ? $usr->id : 1;

    // Busca preço do produto
    $s = $conexao->prepare("SELECT preco, estoque FROM produtos WHERE id = :id");
    $s->bindParam(':id', $produto_id);
    $s->execute();
    $prod = $s->fetch(PDO::FETCH_OBJ);

    $preco_unitario = $prod->preco;
    $total = $preco_unitario * $quantidade;

    // Insere venda
    $sql = "INSERT INTO vendas (cliente_id, usuario_id, total, status, forma_pagamento) VALUES (:cliente_id, :usuario_id, :total, :status, :forma_pagamento)";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':cliente_id', $cliente_id);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':total', $total);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':forma_pagamento', $forma_pagamento);
    $stmt->execute();

    $venda_id = $conexao->lastInsertId();

    // Insere item
    $sql2 = "INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES (:venda_id, :produto_id, :quantidade, :preco_unitario)";
    $stmt2 = $conexao->prepare($sql2);
    $stmt2->bindParam(':venda_id', $venda_id);
    $stmt2->bindParam(':produto_id', $produto_id);
    $stmt2->bindParam(':quantidade', $quantidade);
    $stmt2->bindParam(':preco_unitario', $preco_unitario);
    $stmt2->execute();

    // Atualiza estoque
    $novoEstoque = $prod->estoque - $quantidade;
    $upd = $conexao->prepare("UPDATE produtos SET estoque = :estoque WHERE id = :id");
    $upd->bindParam(':estoque', $novoEstoque);
    $upd->bindParam(':id', $produto_id);
    $upd->execute();

    header('Location:index.php');
    exit;
?>
