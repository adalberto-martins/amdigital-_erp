<?php
session_start();
require "../config/database.php";
include "../app/views/layout/header.php";
include "../app/views/layout/sidebar.php";

$dados = $pdo->query("
    SELECT 
        c.id,
        c.nome AS cliente,

        COALESCE(SUM(CASE 
            WHEN f.tipo='receber' AND f.status='pago' THEN f.valor 
        END),0) AS recebido,

        COALESCE(SUM(CASE 
            WHEN f.tipo='pagar' AND f.status='pago' THEN f.valor 
        END),0) AS pago

    FROM clientes c
    LEFT JOIN financeiro f ON f.cliente_id = c.id

    GROUP BY c.id
    ORDER BY cliente
")->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="main">
<h1>Lucro por Cliente</h1>

<table width="100%" border="1" cellpadding="8">
<tr>
    <th>Cliente</th>
    <th>Recebido</th>
    <th>Pago</th>
    <th>Lucro</th>
</tr>

<?php foreach ($dados as $d):
    $lucro = $d['recebido'] - $d['pago'];
?>
<tr>
    <td><?= htmlspecialchars($d['cliente']) ?></td>
    <td>R$ <?= number_format($d['recebido'],2,',','.') ?></td>
    <td>R$ <?= number_format($d['pago'],2,',','.') ?></td>
    <td style="color:<?= $lucro >= 0 ? 'green' : 'red' ?>">
        R$ <?= number_format($lucro,2,',','.') ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

</main>

<?php include "../app/views/layout/footer.php"; ?>
<?php
session_start();
require "../config/database.php";
include "../app/views/layout/header.php";
include "../app/views/layout/sidebar.php";

$dados = $pdo->query("
    SELECT 
        c.id,
        c.nome AS cliente,

        COALESCE(SUM(CASE 
            WHEN f.tipo='receber' AND f.status='pago' THEN f.valor 
        END),0) AS recebido,

        COALESCE(SUM(CASE 
            WHEN f.tipo='pagar' AND f.status='pago' THEN f.valor 
        END),0) AS pago

    FROM clientes c
    LEFT JOIN financeiro f ON f.cliente_id = c.id

    GROUP BY c.id
    ORDER BY cliente
")->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="main">
<h1>Lucro por Cliente</h1>

<table width="100%" border="1" cellpadding="8">
<tr>
    <th>Cliente</th>
    <th>Recebido</th>
    <th>Pago</th>
    <th>Lucro</th>
</tr>

<?php foreach ($dados as $d):
    $lucro = $d['recebido'] - $d['pago'];
?>
<tr>
    <td><?= htmlspecialchars($d['cliente']) ?></td>
    <td>R$ <?= number_format($d['recebido'],2,',','.') ?></td>
    <td>R$ <?= number_format($d['pago'],2,',','.') ?></td>
    <td style="color:<?= $lucro >= 0 ? 'green' : 'red' ?>">
        R$ <?= number_format($lucro,2,',','.') ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

</main>

<?php include "../app/views/layout/footer.php"; ?>
