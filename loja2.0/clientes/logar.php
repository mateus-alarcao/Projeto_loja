<?php
    include 'conexao.php';

    $email = $_POST['email'];
    $senha = md5($_POST['senha']);

    $sql = "SELECT * FROM usuarios WHERE email = :email AND senha = :senha";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->execute();

    session_start();
    if ($stmt->rowCount() == 1) {
        $usuario = $stmt->fetch(PDO::FETCH_OBJ);
        $_SESSION['logado']   = true;
        $_SESSION['email']    = $email;
        $_SESSION['nome']     = $usuario->nome;
        $_SESSION['perfil']   = $usuario->perfil;
        $_SESSION['usuario_id'] = $usuario->id;

        if ($usuario->perfil === 'cliente') {
            header('Location:cliente/index.php');
        } else {
            header('Location:produtos/index.php');
        }
        exit;
    } else {
        $_SESSION['logado'] = false;
        header('Location:index.php?erro=1');
        exit;
    }
?>
