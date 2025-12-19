<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /public/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>AM Digital - Painel</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<header class="topo">
    <div class="logo">AM Digital</div>
    <div class="usuario">
        <?= $_SESSION['usuario_nome']; ?> |
        <a href="/public/logout.php">Sair</a>
    </div>
</header>

<div class="container">
