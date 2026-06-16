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
    // verificar se e do atleta e se ainda da para cancelar (24h antes)
    $stmt = $pdo->prepare("SELECT * FROM reserva WHERE id = ? AND atleta_id = ?");
    $stmt->execute([$id, $_SESSION['atleta_id']]);
    $reserva = $stmt->fetch();

    if ($reserva) {
        $data_hora_inicio = $reserva['data_jogo'] . ' ' . $reserva['hora_inicio'];
        $diferenca = strtotime($data_hora_inicio) - time();

        if ($diferenca < 86400) {
            $erro = 'Nao podes cancelar esta reserva! Faltam menos de 24 horas para o jogo.';
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
        body { font-family: Arial; margin: 0; background: #f5f5f5; }
        nav { background: green; padding: 10px 20px; color: white; display: flex; justify-content: space-between; }
        nav a { color: white; margin-left: 10px; text-decoration: none; }
        .container { max-width: 900px; margin: 30px auto; padding: 20px; }
        h2 { color: green; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #ddd; font-size: 13px; text-align: left; }
        th { background: green; color: white; }
        .ativa { color: green; font-weight: bold; }
        .cancelada { color: red; }
        .concluida { color: gray; }
        .btn { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; }
        .btn-cancelar { background: red; color: white; }
        .aviso { background: #fff8e0; color: #888; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 12px; }
        .erro { background: #ffe0e0; color: red; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; }
        .sem-reservas { background: white; padding: 30px; text-align: center; border-radius: 8px; color: #888; }
    </style>
</head>
<body>
<nav>
    <span>Clube de Ténis e Pádel</span>
    <div>
        <span>Ola, <?= $_SESSION['nome'] ?></span>
        <a href="index.php">Inicio</a>
        <a href="nova_reserva.php">Nova Reserva</a>
        <a href="logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <h2>As Minhas Reservas</h2>

    <div class="aviso">
        Atencao: so podes cancelar uma reserva ate 24 horas antes do inicio do jogo.
    </div>

    <?php if (isset($erro)): ?>
        <div class="erro"><?= $erro ?></div>
    <?php endif; ?>

    <?php if (count($reservas) === 0): ?>
        <div class="sem-reservas">
            <p>Ainda nao tens nenhuma reserva feita.</p>
            <a href="nova_reserva.php" style="color:green;">Clica aqui para fazer a tua primeira reserva!</a>
        </div>
    <?php else: ?>
    <table>
        <tr>
            <th>Tipo de Campo</th>
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
            <td><?= $r['campo_escolhido'] ?></td>
            <td><?= $r['data_jogo'] ?></td>
            <td><?= $r['hora_inicio'] ?></td>
            <td><?= $r['hora_fim'] ?></td>
            <td><?= $r['suplemento_iluminacao'] ? 'Sim' : 'Nao' ?></td>
            <td><?= number_format($r['valor_total'], 2) ?>€</td>
            <td class="<?= $r['estado_reserva'] ?>"><?= $r['estado_reserva'] ?></td>
            <td><?= $r['checkin'] ? 'Confirmado' : 'Pendente' ?></td>
            <td>
                <?php
                $data_hora = $r['data_jogo'] . ' ' . $r['hora_inicio'];
                $diff = strtotime($data_hora) - time();
                if ($r['estado_reserva'] === 'ativa' && $diff > 86400):
                ?>
                    <a href="reservas.php?cancelar=<?= $r['id'] ?>" class="btn btn-cancelar" onclick="return confirm('Tens a certeza que queres cancelar esta reserva?')">Cancelar</a>
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