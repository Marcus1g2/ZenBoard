<?php
// index.php
require_once 'auth/auth_check.php';

// Como o auth_check.php passou, temos certeza que $logged_in_user_name existe
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ZenBoard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #333;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-logout {
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn-logout:hover {
            background-color: #c82333;
        }

        .board-placeholder {
            background: white;
            padding: 40px;
            text-align: center;
            border-radius: 8px;
            color: #666;
            border: 2px dashed #ccc;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>ZenBoard</h1>
        <div class="user-info">
            <span>Olá, <strong><?php echo htmlspecialchars($logged_in_user_name); ?></strong>!</span>
            <a href="auth/logout.php" class="btn-logout">Sair</a>
        </div>
    </div>

    <div class="board-placeholder">
        <h2>Área do Kanban</h2>
        <p>Aqui entrará o código para listar as tarefas do banco de dados.</p>
    </div>

</body>

</html>