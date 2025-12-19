<?php
session_start();
require "../config/database.php";
include "../app/views/layout/header.php";
include "../app/views/layout/sidebar.php";

$clientes = $pdo->query("SELECT * FROM clientes ORDER BY id DESC")->fetchAll();
?>

<main class="main">
    <h1>Clientes</h1>

    <a href="cliente_form.php" style="margin-bottom:15px;display:inline-block;">
        ➕ Novo Cliente
    </a>

    <table width="100%" border="1" cellpadding="8">
        <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>

        <?php foreach ($clientes as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['nome']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars($c['telefone']) ?></td>
            <td><?= $c['status'] ?></td>
            <td>
                <a href="cliente_form.php?id=<?= $c['id'] ?>">✏️ Editar</a> |
                <a href="cliente_excluir.php?id=<?= $c['id'] ?>"
                   onclick="return confirm('Deseja excluir este cliente?')">🗑️ Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</main>

<?php include "../app/views/layout/footer.php"; ?>
