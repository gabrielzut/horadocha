<?php
    require "verificaLogin.php";
    verificaLogin();
    
    $idproduto = $_POST['idproduto'];

    session_start();

    unset($_SESSION['produtos'][$idproduto]);

    header('Location: ../carrinho.php');
?>