<?php
// includes/header.php
// Garante que a sessão foi iniciada para pegarmos o nome do header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZenBoard - Kanban PHP</title>
    <!-- Adiciona o CSS puro que criamos -->
    <link rel="stylesheet" href="/ZenBoard/assets/css/style.css">
</head>

<body>

    <?php
    // O header só deve mostrar a barra superior se o usuário estiver logado.
    // Em telas como Login e Register, não incluímos este arquivo (ou filtramos aqui).
    if (isset($_SESSION['user_id'])):
    ?>
        <div class="header">
            <h1>ZenBoard</h1>
            <div class="user-info">
                <span>Olá, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!</span>
                <a href="/ZenBoard/auth/logout.php" class="btn btn-danger">Sair</a>
            </div>
        </div>
    <?php endif; ?>