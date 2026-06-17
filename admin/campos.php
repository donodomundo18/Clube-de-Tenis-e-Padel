<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarOperador();

// apagar campo
if (isset($_GET['apagar'])) {
    $id = $_GET['apagar'];
    $stmt = $pdo->prepare("DELETE FROM campo WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: campos.php');
    exit();
}

// adicionar ou editar campo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero_identificador'];
    $tipo = $_POST['tipo_campo'];
    $estado = $_POST['estado_campo'];
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $iluminacao = $_POST['iluminacao'];
    $horario = $_POST['horario'];
    $aluguer = $_POST['aluguer_material'];

    if (isset($_POST['id']) && $_POST['id'] != '') {
        $stmt = $pdo->prepare("UPDATE campo SET numero_identificador=?, tipo_campo=?, estado_campo=?, descricao=?, valor=?, iluminacao=?, horario=?, aluguer_material=? WHERE id=?");
        $stmt->execute([$numero, $tipo, $estado, $descricao, $valor, $iluminacao, $horario, $aluguer, $_POST['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO campo (numero_identificador, tipo_campo, estado_campo, descricao, valor, iluminacao, horario, disponibilidade, aluguer_material) VALUES (?,?,?,?,?,?,?,1,?)");
        $stmt->execute([$numero, $tipo, $estado, $descricao, $valor, $iluminacao, $horario, $aluguer]);
    }
    header('Location: campos.php');
    exit();
}

$campos = $pdo->query("SELECT * FROM campo")->fetchAll();

$editar = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM campo WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $editar = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerir Campos - Backoffice</title>
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
        .formulario { background: white; padding: 22px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd; }
        .formulario h3 { color: #1e3a5f; margin-bottom: 15px; font-size: 15px; }
        label { font-size: 13px; color: #555; display: block; margin-bottom: 4px; }
        input, select, textarea { width: 100%; padding: 8px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 13px; }
        input:focus, select:focus { border-color: #1e3a5f; outline: none; }
        .btn-guardar { background: #f47c3c; color: white; padding: 10px 22px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn-guardar:hover { background: #d4622c; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 13px; text-align: left; }
        th { background: #1e3a5f; color: white; }
        tr:hover { background: #f9f9f9; }
        .btn { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; }
        .btn-editar { background: #f47c3c; color: white; }
        .btn-editar:hover { background: #d4622c; }
        .btn-apagar { background: red; color: white; }
        .btn-apagar:hover { background: darkred; }
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
    <h1>Gerir Campos</h1>
    <p>Adiciona, edita ou remove campos do clube</p>
</div>

<div class="container">
    <div class="formulario">
        <h3><?= $editar ? 'Editar Campo' : 'Adicionar Campo' ?></h3>
        <form method="POST">
            <?php if ($editar): ?>
                <input type="hidden" name="id" value="<?= $editar['id'] ?>">
            <?php endif; ?>

            <label>Número do Campo</label>
            <input type="text" name="numero_identificador" value="<?= $editar['numero_identificador'] ?? '' ?>" required>

            <label>Tipo de Campo</label>
            <select name="tipo_campo" required>
                <option value="">Seleciona...</option>
                <option value="Pádel Coberto" <?= ($editar['tipo_campo'] ?? '') == 'Pádel Coberto' ? 'selected' : '' ?>>Pádel Coberto</option>
                <option value="Pádel Descoberto" <?= ($editar['tipo_campo'] ?? '') == 'Pádel Descoberto' ? 'selected' : '' ?>>Pádel Descoberto</option>
                <option value="Ténis Terra Batida" <?= ($editar['tipo_campo'] ?? '') == 'Ténis Terra Batida' ? 'selected' : '' ?>>Ténis Terra Batida</option>
                <option value="Ténis Rápido" <?= ($editar['tipo_campo'] ?? '') == 'Ténis Rápido' ? 'selected' : '' ?>>Ténis Rápido</option>
            </select>

            <label>Estado</label>
            <select name="estado_campo" required>
                <option value="disponivel" <?= ($editar['estado_campo'] ?? '') == 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                <option value="manutencao" <?= ($editar['estado_campo'] ?? '') == 'manutencao' ? 'selected' : '' ?>>Manutenção</option>
            </select>

            <label>Descrição</label>
            <textarea name="descricao"><?= $editar['descricao'] ?? '' ?></textarea>

            <label>Valor base (€)</label>
            <input type="number" step="0.01" name="valor" value="<?= $editar['valor'] ?? '' ?>" required>

            <label>Custo iluminação (€)</label>
            <input type="number" step="0.01" name="iluminacao" value="<?= $editar['iluminacao'] ?? '0' ?>">

            <label>Horário</label>
            <input type="text" name="horario" value="<?= $editar['horario'] ?? '' ?>" placeholder="ex: 08:00-23:00">

            <label>Custo aluguer material (€)</label>
            <input type="number" step="0.01" name="aluguer_material" value="<?= $editar['aluguer_material'] ?? '0' ?>">

            <button type="submit" class="btn-guardar">Guardar</button>
            <?php if ($editar): ?>
                <a href="campos.php" style="margin-left:10px; color:red; font-size:13px;">Cancelar</a>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <tr>
            <th>Número</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Valor</th>
            <th>Iluminação</th>
            <th>Horário</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($campos as $c): ?>
        <tr>
            <td><?= $c['numero_identificador'] ?></td>
            <td><?= $c['tipo_campo'] ?></td>
            <td><?= $c['estado_campo'] ?></td>
            <td><?= $c['valor'] ?>€</td>
            <td><?= $c['iluminacao'] ?>€</td>
            <td><?= $c['horario'] ?></td>
            <td>
                <a href="campos.php?editar=<?= $c['id'] ?>" class="btn btn-editar">Editar</a>
                <a href="campos.php?apagar=<?= $c['id'] ?>" class="btn btn-apagar" onclick="return confirm('Tens a certeza?')">Apagar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>