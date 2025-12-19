<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require_once "../../config/database.php";

$email = $_POST['email'];
$senha = hash('sha256', $_POST['senha']);

$sql = "SELECT * FROM usuarios WHERE email = ? AND senha = ? AND status = 'ativo'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email, $senha]);
$usuario = $stmt->fetch();

if ($usuario) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_nivel'] = $usuario['nivel'];
    header("Location: ../../public/dashboard.php");
} else {
    header("Location: ../../public/index.php?erro=1");
}
