<?php
session_start();
require "../config/database.php";
include "../app/views/layout/header.php";
include "../app/views/layout/sidebar.php";

$clientes = $pdo->query("SELECT id,nome FROM clientes WHERE status='ativo'")->fetchAll();

$projeto = [
    'id'=>'','cliente_id'=>'','nome'=>'','tipo'=>'',
    'descricao'=>'','valor'=>'','status'=>'orcamento',
    'data_inicio'=>'','data_fim'=>''
];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM projetos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $projeto = $stmt->fetch();
}
?>

<main class="main">
    <h1><?= $projeto['id'] ? 'Editar Projeto' : 'Novo Projeto' ?></h1>

    <form action="projeto_salvar.php" method="POST">
        <input type="hidden" name="id" value="<?= $projeto['id'] ?>">

        <label>Cliente</label><br>
        <select name="cliente_id" required>
            <option value="">Selecione</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= $c['id'] ?>"
                    <?= $c['id']==$projeto['cliente_id']?'selected':'' ?>>
                    <?= $c['nome'] ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Nome do Projeto</label><br>
        <input type="text" name="nome" required value="<?= $projeto['nome'] ?>"><br><br>

        <label>Tipo</label><br>
        <input type="text" name="tipo" value="<?= $projeto['tipo'] ?>"><br><br>

        <label>Descrição</label><br>
        <textarea name="descricao"><?= $projeto['descricao'] ?></textarea><br><br>

        <label>Valor</label><br>
        <input type="number" step="0.01" name="valor" value="<?= $projeto['valor'] ?>"><br><br>

        <label>Status</label><br>
        <select name="status">
            <option value="orcamento" <?= $projeto['status']=='orcamento'?'selected':'' ?>>Orçamento</option>
            <option value="andamento" <?= $projeto['status']=='andamento'?'selected':'' ?>>Em andamento</option>
            <option value="concluido" <?= $projeto['status']=='concluido'?'selected':'' ?>>Concluído</option>
            <option value="cancelado" <?= $projeto['status']=='cancelado'?'selected':'' ?>>Cancelado</option>
        </select><br><br>

        <label>Data início</label><br>
        <input type="date" name="data_inicio" value="<?= $projeto['data_inicio'] ?>"><br><br>

        <label>Data fim</label><br>
        <input type="date" name="data_fim" value="<?= $projeto['data_fim'] ?>"><br><br>

        <button type="submit">Salvar</button>
        <a href="projetos.php">Cancelar</a>
    </form>
</main>

<?php include "../app/views/layout/footer.php"; ?>
