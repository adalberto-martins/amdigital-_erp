<?php
session_start();
require "../config/database.php";
include "../app/views/layout/header.php";
include "../app/views/layout/sidebar.php";

$sql = "
SELECT p.*, c.nome AS cliente
FROM projetos p
JOIN clientes c ON c.id = p.cliente_id
ORDER BY p.id DESC
";
$projetos = $pdo->query($sql)->fetchAll();
?>

<main class="main">
    <h1>Projetos</h1>

    <a href="projeto_form.php" style="margin-bottom:15px;display:inline-block;">
        ➕ Novo Projeto
    </a>

    <table width="100%" border="1" cellpadding="8">
        <tr>
            <th>Projeto</th>
            <th>Cliente</th>
            <th>Tipo</th>
            <th>Valor</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>

        <?php foreach ($projetos as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['nome']) ?></td>
            <td><?= htmlspecialchars($p['cliente']) ?></td>
            <td><?= $p['tipo'] ?></td>
            <td>R$ <?= number_format($p['valor'],2,',','.') ?></td>
            <td><?= $p['status'] ?></td>
            <td>
                <a href="projeto_form.php?id=<?= $p['id'] ?>">✏️ Editar</a> |
                <a href="projeto_excluir.php?id=<?= $p['id'] ?>"
                   onclick="return confirm('Excluir este projeto?')">🗑️ Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</main>

<?php include "../app/views/layout/footer.php"; ?>
