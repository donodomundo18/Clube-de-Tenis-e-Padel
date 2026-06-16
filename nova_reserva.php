<?php
require_once 'config/db.php';
require_once 'includes/session.php';

if (!estaLogado() || $_SESSION['tipo'] !== 'atleta') {
    header('Location: login.php');
    exit();
}

$erro = '';
$sucesso = '';

// buscar campos disponiveis
$campos = $pdo->query("SELECT * FROM campo WHERE estado_campo = 'disponivel' ORDER BY tipo_campo")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_campo = $_POST['tipo_campo'];
    $data_jogo = $_POST['data_jogo'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fim = $_POST['hora_fim'];
    $iluminacao = isset($_POST['iluminacao']) ? 1 : 0;
    $raquetes = (int)$_POST['raquetes'];
    $bolas = (int)$_POST['bolas'];
    $nif = trim($_POST['nif']);

    // validar data e hora
    if ($data_jogo < date('Y-m-d')) {
        $erro = 'Nao podes fazer uma reserva para uma data que ja passou!';
    } elseif ($hora_inicio >= $hora_fim) {
        $erro = 'A hora de inicio tem que ser antes da hora de fim!';
    } else {
        // verificar se ha campo disponivel desse tipo nesse horario
        $stmt = $pdo->prepare("SELECT * FROM campo WHERE tipo_campo = ? AND estado_campo = 'disponivel' AND id NOT IN (SELECT campo_id FROM reserva WHERE data_jogo = ? AND estado_reserva = 'ativa' AND ((hora_inicio < ? AND hora_fim > ?)))");
        $stmt->execute([$tipo_campo, $data_jogo, $hora_fim, $hora_inicio]);
        $campo_livre = $stmt->fetch();

        if (!$campo_livre) {
            $erro = 'Nao ha campos disponiveis desse tipo para a data e horario escolhidos!';
        } else {
            // calcular valor total
            $valor_campo = $campo_livre['valor'];
            $valor_iluminacao = $iluminacao ? $campo_livre['iluminacao'] : 0;
            $valor_aluguer = ($raquetes + $bolas) * $campo_livre['aluguer_material'];
            $valor_total = $valor_campo + $valor_iluminacao + $valor_aluguer;

            // criar reserva
            $stmt = $pdo->prepare("INSERT INTO reserva (atleta_id, campo_id, campo_escolhido, data_jogo, hora_inicio, hora_fim, suplemento_iluminacao, suplemento_aluguer_raquetes, suplemento_aluguer_bolas, valor_total, nif_faturacao) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$_SESSION['atleta_id'], $campo_livre['id'], $tipo_campo, $data_jogo, $hora_inicio, $hora_fim, $iluminacao, $raquetes, $bolas, $valor_total, $nif ?: null]);

            $sucesso = 'Reserva feita com sucesso! Total a pagar: ' . number_format($valor_total, 2) . '€';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Nova Reserva - Clube de Ténis e Pádel</title>
    <style>
        body { font-family: Arial; margin: 0; background: #f5f5f5; }
        nav { background: green; padding: 10px 20px; color: white; display: flex; justify-content: space-between; }
        nav a { color: white; margin-left: 10px; text-decoration: none; }
        .container { max-width: 600px; margin: 30px auto; padding: 20px; }
        h2 { color: green; }
        .formulario { background: white; padding: 25px; border-radius: 8px; border: 1px solid #ddd; }
        label { font-size: 13px; color: #555; display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 9px; margin-top: 4px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .checkbox-linha { display: flex; align-items: center; gap: 8px; margin-top: 10px; }
        .checkbox-linha input { width: auto; }
        .btn { width: 100%; padding: 12px; background: green; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; margin-top: 15px; }
        .btn:hover { background: darkgreen; }
        .erro { background: #ffe0e0; color: red; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; }
        .sucesso { background: #e0ffe0; color: green; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; }
        .aviso { background: #fff8e0; color: #888; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 12px; }
    </style>
</head>
<body>
<nav>
    <span>Clube de Ténis e Pádel</span>
    <div>
        <a href="index.php">Inicio</a>
        <a href="reservas.php">As minhas reservas</a>
        <a href="logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <h2>Nova Reserva</h2>

    <div class="aviso">
        Podes editar ou cancelar a tua reserva ate 24 horas antes do inicio do jogo.
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
                <option value="">Seleciona o tipo de campo...</option>
                <option value="Pádel Coberto">Padel Coberto - 15€/hora</option>
                <option value="Pádel Descoberto">Padel Descoberto - 10€/hora</option>
                <option value="Ténis Terra Batida">Tenis Terra Batida - 12€/hora</option>
                <option value="Ténis Rápido">Tenis Rapido - 18€/hora</option>
            </select>

            <label>Data do Jogo</label>
            <input type="date" name="data_jogo" min="<?= date('Y-m-d') ?>" required>

            <label>Hora de Inicio</label>
            <input type="time" name="hora_inicio" required>

            <label>Hora de Fim</label>
            <input type="time" name="hora_fim" required>

            <div class="checkbox-linha">
                <input type="checkbox" name="iluminacao" id="iluminacao">
                <label for="iluminacao" style="margin-top:0">Iluminacao noturna (custo extra)</label>
            </div>

            <label>Numero de Raquetes a alugar</label>
            <input type="number" name="raquetes" value="0" min="0" max="4">

            <label>Numero de Bolas a alugar</label>
            <input type="number" name="bolas" value="0" min="0" max="10">

            <label>NIF para faturacao (opcional)</label>
            <input type="text" name="nif" placeholder="Apenas se precisares de fatura" maxlength="9">

            <button type="submit" class="btn">Confirmar Reserva</button>
        </form>
    </div>
</div>
</body>
</html><?php
require_once 'config/db.php';
require_once 'includes/session.php';

if (!estaLogado() || $_SESSION['tipo'] !== 'atleta') {
    header('Location: login.php');
    exit();
}

$erro = '';
$sucesso = '';

// buscar campos disponiveis
$campos = $pdo->query("SELECT * FROM campo WHERE estado_campo = 'disponivel' ORDER BY tipo_campo")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_campo = $_POST['tipo_campo'];
    $data_jogo = $_POST['data_jogo'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fim = $_POST['hora_fim'];
    $iluminacao = isset($_POST['iluminacao']) ? 1 : 0;
    $raquetes = (int)$_POST['raquetes'];
    $bolas = (int)$_POST['bolas'];
    $nif = trim($_POST['nif']);

    // validar data e hora
    if ($data_jogo < date('Y-m-d')) {
        $erro = 'Nao podes fazer uma reserva para uma data que ja passou!';
    } elseif ($hora_inicio >= $hora_fim) {
        $erro = 'A hora de inicio tem que ser antes da hora de fim!';
    } else {
        // verificar se ha campo disponivel desse tipo nesse horario
        $stmt = $pdo->prepare("SELECT * FROM campo WHERE tipo_campo = ? AND estado_campo = 'disponivel' AND id NOT IN (SELECT campo_id FROM reserva WHERE data_jogo = ? AND estado_reserva = 'ativa' AND ((hora_inicio < ? AND hora_fim > ?)))");
        $stmt->execute([$tipo_campo, $data_jogo, $hora_fim, $hora_inicio]);
        $campo_livre = $stmt->fetch();

        if (!$campo_livre) {
            $erro = 'Nao ha campos disponiveis desse tipo para a data e horario escolhidos!';
        } else {
            // calcular valor total
            $valor_campo = $campo_livre['valor'];
            $valor_iluminacao = $iluminacao ? $campo_livre['iluminacao'] : 0;
            $valor_aluguer = ($raquetes + $bolas) * $campo_livre['aluguer_material'];
            $valor_total = $valor_campo + $valor_iluminacao + $valor_aluguer;

            // criar reserva
            $stmt = $pdo->prepare("INSERT INTO reserva (atleta_id, campo_id, campo_escolhido, data_jogo, hora_inicio, hora_fim, suplemento_iluminacao, suplemento_aluguer_raquetes, suplemento_aluguer_bolas, valor_total, nif_faturacao) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$_SESSION['atleta_id'], $campo_livre['id'], $tipo_campo, $data_jogo, $hora_inicio, $hora_fim, $iluminacao, $raquetes, $bolas, $valor_total, $nif ?: null]);

            $sucesso = 'Reserva feita com sucesso! Total a pagar: ' . number_format($valor_total, 2) . '€';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Nova Reserva - Clube de Ténis e Pádel</title>
    <style>
        body { font-family: Arial; margin: 0; background: #f5f5f5; }
        nav { background: green; padding: 10px 20px; color: white; display: flex; justify-content: space-between; }
        nav a { color: white; margin-left: 10px; text-decoration: none; }
        .container { max-width: 600px; margin: 30px auto; padding: 20px; }
        h2 { color: green; }
        .formulario { background: white; padding: 25px; border-radius: 8px; border: 1px solid #ddd; }
        label { font-size: 13px; color: #555; display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 9px; margin-top: 4px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .checkbox-linha { display: flex; align-items: center; gap: 8px; margin-top: 10px; }
        .checkbox-linha input { width: auto; }
        .btn { width: 100%; padding: 12px; background: green; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; margin-top: 15px; }
        .btn:hover { background: darkgreen; }
        .erro { background: #ffe0e0; color: red; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; }
        .sucesso { background: #e0ffe0; color: green; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; }
        .aviso { background: #fff8e0; color: #888; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 12px; }
    </style>
</head>
<body>
<nav>
    <span>Clube de Ténis e Pádel</span>
    <div>
        <a href="index.php">Inicio</a>
        <a href="reservas.php">As minhas reservas</a>
        <a href="logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <h2>Nova Reserva</h2>

    <div class="aviso">
        Podes editar ou cancelar a tua reserva ate 24 horas antes do inicio do jogo.
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
                <option value="">Seleciona o tipo de campo...</option>
                <option value="Pádel Coberto">Padel Coberto - 15€/hora</option>
                <option value="Pádel Descoberto">Padel Descoberto - 10€/hora</option>
                <option value="Ténis Terra Batida">Tenis Terra Batida - 12€/hora</option>
                <option value="Ténis Rápido">Tenis Rapido - 18€/hora</option>
            </select>

            <label>Data do Jogo</label>
            <input type="date" name="data_jogo" min="<?= date('Y-m-d') ?>" required>

            <label>Hora de Inicio</label>
            <input type="time" name="hora_inicio" required>

            <label>Hora de Fim</label>
            <input type="time" name="hora_fim" required>

            <div class="checkbox-linha">
                <input type="checkbox" name="iluminacao" id="iluminacao">
                <label for="iluminacao" style="margin-top:0">Iluminacao noturna (custo extra)</label>
            </div>

            <label>Numero de Raquetes a alugar</label>
            <input type="number" name="raquetes" value="0" min="0" max="4">

            <label>Numero de Bolas a alugar</label>
            <input type="number" name="bolas" value="0" min="0" max="10">

            <label>NIF para faturacao (opcional)</label>
            <input type="text" name="nif" placeholder="Apenas se precisares de fatura" maxlength="9">

            <button type="submit" class="btn">Confirmar Reserva</button>
        </form>
    </div>
</div>
</body>
</html>