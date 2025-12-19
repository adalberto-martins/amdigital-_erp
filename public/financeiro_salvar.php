<?php
session_start();
require "../config/database.php";

$data = [
    $_POST['tipo'],
    $_POST['cliente_id'] ?: null,
    $_POST['projeto_id'] ?: null,
    $_POST['descricao'],
    $_POST['valor'],
    $_POST['vencimento'],
    $_POST['status']
];

if (empty($_POST['id'])) {
    $sql = "INSERT INTO financeiro
    (tipo,cliente_id,projeto_id,descricao,valor,vencimento,status)
    VALUES (?,?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
} else {
    $data[] = $_POST['id'];
    $sql = "UPDATE financeiro SET
    tipo=?, cliente_id=?, projeto_id=?, descricao=?, valor=?, vencimento=?, status=?
    WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
}

header("Location: financeiro.php");
