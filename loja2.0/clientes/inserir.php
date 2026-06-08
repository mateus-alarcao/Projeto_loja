<?php
    include '../conexao.php';

    $id       = isset($_POST['id']) ? $_POST['id'] : null;
    $nome     = $_POST['nome'];
    $email    = $_POST['email'];
    $telefone = $_POST['telefone'];
    $cpf      = $_POST['cpf'];
    $cep      = $_POST['cep']      ?? '';
    $logradouro = $_POST['logradouro'] ?? '';
    $bairro   = $_POST['bairro']   ?? '';
    $cidade   = $_POST['cidade']   ?? '';
    $uf       = $_POST['uf']       ?? '';

    if ($id) {
        $sql = "UPDATE clientes SET nome=:nome, email=:email, telefone=:telefone, cpf=:cpf,
                cep=:cep, logradouro=:logradouro, bairro=:bairro, cidade=:cidade, uf=:uf WHERE id=:id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id);
    } else {
        $sql = "INSERT INTO clientes (nome, email, telefone, cpf, cep, logradouro, bairro, cidade, uf)
                VALUES (:nome, :email, :telefone, :cpf, :cep, :logradouro, :bairro, :cidade, :uf)";
        $stmt = $conexao->prepare($sql);
    }
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':cep', $cep);
    $stmt->bindParam(':logradouro', $logradouro);
    $stmt->bindParam(':bairro', $bairro);
    $stmt->bindParam(':cidade', $cidade);
    $stmt->bindParam(':uf', $uf);
    $stmt->execute();

    header('Location:index.php');
    exit;
?>
