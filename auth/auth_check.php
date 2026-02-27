<?php
// auth/auth_check.php
session_start();

// Verifica se o usuário NÃO está logado
if (!isset($_SESSION['user_id'])) {
    // Redireciona para a página de login
    header("Location: /ZenBoard/auth/login.php");
    exit;
}

// Se chegou aqui, o usuário está logado. 
// Opcional: podemos disponibilizar os dados dele para uso na página
$logged_in_user_id = $_SESSION['user_id'];
$logged_in_user_name = $_SESSION['user_name'];
