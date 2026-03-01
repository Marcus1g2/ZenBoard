<?php
require_once '/opt/lampp/htdocs/ZenBoard/config/database.php';

try {
    // Pegar um ID de usuário válido
    $stmt = $pdo->query("SELECT id FROM users LIMIT 1");
    $user = $stmt->fetch();

    if (!$user) {
        die("Nenhum usuário no banco. Cadastre um usuário primeiro.");
    }

    $user_id = $user['id'];
    $title = "Tarefa de Teste CLI";

    echo "Tentando inserir para o usuário $user_id...\n";
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title) VALUES (?, ?)");
    $stmt->execute([$user_id, $title]);

    echo "Sucesso! Inserido.\n";
} catch (Exception $e) {
    echo "ERRO PDO: " . $e->getMessage() . "\n";
}
