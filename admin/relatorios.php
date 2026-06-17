<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarOperador();

$total_ativas = $pdo->query("SELECT COUNT(*) FROM reserva WHERE estado_reserva = 'ativa'")->fetchColumn();
$total_canceladas = $pdo->query("SELECT COUNT(*) FROM reserva WHERE estado_reserva = 'cancelada'")->fetchColumn();
$total_atletas = $pdo->query("SELECT COUNT(*) FROM atleta WHERE estado_atleta = 'ativo'")->fetchColumn();
$receita = $pdo->query("SELECT SUM(montante) FROM pagamento")->fetchColumn();
$por_tipo = $pdo->query("SELECT campo_escolhido, COUNT(*) as total FROM reserva WHERE estado_reserva = 'ativa' GROUP BY campo_escolhido")->fetchAll();
$pagamentos_recentes = $pdo->query("SELECT p.*, a.nome as nome_atleta, r.campo_escolhido FROM pagamento p JOIN reserva r ON p.reserva_id = r.id JOIN atleta a ON r.atleta_id = a.id ORDER BY p.data_pagamento DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatórios - Backoffice</title>
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
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px; }
        .stat { background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; text-align: center; }
        .stat h3 { color: #1e3a5f; font-size: 28px; margin-bottom: 5px; }
        .stat p { font-size: 12px; color: #888; }
        .secao-titulo { color: #1e3a5f; font-size: 15px; margin: 25px 0 12px 0; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; margin-bottom: 25px; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 13px; text-align: left; }
        th { background: #1e3a5f; color: white; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
<nav>
    <span>Backoffice - Clube de Ténis e Pádel</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>
</nav>

<div class="topo">
    <h1>Relatórios e Estatísticas</h1>
    <p>Consulta as estatísticas e dados do clube</p>
</div>

<div class="container">
    <div class="stats">
        <div class="stat">
            <h3><?= $total_ativas ?></h3>
            <p>Reservas ativas</p>
        </div>
        <div class="stat">
            <h3><?= $total_canceladas ?></h3>
            <p>Reservas canceladas</p>
        </div>
        <div class="stat">
            <h3><?= $total_atletas ?></h3>
            <p>Atletas ativos</p>
        </div>
        <div class="stat">
            <h3><?= number_format($receita ?? 0, 2) ?>€</h3>
            <p>Receita total</p>
        </div>
    </div>

    <p class="secao-titulo">Reservas por tipo de campo</p>
    <table>
        <tr>
            <th>Tipo de Campo</th>
            <th>Total de Reservas</th>
        </tr>
        <?php foreach ($por_tipo as $t): ?>
        <tr>
            <td><?= $t['campo_escolhido'] ?></td>
            <td><?= $t['total'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p class="secao-titulo">Últimos pagamentos</p>
    <table>
        <tr>
            <th>Atleta</th>
            <th>Campo</th>
            <th>Montante</th>
            <th>Tipo</th>
            <th>Data</th>
        </tr>
        <?php foreach ($pagamentos_recentes as $p): ?>
        <tr>
            <td><?= $p['nome_atleta'] ?></td>
            <td><?= $p['campo_escolhido'] ?></td>
            <td><?= number_format($p['montante'], 2) ?>€</td>
            <td><?= $p['tipo'] ?></td>
            <td><?= $p['data_pagamento'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>