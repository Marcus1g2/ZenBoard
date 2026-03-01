<?php
// auth/login.php
session_start();
require_once '../config/database.php';

// Se o usuário já estiver logado, redireciona para o painel
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Por favor, preencha todos os campos.";
    } else {
        // Busca o usuário pelo e-mail
        $stmt = $pdo->prepare("SELECT id, name, password_hash, login_attempts FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se o e-mail existe E a senha confere
        if ($user && password_verify($password, $user['password_hash'])) {
            // Sucesso! Inicia a sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Opcional: Zera as tentativas de login (treinamento de segurança)
            $updateStmt = $pdo->prepare("UPDATE users SET login_attempts = 0 WHERE id = ?");
            $updateStmt->execute([$user['id']]);

            // Redireciona para o Kanban
            header("Location: ../index.php");
            exit;
        } else {
            $error = "E-mail ou senha incorretos.";

            // Opcional: Registra uma tentativa falha (treinamento de segurança)
            if ($user) {
                $failStmt = $pdo->prepare("UPDATE users SET login_attempts = login_attempts + 1 WHERE id = ?");
                $failStmt->execute([$user['id']]);
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
    <title>Login - ZenBoard</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>

<body>

    <div class="auth-container">
        <h2>Entrar no ZenBoard</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Entrar</button>
        </form>

        <div class="auth-links" style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 10px;">
            <div>Esqueceu a senha? <a href="forgot_password.php">Recuperar acesso</a></div>
            <div>Ainda não tem conta? <a href="register.php">Cadastre-se</a></div>
        </div>
    </div>

</body>

</html>