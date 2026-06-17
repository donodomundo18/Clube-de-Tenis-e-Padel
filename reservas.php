<?php
require_once 'config/db.php';
require_once 'includes/session.php';

if (!estaLogado() || $_SESSION['tipo'] !== 'atleta') {
    header('Location: login.php');
    exit();
}

// cancelar reserva
if (isset($_GET['cancelar'])) {
    $id = $_GET['cancelar'];
    $stmt = $pdo->prepare("SELECT * FROM reserva WHERE id = ? AND atleta_id = ?");
    $stmt->execute([$id, $_SESSION['atleta_id']]);
    $reserva = $stmt->fetch();

    if ($reserva) {
        $data_hora_inicio = $reserva['data_jogo'] . ' ' . $reserva['hora_inicio'];
        $diferenca = strtotime($data_hora_inicio) - time();

        if ($diferenca < 86400) {
            $erro = 'Não podes cancelar esta reserva! Faltam menos de 24 horas para o jogo.';
        } else {
            $stmt = $pdo->prepare("UPDATE reserva SET estado_reserva = 'cancelada' WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: reservas.php');
            exit();
        }
    }
}

// buscar reservas do atleta
$stmt = $pdo->prepare("SELECT * FROM reserva WHERE atleta_id = ? ORDER BY data_jogo DESC");
$stmt->execute([$_SESSION['atleta_id']]);
$reservas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>As Minhas Reservas - Clube de Ténis e Pádel</title>
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
        .aviso { background: #fff3cd; color: #856404; padding: 10px 15px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; border-left: 4px solid #f47c3c; }
        .erro { background: #ffe0e0; color: red; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; border-left: 4px solid red; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 13px; text-align: left; }
        th { background: #1e3a5f; color: white; }
        tr:hover { background: #f9f9f9; }
        .ativa { color: #1a6b1a; font-weight: bold; }
        .cancelada { color: red; }
        .concluida { color: gray; }
        .btn-cancelar { padding: 5px 10px; background: red; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; }
        .btn-cancelar:hover { background: darkred; }
        .sem-reservas { background: white; padding: 40px; text-align: center; border-radius: 8px; color: #888; }
        .btn-nova { display: inline-block; margin-top: 15px; padding: 10px 20px; background: #f47c3c; color: white; border-radius: 5px; text-decoration: none; font-size: 13px; }
        .btn-nova:hover { background: #d4622c; }
    </style>
</head>
<body>
<nav>
    <span>Clube de Ténis e Pádel</span>
    <div>
        <a href="index.php">Início</a>
        <a href="reservas.php">As minhas reservas</a>
        <a href="nova_reserva.php">Nova Reserva</a>
        <a href="sobre.php">Sobre nós</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="topo">
    <h1>As Minhas Reservas</h1>
    <p>Consulta e gere todas as tuas reservas</p>
</div>

<div class="container">
    <div class="aviso">
        Atenção: só podes cancelar uma reserva até 24 horas antes do início do jogo.
    </div>

    <?php if (isset($erro)): ?>
        <div class="erro"><?= $erro ?></div>
    <?php endif; ?>

    <?php if (count($reservas) === 0): ?>
        <div class="sem-reservas">
            <p>Ainda não tens nenhuma reserva feita.</p>
            <a href="nova_reserva.php" class="btn-nova">Fazer primeira reserva</a>
        </div>
    <?php else: ?>
    <table>
        <tr>
            <th>Tipo de Campo</th>
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
            <td><?= $r['campo_escolhido'] ?></td>
            <td><?= $r['data_jogo'] ?></td>
            <td><?= $r['hora_inicio'] ?></td>
            <td><?= $r['hora_fim'] ?></td>
            <td><?= $r['suplemento_iluminacao'] ? 'Sim' : 'Não' ?></td>
            <td><?= number_format($r['valor_total'], 2) ?>€</td>
            <td class="<?= $r['estado_reserva'] ?>"><?= $r['estado_reserva'] ?></td>
            <td><?= $r['checkin'] ? 'Confirmado' : 'Pendente' ?></td>
            <td>
                <?php
                $data_hora = $r['data_jogo'] . ' ' . $r['hora_inicio'];
                $diff = strtotime($data_hora) - time();
                if ($r['estado_reserva'] === 'ativa' && $diff > 86400):
                ?>
                    <a href="reservas.php?cancelar=<?= $r['id'] ?>" class="btn-cancelar" onclick="return confirm('Tens a certeza que queres cancelar esta reserva?')">Cancelar</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
</body>
</html>