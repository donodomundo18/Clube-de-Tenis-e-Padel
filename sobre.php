<?php
require_once 'config/db.php';
require_once 'includes/session.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Sobre Nos - Clube de Tenis e Padel</title>
    <style>
        body { font-family: Arial; margin: 0; background: #f5f5f5; }
        nav { background: green; padding: 10px 20px; color: white; display: flex; justify-content: space-between; }
        nav a { color: white; margin-left: 10px; text-decoration: none; }
        .container { max-width: 800px; margin: 30px auto; padding: 20px; }
        .caixa { background: white; padding: 25px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd; }
        h2 { color: green; }
        h3 { color: #333; }
        p { color: #555; line-height: 1.6; }
    </style>
</head>
<body>
<nav>
    <span>Clube de Tenis e Padel</span>
    <div>
        <a href="index.php">Inicio</a>
        <a href="sobre.php">Sobre Nos</a>
        <?php if(estaLogado()): ?>
            <a href="nova_reserva.php">Nova Reserva</a>
            <a href="reservas.php">As minhas reservas</a>
            <a href="logout.php">Sair</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <div class="caixa">
        <h2>Sobre o Clube</h2>
        <p>O nosso clube de tenis e padel existe para proporcionar um espaco de qualidade para todos os amantes destes desportos. Somos um clube de pequeno porte mas com muita dedicacao aos nossos atletas.</p>
    </div>

    <div class="caixa">
        <h3>Os nossos campos</h3>
        <p>Temos campos de padel cobertos e descobertos, e campos de tenis em terra batida e piso rapido. Todos os campos estao disponiveis para reserva online.</p>
    </div>

    <div class="caixa">
        <h3>Horario de funcionamento</h3>
        <p>Segunda a Domingo: 08h00 - 23h00</p>
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