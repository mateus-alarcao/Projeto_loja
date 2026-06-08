<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION) || $_SESSION['logado'] == false) {
        header('Location:../index.php');
        exit;
    }
?>
