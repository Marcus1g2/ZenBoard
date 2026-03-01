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

            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/ZenBoard/auth/reset_password.php?token=" . $token;

            // Integração com PHPMailer para envio real (mesmo no Ngrok/XAMPP)
            /*
            require '../vendor/autoload.php';
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

            try {
                // Configurações do SMTP (Substitua pelos seus dados reais)
                $mail->isSMTP();
                $mail->Host       = 'smtp.outlook.com.pt'; // Ex: smtp.gmail.com
                $mail->SMTPAuth   = true;
                $mail->Username   = 'seu_email@dominio.com.pt'; // Seu e-mail de envio
                $mail->Password   = 'sua_senha_ou_app_password'; // Sua senha
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS; // ou ENCRYPTION_STARTTLS (porta 587)
                $mail->Port       = 465; // ou 587

                // Destinatários
                $mail->setFrom('seu_email@dominio.com.br', 'ZenBoard - Sistema');
                $mail->addAddress($email, $user['name']);

                // Conteúdo do E-mail
                $mail->isHTML(true);
                $mail->Subject = 'Recuperação de Senha - ZenBoard';
                $mail->Body    = "Olá <b>{$user['name']}</b>,<br><br>Você solicitou a recuperação de senha.<br><br>Para criar uma nova senha, clique no link abaixo:<br><br><a href='{$resetLink}'>{$resetLink}</a><br><br>Se você não solicitou isso, pode ignorar este e-mail.<br><br>Equipe ZenBoard.";
                $mail->AltBody = "Olá {$user['name']}, Você solicitou a recuperação de senha. Para criar uma nova senha, acesse: {$resetLink}";

                $mail->send();
                $success = "Um link de redefinição de senha foi enviado para o seu e-mail. Por favor, verifique sua caixa de entrada e a pasta de spam.";
            } catch (Exception $e) {
                // Se o SMTP falhar, mostraremos na tela para que você possa debugar as credenciais
                $error = "Ocorreu um erro ao tentar enviar o e-mail de recuperação. Erro SMTP: {$mail->ErrorInfo}";
            }
            */

            // Temporário: exibe o link na tela para não precisar testar com envio de e-mail por enquanto
            $success = "Um link de recuperação de senha foi gerado. <br> <br> <strong><a href='$resetLink'>Clique aqui para redefinir sua senha</a></strong> <br><br> <small>(Nota: o envio de e-mails via SMTP está comentado/desativado no momento)</small>";
        } else {
            // Por segurança, mostramos a mesma mensagem de sucesso, mesmo se não existir.
            // Isso evita "Time-Based Enumeration" ou descoberta de contas.
            $success = "Se o seu e-mail constar em nossa base de dados, mandaremos um link de recuperação.";
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