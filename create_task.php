<?php
// create_task.php
session_start();
require_once 'config/database.php';

// Bloqueia quem não está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $user_id = $_SESSION['user_id'];

    // Impede títulos vazios
    if (!empty($title)) {
        // Por padrão, a nova tarefa entra como 'pendente' conforme modelamos no banco
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title) VALUES (?, ?)");
        $stmt->execute([$user_id, $title]);
    }

    // Volta pro painel independentemente de dar acerto/erro (podemos evoluir pra msg de erro depois)
    header("Location: index.php");
    exit;
}
