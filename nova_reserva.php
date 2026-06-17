<?php
require_once 'config/db.php';
require_once 'includes/session.php';

if (!estaLogado() || $_SESSION['tipo'] !== 'atleta') {
    header('Location: login.php');
    exit();
}

$erro = '';
$sucesso = '';

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

    if ($data_jogo < date('Y-m-d')) {
        $erro = 'Não podes fazer uma reserva para uma data que já passou!';
    } elseif ($hora_inicio >= $hora_fim) {
        $erro = 'A hora de início tem que ser antes da hora de fim!';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM campo WHERE tipo_campo = ? AND estado_campo = 'disponivel' AND id NOT IN (SELECT campo_id FROM reserva WHERE data_jogo = ? AND estado_reserva = 'ativa' AND ((hora_inicio < ? AND hora_fim > ?)))");
        $stmt->execute([$tipo_campo, $data_jogo, $hora_fim, $hora_inicio]);
        $campo_livre = $stmt->fetch();

        if (!$campo_livre) {
            $erro = 'Não há campos disponíveis desse tipo para a data e horário escolhidos!';
        } else {
            $valor_campo = $campo_livre['valor'];
            $valor_iluminacao = $iluminacao ? $campo_livre['iluminacao'] : 0;
            $valor_aluguer = ($raquetes + $bolas) * $campo_livre['aluguer_material'];
            $valor_total = $valor_campo + $valor_iluminacao + $valor_aluguer;

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
        label { font-size: 13px; color: #555; display: block; margin-top: 12px; margin-bottom: 4px; }
        input, select { width: 100%; padding: 9px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 13px; }
        input:focus, select:focus { border-color: #1e3a5f; outline: none; }
        .checkbox-linha { display: flex; align-items: center; gap: 8px; margin-top: 12px; }
        .checkbox-linha input { width: auto; }
        .btn-submit { width: 100%; padding: 12px; background: #f47c3c; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; margin-top: 20px; }
        .btn-submit:hover { background: #d4622c; }
        .erro { background: #ffe0e0; color: red; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; border-left: 4px solid red; }
        .sucesso { background: #e0ffe0; color: #1a5c1a; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; border-left: 4px solid green; }
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
    <h1>Nova Reserva</h1>
    <p>Escolhe o campo, data e horário para a tua reserva</p>
</div>

<div class="container">
    <div class="aviso">
       Atenção: podes editar ou cancelar a tua reserva até 24 horas antes do início do jogo.
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
                <option value="Pádel Coberto">Pádel Coberto — 15€/hora</option>
                <option value="Pádel Descoberto">Pádel Descoberto — 10€/hora</option>
                <option value="Ténis Terra Batida">Ténis Terra Batida — 12€/hora</option>
                <option value="Ténis Rápido">Ténis Rápido — 18€/hora</option>
            </select>

            <label>Data do Jogo</label>
            <input type="date" name="data_jogo" min="<?= date('Y-m-d') ?>" required>

            <label>Hora de Início</label>
            <input type="time" name="hora_inicio" required>

            <label>Hora de Fim</label>
            <input type="time" name="hora_fim" required>

            <div class="checkbox-linha">
                <input type="checkbox" name="iluminacao" id="iluminacao">
                <label for="iluminacao" style="margin-top:0">Iluminação noturna (custo extra)</label>
            </div>

            <label>Número de Raquetes a alugar</label>
            <input type="number" name="raquetes" value="0" min="0" max="4">

            <label>Número de Bolas a alugar</label>
            <input type="number" name="bolas" value="0" min="0" max="10">

            <label>NIF para faturação (opcional)</label>
            <input type="text" name="nif" placeholder="Apenas se precisares de fatura" maxlength="9">

            <button type="submit" class="btn-submit">Confirmar Reserva</button>
        </form>
    </div>
</div>
</body>
</html>