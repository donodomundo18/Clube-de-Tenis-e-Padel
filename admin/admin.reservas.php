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

// buscar todas as reservas com nome do atleta
$reservas = $pdo->query("SELECT r.*, a.nome as nome_atleta FROM reserva r JOIN atleta a ON r.atleta_id = a.id ORDER BY r.data_jogo DESC")->fetchAll();

// contar reservas ativas
$total_ativas = $pdo->query("SELECT COUNT(*) FROM reserva WHERE estado_reserva = 'ativa'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Reservas - Backoffice</title>
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
        .topo .badge { background: #f47c3c; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-top: 8px; display: inline-block; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 12px; text-align: left; vertical-align: middle; }
        th { background: #1e3a5f; color: white; }
        tr:hover { background: #f9f9f9; }
        .ativa { color: #1a6b1a; font-weight: bold; }
        .cancelada { color: red; }
        .concluida { color: gray; }
        .acoes { display: flex; gap: 5px; flex-wrap: wrap; }
        .btn { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 11px; display: inline-block; white-space: nowrap; }
        .btn-checkin { background: #1e3a5f; color: white; }
        .btn-checkin:hover { background: #162d4a; }
        .btn-cancelar { background: red; color: white; }
        .btn-cancelar:hover { background: darkred; }
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
    <h1>Gerir Reservas</h1>
    <p>Consulta, gere e efetua check-in de todas as reservas do clube</p>
    <span class="badge"><?= $total_ativas ?> reservas ativas</span>
</div>

<div class="container">
    <table>
        <tr>
            <th>ID</th>
            <th>Atleta</th>
            <th>Campo</th>
            <th>Data</th>
            <th>Hora Início</th>
            <th>Hora Fim</th>
            <th>Iluminação</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Check-in</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($reservas as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['nome_atleta'] ?></td>
            <td><?= $r['campo_escolhido'] ?></td>
            <td><?= $r['data_jogo'] ?></td>
            <td><?= $r['hora_inicio'] ?></td>
            <td><?= $r['hora_fim'] ?></td>
            <td><?= $r['suplemento_iluminacao'] ? 'Sim' : 'Não' ?></td>
            <td><?= number_format($r['valor_total'], 2) ?>€</td>
            <td class="<?= $r['estado_reserva'] ?>"><?= $r['estado_reserva'] ?></td>
            <td><?= $r['checkin'] ? 'Confirmado' : 'Pendente' ?></td>
            <td>
                <div class="acoes">
                    <?php if ($r['estado_reserva'] === 'ativa' && !$r['checkin']): ?>
                        <a href="admin.reservas.php?checkin=<?= $r['id'] ?>" class="btn btn-checkin">Check-in</a>
                    <?php endif; ?>
                    <?php if ($r['estado_reserva'] === 'ativa'): ?>
                        <a href="admin.reservas.php?cancelar=<?= $r['id'] ?>" class="btn btn-cancelar" onclick="return confirm('Tens a certeza que queres cancelar esta reserva?')">Cancelar</a>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>