<?php
    include '../conexao.php';

    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];

    if ($id) {
        $sql = "UPDATE categorias SET nome = :nome, descricao = :descricao WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id);
    } else {
        $sql = "INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)";
        $stmt = $conexao->prepare($sql);
    }
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->execute();

    header('Location:index.php');
    exit;
?>
