<?php
session_start();
require "../config/database.php";

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: clientes.php");
