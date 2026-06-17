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
        body { font-family: Arial; background: #f0f4f8; }
        nav { background: #1a2744; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; }
        nav span { color: white; font-size: 16px; font-weight: bold; }
        nav a { color: white; text-decoration: none; margin-left: 15px; font-size: 13px; }
        nav a:hover { text-decoration: underline; }
        .hero { background: #1a2744; color: white; text-align: center; padding: 60px 20px; }
        .hero h1 { font-size: 26px; margin-bottom: 10px; }
        .hero p { font-size: 14px; color: #aab; margin-bottom: 25px; }
        .hero .nome { font-size: 15px; color: #ccd; margin-bottom: 20px; }
        .btn-reserva { background: #e63946; color: white; padding: 12px 30px; border: none; border-radius: 5px; font-size: 15px; cursor: pointer; text-decoration: none; }
        .btn-reserva:hover { background: #c1121f; }
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .card { background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; }
        .card h3 { color: #1a2744; font-size: 15px; margin-bottom: 8px; }
        .card p { font-size: 13px; color: #777; }
    </style>
</head>
<body>
<nav>
    <span>Clube de Ténis e Pádel</span>
    <div>
        <?php if ($_SESSION['tipo'] === 'atleta'): ?>
            <a href="index.php">Inicio</a>
            <a href="reservas.php">Reservas</a>
            <a href="nova_reserva.php">Nova Reserva</a>
            <a href="sobre.php">Sobre nos</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="admin/dashboard.php">Backoffice</a>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </div>
</nav>

<?php if ($_SESSION['tipo'] === 'atleta'): ?>
<div class="hero">
    <h1>Bem-vindo ao Clube de Ténis e Pádel</h1>
    <p>Reserve o seu campo de ténis ou pádel de forma rápida e simples.</p>
    <p class="nome">Bem-vindo, <?= $_SESSION['nome'] ?>!</p>
    <a href="nova_reserva.php" class="btn-reserva">Fazer Reserva</a>
</div>

<div class="container">
    <div class="cards">
        <div class="card">
            <h3>Ténis</h3>
            <p>Campos de terra batida e piso rápido disponíveis para reserva.</p>
        </div>
        <div class="card">
            <h3>Pádel</h3>
            <p>Campos cobertos e descobertos para todos os níveis.</p>
        </div>
        <div class="card">
            <h3>Equipamentos</h3>
            <p>Aluguer de raquetes e bolas disponível em todos os campos.</p>
        </div>
    </div>
</div>

<?php else: ?>
<div class="hero">
    <h1>Painel de Gestao</h1>
    <p>Bem-vindo, <?= $_SESSION['nome'] ?>!</p>
    <a href="admin/dashboard.php" class="btn-reserva">Ir para o Backoffice</a>
</div>
<?php endif; ?>

</body>
</html>