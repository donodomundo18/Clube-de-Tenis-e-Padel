<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarOperador();

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reserva_id = $_POST['reserva_id'];
    $montante = $_POST['montante'];
    $tipo = $_POST['tipo'];
    $operador_id = $_SESSION['operador_id'];

    $stmt = $pdo->prepare("INSERT INTO pagamento (reserva_id, montante, tipo, operador_id) VALUES (?,?,?,?)");
    $stmt->execute([$reserva_id, $montante, $tipo, $operador_id]);
    $sucesso = 'Pagamento registado com sucesso!';
}

$reservas = $pdo->query("SELECT r.id, a.nome as nome_atleta, r.campo_escolhido, r.data_jogo, r.valor_total FROM reserva r JOIN atleta a ON r.atleta_id = a.id WHERE r.estado_reserva = 'ativa' ORDER BY r.data_jogo DESC")->fetchAll();

$pagamentos = $pdo->query("SELECT p.*, a.nome as nome_atleta, r.campo_escolhido, r.data_jogo, o.nome as nome_operador FROM pagamento p JOIN reserva r ON p.reserva_id = r.id JOIN atleta a ON r.atleta_id = a.id JOIN operador o ON p.operador_id = o.id ORDER BY p.data_pagamento DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Pagamentos - Backoffice</title>
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
        .formulario { background: white; padding: 22px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #ddd; }
        .formulario h3 { color: #1e3a5f; margin-bottom: 15px; font-size: 15px; }
        label { font-size: 13px; color: #555; display: block; margin-bottom: 4px; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 13px; }
        input:focus, select:focus { border-color: #1e3a5f; outline: none; }
        .btn-guardar { background: #f47c3c; color: white; padding: 10px 22px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; margin-top: 15px; }
        .btn-guardar:hover { background: #d4622c; }
        .secao-titulo { color: #1e3a5f; font-size: 16px; margin: 25px 0 15px 0; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 12px; text-align: left; }
        th { background: #1e3a5f; color: white; }
        tr:hover { background: #f9f9f9; }
        .sucesso { background: #e0ffe0; color: #1a6b1a; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; border-left: 4px solid green; }
        .erro { background: #ffe0e0; color: red; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; border-left: 4px solid red; }
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
    <h1>Pagamentos</h1>
    <p>Regista e consulta os pagamentos das reservas</p>
</div>

<div class="container">
    <?php if ($sucesso): ?>
        <div class="sucesso"><?= $sucesso ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="erro"><?= $erro ?></div>
    <?php endif; ?>

    <div class="formulario">
        <h3>Registar Novo Pagamento</h3>
        <form method="POST">
            <label>Reserva</label>
            <select name="reserva_id" required>
                <option value="">Seleciona a reserva...</option>
                <?php foreach ($reservas as $r): ?>
                    <option value="<?= $r['id'] ?>">
                        #<?= $r['id'] ?> — <?= $r['nome_atleta'] ?> — <?= $r['campo_escolhido'] ?> — <?= $r['data_jogo'] ?> — Total: <?= number_format($r['valor_total'], 2) ?>€
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Montante (€)</label>
            <input type="number" step="0.01" name="montante" placeholder="0.00" required>

            <label>Tipo de Pagamento</label>
            <select name="tipo" required>
                <option value="parcial">Parcial</option>
                <option value="total">Total</option>
            </select>

            <button type="submit" class="btn-guardar">Registar Pagamento</button>
        </form>
    </div>

    <p class="secao-titulo">Histórico de Pagamentos</p>
    <table>
        <tr>
            <th>ID</th>
            <th>Atleta</th>
            <th>Campo</th>
            <th>Data Jogo</th>
            <th>Montante</th>
            <th>Tipo</th>
            <th>Data Pagamento</th>
            <th>Operador</th>
        </tr>
        <?php foreach ($pagamentos as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= $p['nome_atleta'] ?></td>
            <td><?= $p['campo_escolhido'] ?></td>
            <td><?= $p['data_jogo'] ?></td>
            <td><?= number_format($p['montante'], 2) ?>€</td>
            <td><?= $p['tipo'] ?></td>
            <td><?= $p['data_pagamento'] ?></td>
            <td><?= $p['nome_operador'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>