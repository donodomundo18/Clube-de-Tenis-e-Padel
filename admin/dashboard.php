<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarOperador();

$total_reservas = $pdo->query("SELECT COUNT(*) FROM reserva WHERE estado_reserva = 'ativa'")->fetchColumn();
$total_atletas = $pdo->query("SELECT COUNT(*) FROM atleta WHERE estado_atleta = 'ativo'")->fetchColumn();
$total_campos = $pdo->query("SELECT COUNT(*) FROM campo WHERE estado_campo = 'disponivel'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Backoffice - Clube de Ténis e Pádel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f4f4f4; }
        nav { background: #1e3a5f; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; }
        nav span { color: white; font-size: 16px; font-weight: bold; }
        nav a { color: white; text-decoration: none; margin-left: 8px; font-size: 13px; padding: 7px 14px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.3); }
        nav a:hover { background: #f47c3c; border-color: #f47c3c; }
        .hero { background: #1e3a5f; color: white; text-align: center; padding: 40px 20px; }
        .hero h1 { font-size: 22px; margin-bottom: 6px; }
        .hero p { font-size: 13px; color: #aac; }
        .container { max-width: 950px; margin: 30px auto; padding: 0 20px; }
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
        .stat { background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; text-align: center; }
        .stat h3 { color: #1e3a5f; font-size: 30px; margin-bottom: 5px; }
        .stat p { color: #888; font-size: 12px; }
        .secao-titulo { font-size: 15px; color: #1e3a5f; margin-bottom: 15px; font-weight: bold; }
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .card { background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; text-decoration: none; color: #333; text-align: center; }
        .card:hover { background: #1e3a5f; color: white; }
        .card h3 { font-size: 15px; margin-bottom: 6px; }
        .card p { font-size: 12px; color: #777; }
        .card:hover p { color: #aac; }
    </style>
</head>
<body>
<nav>
    <span>Backoffice - Clube de Ténis e Pádel</span>
    <div>
        <a href="../index.php">Ver Site</a>
        <a href="../logout.php">Logout</a>
    </div>
</nav>

<div class="hero">
    <h1>Painel de Administração</h1>
    <p>Olá, <?= $_SESSION['nome'] ?>! Gere tudo a partir daqui.</p>
</div>

<div class="container" style="margin-top:25px;">
    <p class="secao-titulo">Resumo do clube</p>
    <div class="stats">
        <div class="stat">
            <h3><?= $total_reservas ?></h3>
            <p>Reservas ativas</p>
        </div>
        <div class="stat">
            <h3><?= $total_atletas ?></h3>
            <p>Atletas ativos</p>
        </div>
        <div class="stat">
            <h3><?= $total_campos ?></h3>
            <p>Campos disponíveis</p>
        </div>
    </div>

    <p class="secao-titulo">O que queres gerir?</p>
    <div class="cards">
        <a href="campos.php" class="card">
            <h3>Campos</h3>
            <p>Gerir os campos do clube</p>
        </a>
        <a href="atletas.php" class="card">
            <h3>Atletas</h3>
            <p>Gerir atletas registados</p>
        </a>
        <a href="admin.reservas.php" class="card">
            <h3>Reservas</h3>
            <p>Ver e gerir reservas e check-in</p>
        </a>
        <a href="pagamentos.php" class="card">
            <h3>Pagamentos</h3>
            <p>Registar e consultar pagamentos</p>
        </a>
        <a href="relatorios.php" class="card">
            <h3>Relatórios</h3>
            <p>Estatísticas e relatórios do clube</p>
        </a>
        <?php if ($_SESSION['tipo'] === 'gestor'): ?>
        <a href="operadores.php" class="card">
            <h3>Operadores</h3>
            <p>Gerir gestores e rececionistas</p>
        </a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>