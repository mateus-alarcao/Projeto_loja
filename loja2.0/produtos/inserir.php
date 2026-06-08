<?php
    include '../conexao.php';

    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $categoria_id = $_POST['categoria_id'] ?: null;

    if ($id) {
        $sql = "UPDATE produtos SET nome=:nome, descricao=:descricao, preco=:preco, estoque=:estoque, categoria_id=:categoria_id WHERE id=:id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id);
    } else {
        $sql = "INSERT INTO produtos (nome, descricao, preco, estoque, categoria_id) VALUES (:nome, :descricao, :preco, :estoque, :categoria_id)";
        $stmt = $conexao->prepare($sql);
    }
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':preco', $preco);
    $stmt->bindParam(':estoque', $estoque);
    $stmt->bindParam(':categoria_id', $categoria_id);
    $stmt->execute();

    header('Location:index.php');
    exit;
?>
