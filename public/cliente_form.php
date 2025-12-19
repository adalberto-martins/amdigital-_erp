<?php
session_start();
require "../config/database.php";
include "../app/views/layout/header.php";
include "../app/views/layout/sidebar.php";

$cliente = [
    'id' => '',
    'nome' => '',
    'email' => '',
    'telefone' => '',
    'cpf_cnpj' => '',
    'endereco' => '',
    'observacoes' => '',
    'status' => 'ativo'
];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $cliente = $stmt->fetch();
}
?>

<main class="main">
    <h1><?= $cliente['id'] ? 'Editar Cliente' : 'Novo Cliente' ?></h1>

    <form action="cliente_salvar.php" method="POST">
        <input type="hidden" name="id" value="<?= $cliente['id'] ?>">

        <label>Nome</label><br>
        <input type="text" name="nome" required value="<?= $cliente['nome'] ?>"><br><br>

        <label>E-mail</label><br>
        <input type="email" name="email" value="<?= $cliente['email'] ?>"><br><br>

        <label>Telefone</label><br>
        <input type="text" name="telefone" value="<?= $cliente['telefone'] ?>"><br><br>

        <label>CPF/CNPJ</label><br>
        <input type="text" name="cpf_cnpj" value="<?= $cliente['cpf_cnpj'] ?>"><br><br>

        <label>Endereço</label><br>
        <textarea name="endereco"><?= $cliente['endereco'] ?></textarea><br><br>

        <label>Observações</label><br>
        <textarea name="observacoes"><?= $cliente['observacoes'] ?></textarea><br><br>

        <label>Status</label><br>
        <select name="status">
            <option value="ativo" <?= $cliente['status']=='ativo'?'selected':'' ?>>Ativo</option>
            <option value="prospect" <?= $cliente['status']=='prospect'?'selected':'' ?>>Prospect</option>
            <option value="inativo" <?= $cliente['status']=='inativo'?'selected':'' ?>>Inativo</option>
        </select><br><br>

        <button type="submit">Salvar</button>
        <a href="clientes.php">Cancelar</a>
    </form>
</main>

<?php include "../app/views/layout/footer.php"; ?>
