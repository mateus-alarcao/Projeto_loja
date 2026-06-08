<?php
    include '../conexao.php';

    $id = $_GET['id'];

    // Remove itens primeiro
    $s = $conexao->prepare("DELETE FROM itens_venda WHERE venda_id = :id");
    $s->bindParam(':id', $id);
    $s->execute();

    // Remove venda
    $sql = "DELETE FROM vendas WHERE id = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header('Location:index.php');
?>
