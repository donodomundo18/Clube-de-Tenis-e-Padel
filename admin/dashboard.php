<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarOperador();

$total_reservas = $pdo->query("SELECT COUNT(*) FROM reserva WHERE estado_reserva = 'ativa'")->fetchColumn();
$total_atletas = $pdo->query("SELECT COUNT(*) FROM atleta WHERE estado_atleta = 'ativo'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Backoffice - Clube de Ténis e Pádel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f0f4f0; }
        nav { background: #1a6b1a; padding: 12px 25px; color: white; display: flex; justify-content: space-between; align-items: center; }
        nav span { font-size: 16px; font-weight: bold; }
        nav .nav-links a { color: white; margin-left: 15px; text-decoration: none; background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 4px; font-size: 13px; }
        nav .nav-links a:hover { background: rgba(255,255,255,0.4); }
        .container { max-width: 950px; margin: 30px auto; padding: 20px; }
        .boas-vindas { background: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #1a6b1a; }
        .boas-vindas h2 { color: #1a6b1a; font-size: 18px; }
        .boas-vindas p { color: #888; font-size: 13px; margin-top: 3px; }
        .stats { display: flex; gap: 15px; margin-bottom: 25px; }
        .stat { background: white; padding: 15px 20px; border-radius: 8px; border: 1px solid #ddd; text-align: center; flex: 1; }
        .stat h3 { color: #1a6b1a; font-size: 28px; }
        .stat p { color: #888; font-size: 12px; margin-top: 3px; }
        h3.titulo { color: #333; margin-bottom: 15px; font-size: 15px; }
        .cards { display: flex; flex-wrap: wrap; gap: 12px; }
        .card { background: white; padding: 18px 20px; border-radius: 8px; width: 180px; text-align: center; text-decoration: none; color: #333; border: 1px solid #ddd; transition: all 0.2s; }
        .card:hover { background: #1a6b1a; color: white; border-color: #1a6b1a; }
        .card h3 { font-size: 15px; }
        .card p { font-size: 11px; color: #999; margin-top: 5px; }
        .card:hover p { color: #ccc; }
    </style>
</head>
<body>
<nav>
    <span>Backoffice - Clube de Ténis e Pádel</span>
    <div class="nav-links">
        <span>Ola, <?= $_SESSION['nome'] ?> (<?= $_SESSION['tipo'] ?>)</span>
        <a href="../index.php">Ver Site</a>
        <a href="../logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <div class="boas-vindas">
        <h2>Painel de Administracao</h2>
        <p>Bem-vindo ao backoffice do clube. Gere tudo a partir daqui.</p>
    </div>

    <div class="stats">
        <div class="stat">
            <h3><?= $total_reservas ?></h3>
            <p>Reservas ativas</p>
        </div>
        <div class="stat">
            <h3><?= $total_atletas ?></h3>
            <p>Atletas ativos</p>
        </div>
    </div>

    <h3 class="titulo">O que queres gerir?</h3>
    <div class="cards">
        <a href="campos.php" class="card">
            <h3>Campos</h3>
            <p>Gerir campos do clube</p>
        </a>
        <a href="atletas.php" class="card">
            <h3>Atletas</h3>
            <p>Gerir atletas</p>
        </a>
        <a href="admin.reservas.php" class="card">
            <h3>Reservas</h3>
            <p>Ver e gerir reservas</p>
        </a>
        <a href="pagamentos.php" class="card">
            <h3>Pagamentos</h3>
            <p>Registar pagamentos</p>
        </a>
        <a href="relatorios.php" class="card">
            <h3>Relatorios</h3>
            <p>Estatisticas do clube</p>
        </a>
        <?php if ($_SESSION['tipo'] === 'gestor'): ?>
        <a href="operadores.php" class="card">
            <h3>Operadores</h3>
            <p>Gerir staff do clube</p>
        </a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>