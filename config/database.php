<?php
// config/database.php

$host = 'localhost';
$dbname = 'ZenBoard_DB';
$username = 'root'; // Padrão do XAMPP
$password = '';     // Padrão do XAMPP (vazio)

try {
    // Cria a conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Configura o PDO para lançar exceções em caso de erro (ótimo para debug)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Opcional: descomente a linha abaixo para testar se conectou (lembre de apagar depois)
    // echo "Conexão com ZenBoard_DB realizada com sucesso!";
} catch (PDOException $e) {
    // Em produção não mostramos o erro inteiro na tela, mas para estudo é útil
    die("Erro de conexão ao banco de dados: " . $e->getMessage());
}
