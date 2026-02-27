<?php
// delete_task.php
session_start();
require_once 'config/database.php';

// Somente logados podem deletar
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

// Verifica se recebemos por GET o ID da tarefa
if (isset($_GET['id'])) {
    $task_id = (int) $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Deleta a tarefa SE e SOMENTE SE ela pertencer a quem está logado (Segurança Total!)
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$task_id, $user_id]);
}

// Retorna ao painel
header("Location: index.php");
exit;
