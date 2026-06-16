<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarOperador();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Backoffice</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; margin: 0; }
        nav { background: green; padding: 10px 20px; color: white; display: flex; justify-content: space-between; }
        nav a { color: white; margin-left: 10px; text-decoration: none; }
        .container { max-width: 900px; margin: 30px auto; padding: 20px; }
        h2 { color: green; margin-bottom: 20px; }
        .cards { display: flex; flex-wrap: wrap; gap: 15px; }
        .card { background: white; padding: 20px; border-radius: 8px; width: 200px; text-align: center; text-decoration: none; color: black; border: 1px solid #ddd; }
        .card:hover { background: green; color: white; }
    </style>
</head>
<body>
<nav>
    <span>Backoffice - Clube de Ténis e Padel</span>
    <div>
        <span>Ola, <?= $_SESSION['nome'] ?></span>
        <a href="../logout.php">Sair</a>
    </div>
</nav>
<div class="container">
    <h2>Painel de Administracao</h2>
    <div class="cards">
        <a href="campos.php" class="card"><h3>Campos</h3></a>
        <a href="atletas.php" class="card"><h3>Atletas</h3></a>
        <a href="admin.reservas.php" class="card"><h3>Reservas</h3></a>
        <a href="pagamentos.php" class="card"><h3>Pagamentos</h3></a>
        <a href="relatorios.php" class="card"><h3>Relatorios</h3></a>
        <?php if ($_SESSION['tipo'] === 'gestor'): ?>
        <a href="operadores.php" class="card"><h3>Operadores</h3></a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>