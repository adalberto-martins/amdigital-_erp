<?php
session_start();
require "../config/database.php";
include "../app/views/layout/header.php";
include "../app/views/layout/sidebar.php";

$sql = "
SELECT f.*, 
       c.nome AS cliente,
       p.nome AS projeto
FROM financeiro f
LEFT JOIN clientes c ON c.id = f.cliente_id
LEFT JOIN projetos p ON p.id = f.projeto_id
ORDER BY f.vencimento ASC
";
$registros = $pdo->query($sql)->fetchAll();
?>

<main class="main">
    <h1>Financeiro</h1>

    <a href="financeiro_form.php" style="margin-bottom:15px;display:inline-block;">
        ➕ Novo Lançamento
    </a>

    <table width="100%" border="1" cellpadding="8">
        <tr>
            <th>Tipo</th>
            <th>Cliente</th>
            <th>Projeto</th>
            <th>Descrição</th>
            <th>Valor</th>
            <th>Vencimento</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>

        <?php foreach ($registros as $r): ?>
        <tr>
            <td><?= $r['tipo']=='receber'?'💚 Receber':'💸 Pagar' ?></td>
            <td><?= $r['cliente'] ?? '-' ?></td>
            <td><?= $r['projeto'] ?? '-' ?></td>
            <td><?= htmlspecialchars($r['descricao']) ?></td>
            <td>R$ <?= number_format($r['valor'],2,',','.') ?></td>
            <td><?= date('d/m/Y', strtotime($r['vencimento'])) ?></td>
            <td><?= $r['status'] ?></td>
            <td>
                <a href="financeiro_form.php?id=<?= $r['id'] ?>">✏️</a>
                <a href="financeiro_excluir.php?id=<?= $r['id'] ?>"
                   onclick="return confirm('Excluir lançamento?')">🗑️</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</main>

<?php include "../app/views/layout/footer.php"; ?>
