<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION) || $_SESSION['logado'] == false) {
        header('Location:../index.php');
        exit;
    }
    if ($_SESSION['perfil'] !== 'cliente') {
        header('Location:../produtos/index.php');
        exit;
    }
?>
