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
        $stmt = $pdo->prepare("SELECT id, name, password_hash, login_attempts, locked_until FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verifica se a conta está bloqueada no momento
            if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
                $time_left = ceil((strtotime($user['locked_until']) - time()) / 60);
                $error = "Conta temporariamente bloqueada por múltiplas tentativas. Tente novamente em {$time_left} minutos.";
            } else {
                // Conta liberada ou bloqueio já expirou, vamos checar a senha
                if (password_verify($password, $user['password_hash'])) {
                    // Sucesso! Inicia a sessão
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];

                    // Zera as tentativas e limpa o tempo de bloqueio
                    $updateStmt = $pdo->prepare("UPDATE users SET login_attempts = 0, locked_until = NULL WHERE id = ?");
                    $updateStmt->execute([$user['id']]);

                    // Redireciona para o Kanban
                    header("Location: ../index.php");
                    exit;
                } else {
                    // Senha Incorreta
                    $attempts = $user['login_attempts'] + 1;
                    $lock_until = null;
                    $error = "E-mail ou senha incorretos.";

                    // Se atingiu 5 falhas, bloqueia por 15 minutos
                    if ($attempts >= 5) {
                        $lock_until = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                        $error = "Muitas tentativas falhas. Sua conta foi bloqueada por 15 minutos.";
                    } else {
                        $restantes = 5 - $attempts;
                        if ($restantes <= 2) {
                            $error .= " Atenção: Você tem apenas mais {$restantes} " . ($restantes == 1 ? "tentativa" : "tentativas") . " antes do bloqueio.";
                        }
                    }

                    // Registra a falha no banco
                    $failStmt = $pdo->prepare("UPDATE users SET login_attempts = ?, locked_until = ? WHERE id = ?");
                    $failStmt->execute([$attempts, $lock_until, $user['id']]);
                }
            }
        } else {
            // E-mail não encontrado (mas exibimos mensagem genérica por segurança)
            $error = "E-mail ou senha incorretos.";
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