<?php
session_start();
require "../config/database.php";
include "../app/views/layout/header.php";
include "../app/views/layout/sidebar.php";

/* Indicadores financeiros */
$total_receber = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro 
    WHERE tipo='receber' AND status='pendente'
")->fetchColumn();

$total_pagar = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro 
    WHERE tipo='pagar' AND status='pendente'
")->fetchColumn();

$recebido = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro 
    WHERE tipo='receber' AND status='pago'
")->fetchColumn();

$pago = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro 
    WHERE tipo='pagar' AND status='pago'
")->fetchColumn();

$lucro = ($recebido ?? 0) - ($pago ?? 0);


$projetos_andamento = $pdo->query("
    SELECT COUNT(*) FROM projetos WHERE status='andamento'
")->fetchColumn();

$clientes_ativos = $pdo->query("
    SELECT COUNT(*) FROM clientes WHERE status='ativo'
")->fetchColumn();

/* Gráfico últimos 6 meses */
$grafico = $pdo->query("
    SELECT 
        DATE_FORMAT(vencimento,'%Y-%m') AS mes,
        SUM(CASE WHEN tipo='receber' THEN valor ELSE 0 END) AS receber,
        SUM(CASE WHEN tipo='pagar' THEN valor ELSE 0 END) AS pagar
    FROM financeiro
    WHERE vencimento >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY mes
    ORDER BY mes
")->fetchAll(PDO::FETCH_ASSOC);

$lucro_mensal = $pdo->query("
    SELECT 
        DATE_FORMAT(vencimento,'%Y-%m') AS mes,
        SUM(CASE WHEN tipo='receber' AND status='pago' THEN valor ELSE 0 END) -
        SUM(CASE WHEN tipo='pagar' AND status='pago' THEN valor ELSE 0 END) AS lucro
    FROM financeiro
    GROUP BY mes
    ORDER BY mes
")->fetchAll(PDO::FETCH_ASSOC);

?>

<main class="main">
<h1>Dashboard Financeiro</h1>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:15px;">

<div style="background:#dcfce7;padding:15px;">
    <strong>A Receber</strong><br>
    R$ <?= number_format($total_receber,2,',','.') ?>
</div>

<div style="background:#fee2e2;padding:15px;">
    <strong>A Pagar</strong><br>
    R$ <?= number_format($total_pagar,2,',','.') ?>
</div>

<div style="background:#e0f2fe;padding:15px;">
    <strong>Recebido</strong><br>
    R$ <?= number_format($recebido,2,',','.') ?>
</div>

<div style="background:#fef9c3;padding:15px;">
    <strong>Pago</strong><br>
    R$ <?= number_format($pago,2,',','.') ?>
</div>

<div style="
    background:<?= $lucro >= 0 ? '#dcfce7' : '#fee2e2' ?>;
    padding:15px;
    border:2px solid <?= $lucro >= 0 ? '#16a34a' : '#dc2626' ?>;
">
    <strong>Lucro</strong><br>
    R$ <?= number_format($lucro,2,',','.') ?>
</div>


<div style="background:#f3e8ff;padding:15px;">
    <strong>Projetos em andamento</strong><br>
    <?= $projetos_andamento ?>
</div>

<div style="background:#ecfeff;padding:15px;">
    <strong>Clientes ativos</strong><br>
    <?= $clientes_ativos ?>
</div>

</div>

<h2 style="margin-top:30px;">Fluxo financeiro (últimos 6 meses)</h2>
<canvas id="graficoFinanceiro"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const dados = <?= json_encode($grafico) ?>;

const meses = dados.map(i => i.mes);
const receber = dados.map(i => i.receber);
const pagar = dados.map(i => i.pagar);

new Chart(document.getElementById('graficoFinanceiro'), {
    type: 'bar',
    data: {
        labels: meses,
        datasets: [
            { label: 'A Receber', data: receber },
            { label: 'A Pagar', data: pagar }
        ]
    }
});
</script>
</main>

<?php include "../app/views/layout/footer.php"; ?>
