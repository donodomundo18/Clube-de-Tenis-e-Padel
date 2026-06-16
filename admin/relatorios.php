<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarOperador();

// total de reservas ativas
$total_ativas = $pdo->query("SELECT COUNT(*) FROM reserva WHERE estado_reserva = 'ativa'")->fetchColumn();

// total de reservas canceladas
$total_canceladas = $pdo->query("SELECT COUNT(*) FROM reserva WHERE estado_reserva = 'cancelada'")->fetchColumn();

// total de atletas ativos
$total_atletas = $pdo->query("SELECT COUNT(*) FROM atleta WHERE estado_atleta = 'ativo'")->fetchColumn();

// receita total
$receita = $pdo->query("SELECT SUM(montante) FROM pagamento")->fetchColumn();

// reservas por tipo de campo
$por_tipo = $pdo->query("SELECT campo_escolhido, COUNT(*) as total FROM reserva WHERE estado_reserva = 'ativa' GROUP BY campo_escolhido")->fetchAll();

// pagamentos recentes
$pagamentos_recentes = $pdo->query("SELECT p.*, a.nome as nome_atleta, r.campo_escolhido FROM pagamento p JOIN reserva r ON p.reserva_id = r.id JOIN atleta a ON r.atleta_id = a.id ORDER BY p.data_pagamento DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatorios - Backoffice</title>
    <style>
        body { font-family: Arial; margin: 0; background: #f5f5f5; }
        nav { background: green; padding: 10px 20px; color: white; display: flex; justify-content: space-between; }
        nav a { color: white; margin-left: 10px; text-decoration: none; }
        .container { max-width: 1000px; margin: 30px auto; padding: 20px; }
        h2 { color: green; }
        h3 { color: #444; margin-top: 30px; }
        .stats { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 25px; }
        .stat { background: white; padding: 20px 25px; border-radius: 8px; border: 1px solid #ddd; text-align: center; min-width: 150px; }
        .stat h3 { color: green; font-size: 30px; margin: 0 0 5px 0; }
        .stat p { margin: 0; font-size: 12px; color: #888; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 10px; }
        th, td { padding: 9px 10px; border: 1px solid #ddd; font-size: 13px; text-align: left; }
        th { background: green; color: white; }
    </style>
</head>
<body>
<nav>
    <span>Backoffice - Relatorios</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="../logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <h2>Relatorios e Estatisticas</h2>

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

    <h3>Reservas por tipo de campo</h3>
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

    <h3>Ultimos pagamentos</h3>
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