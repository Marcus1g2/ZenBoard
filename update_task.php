<?php
// update_task.php
session_start();
require_once 'config/database.php';

// Somente logados podem atualizar
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

// Verifica se recebemos por GET o ID da tarefa e o novo status
if (isset($_GET['id']) && isset($_GET['action'])) {
    $task_id = (int) $_GET['id'];
    $action = $_GET['action'];
    $user_id = $_SESSION['user_id'];

    // Determinamos o novo status baseado na ação solicitada
    $novo_status = '';
    if ($action === 'move_to_progress') {
        $novo_status = 'em_andamento';
    } elseif ($action === 'move_to_done') {
        $novo_status = 'concluida';
    } elseif ($action === 'move_to_pending') {
        $novo_status = 'pendente'; // Caso queira voltar a tarefa pra trás
    }

    // Se a ação for válida, atualiza o banco
    if (!empty($novo_status)) {
        // Garantimos de forma vitalicia que a tarefa pertence ao usuário logado!
        // LIMIT 1 é só por boa prática (já atualiza 1 só mesmo por ser ID)
        $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$novo_status, $task_id, $user_id]);
    }
}

// Retorna ao painel Kanban após tentar atualizar
header("Location: index.php");
exit;
