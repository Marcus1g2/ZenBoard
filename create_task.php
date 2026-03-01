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
        try {
            // Por padrão, a nova tarefa entra como 'pendente' conforme modelamos no banco
            // Adicionado 'description' nulo/vazio para evitar erro 1364 no MySQL
            $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description) VALUES (?, ?, '')");
            $stmt->execute([$user_id, $title]);

            // Volta pro painel apenas se deu sucesso
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            // Se der erro no banco (ex: problema nas colunas de data que você criou), ele mostrará na tela
            die("Erro ao tentar salvar a tarefa: " . $e->getMessage());
        }
    } else {
        header("Location: index.php");
        exit;
    }
}
