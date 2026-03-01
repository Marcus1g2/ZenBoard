<?php
// auth/reset_password.php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';
$validToken = false;
$email = '';

// 1. Verifica se o token veio via GET (quando clica no link) ou POST (quando envia a nova senha)
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (empty($token)) {
    $error = "Token de recuperação inválido ou não fornecido.";
} else {
    // 2. Busca o token no banco de dados e vê se não expirou
    $stmt = $pdo->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
    $stmt->execute([$token]);
    $resetData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resetData) {
        $expiresAt = strtotime($resetData['expires_at']);
        $now = time();

        if ($now > $expiresAt) {
            $error = "Este link de recuperação expirou. Por favor, solicite um novo.";
        } else {
            $validToken = true;
            $email = $resetData['email'];
        }
    } else {
        $error = "Token inválido.";
    }
}

// 3. Se enviou o formulário de nova senha e o token ainda é válido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($password) || empty($password_confirm)) {
        $error = "Preencha ambas as senhas.";
    } elseif (strlen($password) < 6) {
        $error = "A nova senha deve ter pelo menos 6 caracteres.";
    } elseif ($password !== $password_confirm) {
        $error = "As senhas não coincidem.";
    } else {
        // Sucesso: vamos atualizar no banco
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Atualiza a tabela users
        $updateStmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        if ($updateStmt->execute([$password_hash, $email])) {

            // Deleta o token usado para não ser reutilizado
            $delStmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
            $delStmt->execute([$token]);

            $success = "Sua senha foi redefinida com sucesso!";
            $validToken = false; // Esconde o formulário já que deu sucesso
        } else {
            $error = "Ocorreu um erro ao atualizar sua senha. Tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - ZenBoard</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>

<body>

    <div class="auth-container">
        <h2>Redefinir Senha</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <div class="auth-links">
                <a href="login.php" class="btn btn-success" style="display:inline-block; margin-top:10px; padding:10px 20px; color:white; border-radius:4px; text-decoration:none;">Ir para o Login</a>
            </div>
        <?php elseif ($validToken): ?>
            <p style="text-align: center; color: #666; font-size: 0.9rem; margin-bottom: 1.5rem;">
                Defina uma nova senha para a conta: <br> <strong><?php echo htmlspecialchars($email); ?></strong>
            </p>
            <form action="reset_password.php" method="POST">
                <!-- Enviamos o token em campo oculto para manter no POST -->
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="form-group">
                    <label for="password">Nova Senha</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="password_confirm">Confirmar Nova Senha</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                <button type="submit">Salvar Nova Senha</button>
            </form>
        <?php else: ?>
            <!-- Tela caso o token seja inválido de cara e não gerou success -->
            <div class="auth-links">
                <a href="forgot_password.php">Solicitar nova recuperação de senha</a>
            </div>
        <?php endif; ?>

    </div>

</body>

</html>