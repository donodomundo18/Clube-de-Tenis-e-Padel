<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarOperador();

$sucesso = '';
$erro = '';

// registar pagamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reserva_id = $_POST['reserva_id'];
    $montante = $_POST['montante'];
    $tipo = $_POST['tipo'];
    $operador_id = $_SESSION['operador_id'];

    $stmt = $pdo->prepare("INSERT INTO pagamento (reserva_id, montante, tipo, operador_id) VALUES (?,?,?,?)");
    $stmt->execute([$reserva_id, $montante, $tipo, $operador_id]);
    $sucesso = 'Pagamento registado com sucesso!';
}

// buscar reservas ativas para selecionar
$reservas = $pdo->query("SELECT r.id, a.nome as nome_atleta, r.campo_escolhido, r.data_jogo, r.valor_total FROM reserva r JOIN atleta a ON r.atleta_id = a.id WHERE r.estado_reserva = 'ativa' ORDER BY r.data_jogo DESC")->fetchAll();

// buscar todos os pagamentos
$pagamentos = $pdo->query("SELECT p.*, a.nome as nome_atleta, r.campo_escolhido, r.data_jogo, o.nome as nome_operador FROM pagamento p JOIN reserva r ON p.reserva_id = r.id JOIN atleta a ON r.atleta_id = a.id JOIN operador o ON p.operador_id = o.id ORDER BY p.data_pagamento DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Pagamentos - Backoffice</title>
    <style>
        body { font-family: Arial; margin: 0; background: #f5f5f5; }
        nav { background: green; padding: 10px 20px; color: white; display: flex; justify-content: space-between; }
        nav a { color: white; margin-left: 10px; text-decoration: none; }
        .container { max-width: 1000px; margin: 30px auto; padding: 20px; }
        h2 { color: green; }
        .formulario { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd; }
        label { font-size: 13px; color: #555; display: block; margin-top: 8px; }
        input, select { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn-guardar { background: green; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 9px 10px; border: 1px solid #ddd; font-size: 12px; text-align: left; }
        th { background: green; color: white; }
        .sucesso { background: #e0ffe0; color: green; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; }
        .erro { background: #ffe0e0; color: red; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; }
    </style>
</head>
<body>
<nav>
    <span>Backoffice - Pagamentos</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="../logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <h2>Registar Pagamento</h2>

    <?php if ($sucesso): ?>
        <div class="sucesso"><?= $sucesso ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="erro"><?= $erro ?></div>
    <?php endif; ?>

    <div class="formulario">
        <form method="POST">
            <label>Reserva</label>
            <select name="reserva_id" required>
                <option value="">Seleciona a reserva...</option>
                <?php foreach ($reservas as $r): ?>
                    <option value="<?= $r['id'] ?>">
                        #<?= $r['id'] ?> - <?= $r['nome_atleta'] ?> - <?= $r['campo_escolhido'] ?> - <?= $r['data_jogo'] ?> - Total: <?= number_format($r['valor_total'], 2) ?>€
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

    <h2>Historico de Pagamentos</h2>
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