<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarOperador();

// fazer check-in
if (isset($_GET['checkin'])) {
    $id = $_GET['checkin'];
    $stmt = $pdo->prepare("UPDATE reserva SET checkin = 1 WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: admin.reservas.php');
    exit();
}

// cancelar reserva
if (isset($_GET['cancelar'])) {
    $id = $_GET['cancelar'];
    $stmt = $pdo->prepare("UPDATE reserva SET estado_reserva = 'cancelada' WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: admin.reservas.php');
    exit();
}

// buscar todas as reservas
$reservas = $pdo->query("SELECT r.*, a.nome as nome_atleta FROM reserva r JOIN atleta a ON r.atleta_id = a.id ORDER BY r.data_jogo DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Reservas - Backoffice</title>
    <style>
        body { font-family: Arial; margin: 0; background: #f5f5f5; }
        nav { background: green; padding: 10px 20px; color: white; display: flex; justify-content: space-between; }
        nav a { color: white; margin-left: 10px; text-decoration: none; }
        .container { max-width: 1100px; margin: 30px auto; padding: 20px; }
        h2 { color: green; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 8px 10px; border: 1px solid #ddd; font-size: 12px; text-align: left; }
        th { background: green; color: white; }
        .ativa { color: green; font-weight: bold; }
        .cancelada { color: red; }
        .concluida { color: gray; }
        .btn { padding: 4px 8px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 11px; }
        .btn-checkin { background: blue; color: white; }
        .btn-cancelar { background: red; color: white; }
    </style>
</head>
<body>
<nav>
    <span>Backoffice - Reservas</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="../logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <h2>Gerir Reservas</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Atleta</th>
            <th>Campo</th>
            <th>Data</th>
            <th>Hora Inicio</th>
            <th>Hora Fim</th>
            <th>Iluminacao</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Check-in</th>
            <th>Acoes</th>
        </tr>
        <?php foreach ($reservas as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['nome_atleta'] ?></td>
            <td><?= $r['campo_escolhido'] ?></td>
            <td><?= $r['data_jogo'] ?></td>
            <td><?= $r['hora_inicio'] ?></td>
            <td><?= $r['hora_fim'] ?></td>
            <td><?= $r['suplemento_iluminacao'] ? 'Sim' : 'Nao' ?></td>
            <td><?= number_format($r['valor_total'], 2) ?>€</td>
            <td class="<?= $r['estado_reserva'] ?>"><?= $r['estado_reserva'] ?></td>
            <td><?= $r['checkin'] ? 'Confirmado' : 'Pendente' ?></td>
            <td>
                <?php if ($r['estado_reserva'] === 'ativa' && !$r['checkin']): ?>
                    <a href="admin.reservas.php?checkin=<?= $r['id'] ?>" class="btn btn-checkin">Check-in</a>
                <?php endif; ?>
                <?php if ($r['estado_reserva'] === 'ativa'): ?>
                    <a href="admin.reservas.php?cancelar=<?= $r['id'] ?>" class="btn btn-cancelar" onclick="return confirm('Cancelar esta reserva?')">Cancelar</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>