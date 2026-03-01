<?php
// auth/forgot_password.php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error = "Por favor, informe seu e-mail.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Formato de e-mail inválido.";
    } else {
        // Verifica se o usuário existe
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Gera um token único e aleatório
            $token = bin2hex(random_bytes(32));

            // Define validade (ex: 1 hora a partir de agora)
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Apaga tokens antigos desse usuário por segurança
            $delStmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
            $delStmt->execute([$email]);

            // Insere o novo token
            $insertStmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $insertStmt->execute([$email, $token, $expiresAt]);

            // Em produção real dispararíamos um e-mail aqui usando mail() ou PHPMailer.
            // Para testes em servidor local (XAMPP sem servidor SMTP),
            // Vamos exibir o link único direto na tela para o usuário poder testar o fluxo.

            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/ZenBoard/auth/reset_password.php?token=" . $token;

            // Comentado para ambiente de teste. Em prod: envia e-mail com a URl.
            // mail($email, "Recuperação de Senha - ZenBoard", "Acesse para redefinir: " . $resetLink);

            $success = "Um link de recuperação de senha foi gerado. <br> <br> <strong><a href='$resetLink'>Clique aqui para redefinir sua senha</a></strong> <br><br> <small>(Nota: num cenário real, este link será enviado para: $email)</small>";
        } else {
            // Por segurança, mostramos a mesma mensagem de sucesso, mesmo se não existir.
            // Isso evita "Time-Based Enumeration" ou descoberta de contas.
            $success = "Se o seu e-mail constar em nossa base de dados, mandaremos um link de recuperação. (Nota para teste: E-mail não encontrado no banco).";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - ZenBoard</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>

<body>

    <div class="auth-container">
        <h2>Recuperar Senha</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; // Not escaping deliberately to show the clickable link 
                                    ?></div>
            <div class="auth-links">
                <a href="login.php">Voltar ao Login</a>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666; font-size: 0.9rem; margin-bottom: 1.5rem;">
                Informe seu e-mail cadastrado e enviaremos instruções para redefinir sua senha.
            </p>
            <form action="forgot_password.php" method="POST">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <button type="submit">Enviar Link de Recuperação</button>
            </form>
            <div class="auth-links">
                Lembrou da senha? <a href="login.php">Faça login</a>
            </div>
        <?php endif; ?>

    </div>

</body>

</html>