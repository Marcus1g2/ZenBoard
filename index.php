<?php
// index.php
require_once 'auth/auth_check.php';

// Inclui o Cabeçalho (Header e CSS)
include 'includes/header.php';
?>

<!-- Área Principal do Kanban -->
<div class="board">

    <!-- Coluna: Pendente -->
    <div class="column col-pendente">
        <div class="column-header">
            <span>Pendente</span>
            <span class="task-count">0</span>
        </div>

        <!-- Formulário Rápido para Adicionar Tarefa -->
        <div class="add-task-form">
            <form action="create_task.php" method="POST">
                <input type="text" name="title" placeholder="Nova tarefa..." required>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Adicionar</button>
            </form>
        </div>

        <div class="tasks-container">
            <!-- Exemplo Estático (será substituído pelo PHP puxando do banco) -->
            <div class="task-card">
                <h3>Exemplo de Tarefa</h3>
                <p>Criar a conexão com o banco.</p>
                <div class="task-actions">
                    <a href="#">Mover &rarr;</a>
                    <a href="#" class="delete">Excluir</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna: Em Andamento -->
    <div class="column col-em-andamento">
        <div class="column-header">
            <span>Em Andamento</span>
            <span class="task-count">0</span>
        </div>
        <div class="tasks-container">
            <!-- Tarefas virão aqui -->
        </div>
    </div>

    <!-- Coluna: Concluída -->
    <div class="column col-concluida">
        <div class="column-header">
            <span>Concluída</span>
            <span class="task-count">0</span>
        </div>
        <div class="tasks-container">
            <!-- Tarefas virão aqui -->
        </div>
    </div>

</div>

<?php
// Inclui o Rodapé
include 'includes/footer.php';
?>