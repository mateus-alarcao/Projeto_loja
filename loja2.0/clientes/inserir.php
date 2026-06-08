<?php
    include '../conexao.php';

    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $cpf = $_POST['cpf'];

    if ($id) {
        $sql = "UPDATE clientes SET nome=:nome, email=:email, telefone=:telefone, cpf=:cpf WHERE id=:id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id);
    } else {
        $sql = "INSERT INTO clientes (nome, email, telefone, cpf) VALUES (:nome, :email, :telefone, :cpf)";
        $stmt = $conexao->prepare($sql);
    }
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->execute();

    header('Location:index.php');
    exit;
?>
