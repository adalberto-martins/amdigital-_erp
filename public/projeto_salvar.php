<?php
session_start();
require "../config/database.php";

$data = [
    $_POST['cliente_id'],
    $_POST['nome'],
    $_POST['tipo'],
    $_POST['descricao'],
    $_POST['valor'],
    $_POST['status'],
    $_POST['data_inicio'],
    $_POST['data_fim']
];

if (empty($_POST['id'])) {
    $sql = "INSERT INTO projetos
    (cliente_id,nome,tipo,descricao,valor,status,data_inicio,data_fim)
    VALUES (?,?,?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
} else {
    $data[] = $_POST['id'];
    $sql = "UPDATE projetos SET
    cliente_id=?, nome=?, tipo=?, descricao=?, valor=?, status=?, data_inicio=?, data_fim=?
    WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
}

header("Location: projetos.php");
