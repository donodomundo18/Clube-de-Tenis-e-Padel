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
        body { font-family: Arial, sans-serif; background: #f0f0f0; }
        nav { background: #2c7a2c; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        nav h1 { color: white; font-size: 20px; }
        nav a { color: white; text-decoration: none; margin-left: 15px; }
        nav a:hover { text-decoration: underline; }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .boas-vindas { background: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .boas-vindas h2 { color: #2c7a2c; margin-bottom: 10px; }
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .card { background: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333; }
        .card:hover { background: #2c7a2c; color: white; transform: translateY(-3px); transition: all 0.3s; }
        .card .icone { font-size: 40px; margin-bottom: 10px; }
        .card h3 { margin-bottom: 8px; }
        .card p { font-size: 13px; opacity: 0.8; }
    </style>
</head>
<body>
<nav>
    <h1>🎾 Clube de Ténis e Pádel</h1>
    <div>
        <span style="color:white">Olá, <?= $_SESSION['nome'] ?>!</span>
        <?php if ($_SESSION['tipo'] === 'atleta'): ?>
            <a href="reservas.php">As minhas reservas</a>
            <a href="nova_reserva.php">Nova reserva</a>
        <?php else: ?>
            <a href="admin/dashboard.php">Backoffice</a>
        <?php endif; ?>
        <a href="logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <div class="boas-vindas">
        <h2>Bem-vindo, <?= $_SESSION['nome'] ?>! 👋</h2>
        <p>Clube de Ténis e Pádel — Gestão de Reservas de Campos</p>
    </div>

    <?php if ($_SESSION['tipo'] === 'atleta'): ?>
    <div class="cards">
        <a href="nova_reserva.php" class="card">
            <div class="icone">📅</div>
            <h3>Nova Reserva</h3>
            <p>Reserva um campo de ténis ou pádel</p>
        </a>
        <a href="reservas.php" class="card">
            <div class="icone">📋</div>
            <h3>As Minhas Reservas</h3>
            <p>Consulta e gere as tuas reservas</p>
        </a>
        <a href="sobre.php" class="card">
            <div class="icone">ℹ️</div>
            <h3>Sobre Nós</h3>
            <p>Conhece o nosso clube</p>
        </a>
    </div>
    <?php else: ?>
    <div class="cards">
        <a href="admin/dashboard.php" class="card">
            <div class="icone">⚙️</div>
            <h3>Backoffice</h3>
            <p>Gerir o clube</p>
        </a>
    </div>
    <?php endif; ?>
</div>
</body>
</html>