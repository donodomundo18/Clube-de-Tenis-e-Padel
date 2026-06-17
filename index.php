<?php
require_once 'config/db.php';
require_once 'includes/session.php';

if (!estaLogado()) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Clube de Ténis e Pádel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #eef2ee; }
        nav { background: #1a5c1a; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; }
        nav span { color: white; font-size: 16px; font-weight: bold; }
        nav a { color: white; text-decoration: none; margin-left: 10px; padding: 6px 12px; background: rgba(255,255,255,0.2); border-radius: 4px; font-size: 13px; }
        nav a:hover { background: rgba(255,255,255,0.4); }
        .container { max-width: 850px; margin: 35px auto; padding: 0 20px; }
        .topo { background: white; padding: 20px 25px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #1a5c1a; }
        .topo h2 { color: #1a5c1a; font-size: 20px; }
        .topo p { color: #777; font-size: 13px; margin-top: 4px; }
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .card { background: white; padding: 22px; border-radius: 8px; text-align: center; text-decoration: none; color: #333; border: 1px solid #ddd; }
        .card:hover { background: #1a5c1a; color: white; }
        .card h3 { font-size: 15px; margin-bottom: 6px; }
        .card p { font-size: 12px; color: #888; }
        .card:hover p { color: #ccc; }
    </style>
</head>
<body>
<nav>
    <span>Clube de Ténis e Pádel</span>
    <div>
        <?php if ($_SESSION['tipo'] === 'atleta'): ?>
            <a href="reservas.php">As minhas reservas</a>
            <a href="nova_reserva.php">Nova reserva</a>
            <a href="sobre.php">Sobre nos</a>
        <?php else: ?>
            <a href="admin/dashboard.php">Backoffice</a>
        <?php endif; ?>
        <a href="logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <div class="topo">
        <h2>Bem-vindo, <?= $_SESSION['nome'] ?>!</h2>
        <p>Clube de Ténis e Pádel — Gestao de Reservas de Campos</p>
    </div>

    <?php if ($_SESSION['tipo'] === 'atleta'): ?>
    <div class="cards">
        <a href="nova_reserva.php" class="card">
            <h3>Nova Reserva</h3>
            <p>Reserva um campo de tenis ou padel</p>
        </a>
        <a href="reservas.php" class="card">
            <h3>As Minhas Reservas</h3>
            <p>Consulta e gere as tuas reservas</p>
        </a>
        <a href="sobre.php" class="card">
            <h3>Sobre Nos</h3>
            <p>Conhece o nosso clube</p>
        </a>
    </div>
    <?php else: ?>
    <div class="cards">
        <a href="admin/dashboard.php" class="card">
            <h3>Ir para o Backoffice</h3>
            <p>Gerir o clube</p>
        </a>
    </div>
    <?php endif; ?>
</div>
</body>
</html>