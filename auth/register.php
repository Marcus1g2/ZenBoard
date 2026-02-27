<?php
// auth/register.php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validação básica
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Por favor, preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Formato de e-mail inválido.";
    } elseif (strlen($password) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        // Verifica se o e-mail já existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Este e-mail já está cadastrado.";
        } else {
            // Hashing da senha (Melhor prática de segurança)
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Inserção no banco de dados
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $password_hash])) {
                $success = "Cadastro realizado com sucesso! Você já pode fazer login.";
                // Em um fluxo real, poderíamos redirecionar direto para o login.php:
                // header("Location: login.php");
                // exit;
            } else {
                $error = "Erro ao cadastrar usuário. Tente novamente mais tarde.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - ZenBoard</title>
    <!-- Um estilo básico embutido para podermos testar a tela rapidamente.
         Depois vamos mover tudo para o assets/css/style.css -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .auth-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: .5rem;
            color: #666;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: .75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: .75rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: #dc3545;
            background: #f8d7da;
            padding: .5rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .success {
            color: #28a745;
            background: #d4edda;
            padding: .5rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .auth-links {
            text-align: center;
            margin-top: 1rem;
        }

        .auth-links a {
            color: #007bff;
            text-decoration: none;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="auth-container">
        <h2>Criar Conta</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php else: ?>
            <!-- O formulário só aparece se não houver sucesso (ou você pode deixar sempre) -->
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="name">Nome Completo</label>
                    <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="password">Senha (mínimo 6 caracteres)</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Cadastrar</button>
            </form>
        <?php endif; ?>

        <div class="auth-links">
            Já tem uma conta? <a href="login.php">Faça login</a>
        </div>
    </div>

</body>

</html>