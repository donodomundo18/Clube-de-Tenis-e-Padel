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
        body { font-family: Arial; background: #f4f4f4; }
        nav { background: #1e3a5f; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; }
        nav span { color: white; font-size: 16px; font-weight: bold; }
        nav a { color: white; text-decoration: none; margin-left: 12px; font-size: 13px; padding: 6px 12px; border-radius: 4px; }
        nav a:hover { background: #f47c3c; }
        .hero { background: #1e3a5f; color: white; text-align: center; padding: 55px 20px; }
        .hero h1 { font-size: 24px; margin-bottom: 8px; }
        .hero p { font-size: 13px; color: #aac; margin-bottom: 8px; }
        .hero .nome { font-size: 15px; color: white; margin-bottom: 20px; font-weight: bold; }
        .btn { background: #f47c3c; color: white; padding: 11px 28px; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #d4622c; }
        .container { max-width: 900px; margin: 35px auto; padding: 0 20px; }
        .secao-titulo { font-size: 15px; color: #1e3a5f; margin-bottom: 15px; font-weight: bold; }
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
        .card { background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; }
        .card h3 { color: #1e3a5f; font-size: 15px; margin-bottom: 6px; }
        .card p { font-size: 12px; color: #777; line-height: 1.5; }
        .card-link { background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; text-decoration: none; color: #333; display: block; text-align: center; }
        .card-link:hover { background: #1e3a5f; color: white; }
        .card-link:hover p { color: #aac; }
        .card-link h3 { font-size: 15px; margin-bottom: 6px; }
        .card-link p { font-size: 12px; color: #777; }
    </style>
</head>
<body>
<nav>
    <span>Clube de Ténis e Pádel</span>
    <div>
        <?php if ($_SESSION['tipo'] === 'atleta'): ?>
            <a href="index.php">Inicio</a>
            <a href="reservas.php">As minhas reservas</a>
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
    <h1>Clube de Ténis e Pádel</h1>
    <p>Gere as tuas reservas de campo de forma simples e rapida.</p>
    <p class="nome">Ola, <?= $_SESSION['nome'] ?>!</p>
    <a href="nova_reserva.php" class="btn">Reservar Campo</a>
</div>

<div class="container">
    <p class="secao-titulo">O que podes fazer</p>
    <div class="cards">
        <a href="nova_reserva.php" class="card-link">
            <h3>Nova Reserva</h3>
            <p>Escolhe o tipo de campo, data e horario e faz a tua reserva.</p>
        </a>
        <a href="reservas.php" class="card-link">
            <h3>As Minhas Reservas</h3>
            <p>Consulta todas as tuas reservas ativas, passadas e canceladas.</p>
        </a>
        <a href="sobre.php" class="card-link">
            <h3>Sobre o Clube</h3>
            <p>Conhece os nossos campos, horarios e contactos.</p>
        </a>
    </div>

    <p class="secao-titulo">Tipos de campo disponiveis</p>
    <div class="cards">
        <div class="card">
            <h3>Padel Coberto</h3>
            <p>Campo interior com piso sintetico. Disponivel todos os dias.</p>
        </div>
        <div class="card">
            <h3>Tenis Terra Batida</h3>
            <p>Campo classico de terra batida para todos os niveis.</p>
        </div>
        <div class="card">
            <h3>Tenis Rapido</h3>
            <p>Campo de piso rapido coberto ideal para jogos competitivos.</p>
        </div>
    </div>
</div>

<?php else: ?>
<div class="hero">
    <h1>Area de Gestao</h1>
    <p>Acede ao painel de administracao do clube.</p>
    <p class="nome">Ola, <?= $_SESSION['nome'] ?>!</p>
    <a href="admin/dashboard.php" class="btn">Ir para o Backoffice</a>
</div>
<?php endif; ?>

</body>
</html>