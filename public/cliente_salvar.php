<?php
session_start();
require "../config/database.php";

$data = [
    $_POST['nome'],
    $_POST['email'],
    $_POST['telefone'],
    $_POST['cpf_cnpj'],
    $_POST['endereco'],
    $_POST['observacoes'],
    $_POST['status']
];

if (empty($_POST['id'])) {
    $sql = "INSERT INTO clientes
        (nome,email,telefone,cpf_cnpj,endereco,observacoes,status)
        VALUES (?,?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
} else {
    $data[] = $_POST['id'];
    $sql = "UPDATE clientes SET
        nome=?, email=?, telefone=?, cpf_cnpj=?, endereco=?, observacoes=?, status=?
        WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
}

header("Location: clientes.php");
