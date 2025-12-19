<?php
require "../../config/database.php";

$hoje = date('Y-m-d');
$mesAtual = date('Y-m');

$recorrentes = $pdo->query("
    SELECT *
    FROM custos
    WHERE recorrente='sim'
")->fetchAll();

foreach ($recorrentes as $custo) {

    $dataGerada = $mesAtual . '-' . str_pad($custo['dia_recorrencia'],2,'0',STR_PAD_LEFT);

    // verifica se já existe esse custo no mês
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM custos
        WHERE descricao = ?
        AND DATE_FORMAT(data,'%Y-%m') = ?
    ");
    $stmt->execute([$custo['descricao'], $mesAtual]);

    if ($stmt->fetchColumn() == 0) {

        $stmt = $pdo->prepare("
            INSERT INTO custos
            (descricao, categoria, tipo, valor, recorrente, dia_recorrencia, data)
            VALUES (?,?,?,?, 'nao', NULL, ?)
        ");

        $stmt->execute([
            $custo['descricao'],
            $custo['categoria'],
            $custo['tipo'],
            $custo['valor'],
            $dataGerada
        ]);
    }
}

echo "Custos recorrentes processados com sucesso";
