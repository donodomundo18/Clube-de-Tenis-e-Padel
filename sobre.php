<?php
require_once 'config/db.php';
require_once 'includes/session.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Sobre Nós - Clube de Ténis e Pádel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f4f4f4; }
        nav { background: #1e3a5f; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; }
        nav span { color: white; font-size: 16px; font-weight: bold; }
        nav a { color: white; text-decoration: none; margin-left: 8px; font-size: 13px; padding: 7px 14px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.3); }
        nav a:hover { background: #f47c3c; border-color: #f47c3c; }
        .topo { background: #1e3a5f; color: white; padding: 25px; text-align: center; }
        .topo h1 { font-size: 20px; }
        .topo p { font-size: 13px; color: #aac; margin-top: 5px; }
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; }
        .caixa { background: white; padding: 22px 25px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #ddd; border-left: 4px solid #1e3a5f; }
        .caixa h2 { color: #1e3a5f; font-size: 17px; margin-bottom: 10px; }
        .caixa h3 { color: #1e3a5f; font-size: 15px; margin-bottom: 8px; }
        .caixa p { color: #555; line-height: 1.6; font-size: 13px; }
        .btn-reserva { display: inline-block; margin-top: 20px; padding: 11px 25px; background: #f47c3c; color: white; border-radius: 5px; text-decoration: none; font-size: 14px; }
        .btn-reserva:hover { background: #d4622c; }
    </style>
</head>
<body>
<nav>
    <span>Clube de Ténis e Pádel</span>
    <div>
        <a href="index.php">Início</a>
        <a href="sobre.php">Sobre nós</a>
        <?php if(estaLogado()): ?>
            <a href="nova_reserva.php">Nova Reserva</a>
            <a href="reservas.php">As minhas reservas</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<div class="topo">
    <h1>Sobre o Clube</h1>
    <p>Conhece a nossa história e os nossos serviços</p>
</div>

<div class="container">
    <div class="caixa">
        <h2>Quem somos</h2>
        <p>O nosso clube de ténis e pádel existe para proporcionar um espaço de qualidade para todos os amantes destes desportos. Somos um clube de pequeno porte mas com muita dedicação aos nossos atletas.</p>
        <?php if(estaLogado()): ?>
            <a href="nova_reserva.php" class="btn-reserva">Reservar Campo</a>
        <?php endif; ?>
    </div>

    <div class="caixa">
        <h3>Os nossos campos</h3>
        <p>Temos campos de pádel cobertos e descobertos, e campos de ténis em terra batida e piso rápido. Todos os campos estão disponíveis para reserva online.</p>
    </div>

    <div class="caixa">
        <h3>Horário de funcionamento</h3>
        <p>Segunda a Domingo: 08h00 — 23h00</p>
    </div>

    <div class="caixa">
        <h3>Contactos</h3>
        <p>Email: clube@tenis-padel.pt</p>
        <p>Telefone: 210 000 000</p>
        <p>Morada: Lisboa, Portugal</p>
    </div>
</div>
</body>
</html>