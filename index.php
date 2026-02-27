<?php
// index.php
require_once 'auth/auth_check.php';
require_once 'config/database.php';

$user_id = $_SESSION['user_id'];

// Busca todas as tarefas APENAS do usuário logado e ordena pela mais recente
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separa as tarefas por status em arrays diferentes para facilitar o HTML
$pendentes = [];
$em_andamento = [];
$concluidas = [];

foreach ($tasks as $task) {
    if ($task['status'] === 'pendente') {
        $pendentes[] = $task;
    } elseif ($task['status'] === 'em_andamento') {
        $em_andamento[] = $task;
    } elseif ($task['status'] === 'concluida') {
        $concluidas[] = $task;
    }
}

// Inclui o Cabeçalho (Header e CSS)
include 'includes/header.php';
?>

<!-- Área Principal do Kanban -->
<div class="board">

    <!-- COLUNA 1: PENDENTE -->
    <div class="column col-pendente">
        <div class="column-header">
            <span>Pendente</span>
            <span class="task-count" style="background: #ccc; border-radius: 50%; padding: 2px 8px; font-size: 0.8rem;"><?php echo count($pendentes); ?></span>
        </div>

        <!-- Formulário Rápido para Adicionar Tarefa -->
        <div class="add-task-form">
            <form action="create_task.php" method="POST">
                <input type="text" name="title" placeholder="Nova tarefa..." required>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Adicionar</button>
            </form>
        </div>

        <div class="tasks-container">
            <?php foreach ($pendentes as $tp): ?>
                <div class="task-card">
                    <h3><?php echo htmlspecialchars($tp['title']); ?></h3>
                    <!-- Mostra a hora de criação bonitinha ou data de atualização que você incluiu -->
                    <p>Modificado: <?php echo date('d/m H:i', strtotime($tp['updated_at'] ?? $tp['created_at'])); ?></p>
                    <div class="task-actions">
                        <a href="update_task.php?id=<?php echo $tp['id']; ?>&action=move_to_progress" title="Mover para Em Andamento">Avançar &rarr;</a>
                        <a href="delete_task.php?id=<?php echo $tp['id']; ?>" class="delete" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');">Excluir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- COLUNA 2: EM ANDAMENTO -->
    <div class="column col-em-andamento">
        <div class="column-header">
            <span>Em Andamento</span>
            <span class="task-count" style="background: #ccc; border-radius: 50%; padding: 2px 8px; font-size: 0.8rem;"><?php echo count($em_andamento); ?></span>
        </div>
        <div class="tasks-container">
            <?php foreach ($em_andamento as $ta): ?>
                <div class="task-card">
                    <h3><?php echo htmlspecialchars($ta['title']); ?></h3>
                    <p>Modificado: <?php echo date('d/m H:i', strtotime($ta['updated_at'] ?? $ta['created_at'])); ?></p>
                    <div class="task-actions">
                        <a href="update_task.php?id=<?php echo $ta['id']; ?>&action=move_to_pending" title="Voltar para Pendente">&larr; Voltar</a>
                        <a href="update_task.php?id=<?php echo $ta['id']; ?>&action=move_to_done" title="Mover para Concluída">Concluir &checkmark;</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- COLUNA 3: CONCLUÍDA -->
    <div class="column col-concluida">
        <div class="column-header">
            <span>Concluída</span>
            <span class="task-count" style="background: #ccc; border-radius: 50%; padding: 2px 8px; font-size: 0.8rem;"><?php echo count($concluidas); ?></span>
        </div>
        <div class="tasks-container">
            <?php foreach ($concluidas as $tc): ?>
                <div class="task-card">
                    <h3 style="text-decoration: line-through; color: #888;"><?php echo htmlspecialchars($tc['title']); ?></h3>
                    <p>Concluído em: <?php echo date('d/m H:i', strtotime($tc['updated_at'] ?? $tc['created_at'])); ?></p>
                    <div class="task-actions">
                        <a href="update_task.php?id=<?php echo $tc['id']; ?>&action=move_to_progress" title="Voltar para Em Andamento">&larr; Reabrir</a>
                        <a href="delete_task.php?id=<?php echo $tc['id']; ?>" class="delete" onclick="return confirm('Excluir definitivamente?');">Excluir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<?php
// Inclui o Rodapé
include 'includes/footer.php';
?>