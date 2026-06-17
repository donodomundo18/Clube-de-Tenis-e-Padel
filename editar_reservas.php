<?php
require_once 'config/db.php';
require_once 'includes/session.php';

if (!estaLogado() || $_SESSION['tipo'] !== 'atleta') {
    header('Location: login.php');
    exit();
}

// buscar reserva
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: reservas.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM reserva WHERE id = ? AND atleta_id = ?");
$stmt->execute([$id, $_SESSION['atleta_id']]);
$reserva = $stmt->fetch();

if (!$reserva) {
    header('Location: reservas.php');
    exit();
}

// verificar se ainda da para editar (24h antes)
$data_hora_inicio = $reserva['data_jogo'] . ' ' . $reserva['hora_inicio'];
$diferenca = strtotime($data_hora_inicio) - time();

if ($diferenca < 86400) {
    header('Location: reservas.php');
    exit();
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_campo = $_POST['tipo_campo'];
    $data_jogo = $_POST['data_jogo'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fim = $_POST['hora_fim'];
    $iluminacao = isset($_POST['iluminacao']) ? 1 : 0;
    $raquetes = (int)$_POST['raquetes'];
    $bolas = (int)$_POST['bolas'];

    if ($data_jogo < date('Y-m-d')) {
        $erro = 'Não podes escolher uma data que já passou!';
    } elseif ($hora_inicio >= $hora_fim) {
        $erro = 'A hora de início tem que ser antes da hora de fim!';
    } else {
        // verificar disponibilidade excluindo a reserva atual
        $stmt = $pdo->prepare("SELECT * FROM campo WHERE tipo_campo = ? AND estado_campo = 'disponivel' AND id NOT IN (SELECT campo_id FROM reserva WHERE data_jogo = ? AND estado_reserva = 'ativa' AND id != ? AND ((hora_inicio < ? AND hora_fim > ?)))");
        $stmt->execute([$tipo_campo, $data_jogo, $id, $hora_fim, $hora_inicio]);
        $campo_livre = $stmt->fetch();

        if (!$campo_livre) {
            $erro = 'Não há campos disponíveis desse tipo para a data e horário escolhidos!';
        } else {
            // recalcular valor
            $valor_campo = $campo_livre['valor'];
            $valor_iluminacao = $iluminacao ? $campo_livre['iluminacao'] : 0;
            $valor_aluguer = ($raquetes + $bolas) * $campo_livre['aluguer_material'];
            $valor_total = $valor_campo + $valor_iluminacao + $valor_aluguer;

            $stmt = $pdo->prepare("UPDATE reserva SET campo_id=?, campo_escolhido=?, data_jogo=?, hora_inicio=?, hora_fim=?, suplemento_iluminacao=?, suplemento_aluguer_raquetes=?, suplemento_aluguer_bolas=?, valor_total=? WHERE id=?");
            $stmt->execute([$campo_livre['id'], $tipo_campo, $data_jogo, $hora_inicio, $hora_fim, $iluminacao, $raquetes, $bolas, $valor_total, $id]);

            $sucesso = 'Reserva atualizada com sucesso! Novo total: ' . number_format($valor_total, 2) . '€';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Reserva - Clube de Ténis e Pádel</title>
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
        .container { max-width: 600px; margin: 30px auto; padding: 0 20px; }
        .aviso { background: #fff3cd; color: #856404; padding: 10px 15px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; border-left: 4px solid #f47c3c; }
        .formulario { background: white; padding: 25px; border-radius: 8px; border: 1px solid #ddd; }
        label { font-size: 13px; color: #555; display: block; margin-bottom: 4px; margin-top: 12px; }
        input, select { width: 100%; padding: 9px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 13px; }
        input:focus, select:focus { border-color: #1e3a5f; outline: none; }
        .checkbox-linha { display: flex; align-items: center; gap: 8px; margin-top: 12px; }
        .checkbox-linha input { width: auto; }
        .btn-submit { width: 100%; padding: 12px; background: #f47c3c; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; margin-top: 20px; }
        .btn-submit:hover { background: #d4622c; }
        .btn-voltar { display: inline-block; margin-top: 15px; color: #1e3a5f; font-size: 13px; text-decoration: none; }
        .erro { background: #ffe0e0; color: red; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; border-left: 4px solid red; }
        .sucesso { background: #e0ffe0; color: #1a6b1a; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; border-left: 4px solid green; }
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
    <h1>Editar Reserva</h1>
    <p>Altera os dados da tua reserva</p>
</div>

<div class="container">
    <div class="aviso">
        Atenção: só podes editar esta reserva até 24 horas antes do início do jogo.
    </div>

    <?php if ($erro): ?>
        <div class="erro"><?= $erro ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="sucesso"><?= $sucesso ?></div>
    <?php endif; ?>

    <div class="formulario">
        <form method="POST">
            <label>Tipo de Campo</label>
            <select name="tipo_campo" required>
                <option value="Pádel Coberto" <?= $reserva['campo_escolhido'] == 'Pádel Coberto' ? 'selected' : '' ?>>Pádel Coberto — 15€/hora</option>
                <option value="Pádel Descoberto" <?= $reserva['campo_escolhido'] == 'Pádel Descoberto' ? 'selected' : '' ?>>Pádel Descoberto — 10€/hora</option>
                <option value="Ténis Terra Batida" <?= $reserva['campo_escolhido'] == 'Ténis Terra Batida' ? 'selected' : '' ?>>Ténis Terra Batida — 12€/hora</option>
                <option value="Ténis Rápido" <?= $reserva['campo_escolhido'] == 'Ténis Rápido' ? 'selected' : '' ?>>Ténis Rápido — 18€/hora</option>
            </select>

            <label>Data do Jogo</label>
            <input type="date" name="data_jogo" value="<?= $reserva['data_jogo'] ?>" min="<?= date('Y-m-d') ?>" required>

            <label>Hora de Início</label>
            <input type="time" name="hora_inicio" value="<?= $reserva['hora_inicio'] ?>" required>

            <label>Hora de Fim</label>
            <input type="time" name="hora_fim" value="<?= $reserva['hora_fim'] ?>" required>

            <div class="checkbox-linha">
                <input type="checkbox" name="iluminacao" id="iluminacao" <?= $reserva['suplemento_iluminacao'] ? 'checked' : '' ?>>
                <label for="iluminacao" style="margin-top:0">Iluminação noturna (custo extra)</label>
            </div>

            <label>Número de Raquetes a alugar</label>
            <input type="number" name="raquetes" value="<?= $reserva['suplemento_aluguer_raquetes'] ?>" min="0" max="4">

            <label>Número de Bolas a alugar</label>
            <input type="number" name="bolas" value="<?= $reserva['suplemento_aluguer_bolas'] ?>" min="0" max="10">

            <button type="submit" class="btn-submit">Guardar Alterações</button>
        </form>
        <a href="reservas.php" class="btn-voltar">← Voltar às minhas reservas</a>
    </div>
</div>
</body>
</html>