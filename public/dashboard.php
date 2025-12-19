<?php
session_start();
require "../config/database.php";
include "../app/views/layout/header.php";
include "../app/views/layout/sidebar.php";

/* ===============================
   FILTRO POR PERÍODO
   =============================== */
$mes = $_GET['mes'] ?? date('m');
$ano = $_GET['ano'] ?? date('Y');
$filtroData = "$ano-$mes";

/* ===============================
   INDICADORES FINANCEIROS
   =============================== */
$total_receber = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE tipo='receber'
      AND status='pendente'
      AND DATE_FORMAT(vencimento,'%Y-%m')='$filtroData'
")->fetchColumn();

$total_pagar = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE tipo='pagar'
      AND status='pendente'
      AND DATE_FORMAT(vencimento,'%Y-%m')='$filtroData'
")->fetchColumn();

$recebido = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE tipo='receber'
      AND status='pago'
      AND DATE_FORMAT(vencimento,'%Y-%m')='$filtroData'
")->fetchColumn();

$pago = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE tipo='pagar'
      AND status='pago'
      AND DATE_FORMAT(vencimento,'%Y-%m')='$filtroData'
")->fetchColumn();

/* ===============================
   CUSTOS
   =============================== */
$total_custos = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM custos
    WHERE DATE_FORMAT(data,'%Y-%m')='$filtroData'
")->fetchColumn();

/* ===============================
   LUCRO REAL
   =============================== */
$lucro_real = $recebido - $pago - $total_custos;

/* ===============================
   OUTROS INDICADORES
   =============================== */
$projetos_andamento = $pdo->query("
    SELECT COUNT(*) FROM projetos WHERE status='andamento'
")->fetchColumn();

$clientes_ativos = $pdo->query("
    SELECT COUNT(*) FROM clientes WHERE status='ativo'
")->fetchColumn();

/* ===============================
   GRÁFICO FINANCEIRO (ANO)
   =============================== */
$grafico_financeiro = $pdo->query("
    SELECT 
        DATE_FORMAT(vencimento,'%m') mes,
        SUM(CASE WHEN tipo='receber' AND status='pago' THEN valor ELSE 0 END) receber,
        SUM(CASE WHEN tipo='pagar' AND status='pago' THEN valor ELSE 0 END) pagar
    FROM financeiro
    WHERE YEAR(vencimento)='$ano'
    GROUP BY mes
    ORDER BY mes
")->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   GRÁFICO DE LUCRO MENSAL (12 MESES)
   =============================== */
$lucro_mensal = $pdo->query("
    SELECT 
        meses.mes,
        COALESCE(rec.recebido,0)
        - COALESCE(pag.pago,0)
        - COALESCE(cus.custos,0) AS lucro
    FROM (
        SELECT '01' mes UNION SELECT '02' UNION SELECT '03' UNION SELECT '04'
        UNION SELECT '05' UNION SELECT '06' UNION SELECT '07' UNION SELECT '08'
        UNION SELECT '09' UNION SELECT '10' UNION SELECT '11' UNION SELECT '12'
    ) meses
    LEFT JOIN (
        SELECT DATE_FORMAT(vencimento,'%m') mes, SUM(valor) recebido
        FROM financeiro
        WHERE tipo='receber' AND status='pago' AND YEAR(vencimento)='$ano'
        GROUP BY mes
    ) rec ON rec.mes = meses.mes
    LEFT JOIN (
        SELECT DATE_FORMAT(vencimento,'%m') mes, SUM(valor) pago
        FROM financeiro
        WHERE tipo='pagar' AND status='pago' AND YEAR(vencimento)='$ano'
        GROUP BY mes
    ) pag ON pag.mes = meses.mes
    LEFT JOIN (
        SELECT DATE_FORMAT(data,'%m') mes, SUM(valor) custos
        FROM custos
        WHERE YEAR(data)='$ano'
        GROUP BY mes
    ) cus ON cus.mes = meses.mes
    ORDER BY meses.mes
")->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="main">
<h1>Dashboard Financeiro</h1>

<!-- FILTRO -->
<form method="GET" style="margin-bottom:20px;">
    <select name="mes">
        <?php for ($m=1;$m<=12;$m++):
            $mm=str_pad($m,2,'0',STR_PAD_LEFT); ?>
            <option value="<?= $mm ?>" <?= $mes==$mm?'selected':'' ?>><?= $mm ?></option>
        <?php endfor; ?>
    </select>

    <select name="ano">
        <?php for ($a=date('Y');$a>=date('Y')-5;$a--): ?>
            <option value="<?= $a ?>" <?= $ano==$a?'selected':'' ?>><?= $a ?></option>
        <?php endfor; ?>
    </select>

    <button type="submit">Filtrar</button>
</form>

<!-- CARDS -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:15px;">

<div style="background:#dcfce7;padding:15px;">
<strong>A Receber</strong><br>R$ <?= number_format($total_receber,2,',','.') ?>
</div>

<div style="background:#fee2e2;padding:15px;">
<strong>A Pagar</strong><br>R$ <?= number_format($total_pagar,2,',','.') ?>
</div>

<div style="background:#e0f2fe;padding:15px;">
<strong>Recebido</strong><br>R$ <?= number_format($recebido,2,',','.') ?>
</div>

<div style="background:#fef9c3;padding:15px;">
<strong>Pago</strong><br>R$ <?= number_format($pago,2,',','.') ?>
</div>

<div style="background:#fde68a;padding:15px;">
<strong>Custos</strong><br>R$ <?= number_format($total_custos,2,',','.') ?>
</div>

<div style="
background:<?= $lucro_real>=0?'#dcfce7':'#fee2e2' ?>;
border:2px solid <?= $lucro_real>=0?'#16a34a':'#dc2626' ?>;
padding:15px;">
<strong>Lucro Real</strong><br>
R$ <?= number_format($lucro_real,2,',','.') ?>
</div>

<div style="background:#f3e8ff;padding:15px;">
<strong>Projetos em andamento</strong><br><?= $projetos_andamento ?>
</div>

<div style="background:#ecfeff;padding:15px;">
<strong>Clientes ativos</strong><br><?= $clientes_ativos ?>
</div>

</div>

<!-- GRÁFICOS -->
<h2 style="margin-top:40px;">Fluxo Financeiro (<?= $ano ?>)</h2>
<canvas id="graficoFinanceiro"></canvas>

<h2 style="margin-top:40px;">Lucro Mensal (<?= $ano ?>)</h2>
<canvas id="graficoLucro"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const fin = <?= json_encode($grafico_financeiro) ?>;
new Chart(document.getElementById('graficoFinanceiro'),{
    type:'bar',
    data:{
        labels:fin.map(i=>i.mes),
        datasets:[
            {label:'Recebido',data:fin.map(i=>i.receber)},
            {label:'Pago',data:fin.map(i=>i.pagar)}
        ]
    }
});
</script>

<script>
const lucro = <?= json_encode($lucro_mensal) ?>;
new Chart(document.getElementById('graficoLucro'),{
    type:'line',
    data:{
        labels:lucro.map(i=>i.mes),
        datasets:[{
            label:'Lucro Mensal',
            data:lucro.map(i=>i.lucro),
            tension:0.4,
            fill:true
        }]
    }
});
</script>

</main>

<?php include "../app/views/layout/footer.php"; ?>
