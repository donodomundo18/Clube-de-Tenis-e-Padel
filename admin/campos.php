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
        // editar
        $stmt = $pdo->prepare("UPDATE campo SET numero_identificador=?, tipo_campo=?, estado_campo=?, descricao=?, valor=?, iluminacao=?, horario=?, aluguer_material=? WHERE id=?");
        $stmt->execute([$numero, $tipo, $estado, $descricao, $valor, $iluminacao, $horario, $aluguer, $_POST['id']]);
    } else {
        // adicionar
        $stmt = $pdo->prepare("INSERT INTO campo (numero_identificador, tipo_campo, estado_campo, descricao, valor, iluminacao, horario, disponibilidade, aluguer_material) VALUES (?,?,?,?,?,?,?,1,?)");
        $stmt->execute([$numero, $tipo, $estado, $descricao, $valor, $iluminacao, $horario, $aluguer]);
    }
    header('Location: campos.php');
    exit();
}

// buscar campos
$campos = $pdo->query("SELECT * FROM campo")->fetchAll();

// buscar campo para editar
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
    <title>Gerir Campos - Clube de Ténis e Pádel</title>
    <style>
        body { font-family: Arial; margin: 0; background: #f5f5f5; }
        nav { background: green; padding: 10px 20px; color: white; display: flex; justify-content: space-between; }
        nav a { color: white; margin-left: 10px; text-decoration: none; }
        .container { max-width: 1000px; margin: 30px auto; padding: 20px; }
        h2 { color: green; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 13px; }
        th { background: green; color: white; }
        .btn { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; }
        .btn-editar { background: orange; color: white; }
        .btn-apagar { background: red; color: white; }
        .formulario { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd; }
        input, select, textarea { width: 100%; padding: 8px; margin: 5px 0 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn-guardar { background: green; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
<nav>
    <span>Backoffice - Campos</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="../logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <h2>Gerir Campos</h2>

    <div class="formulario">
        <h3><?= $editar ? 'Editar Campo' : 'Adicionar Campo' ?></h3>
        <form method="POST">
            <?php if ($editar): ?>
                <input type="hidden" name="id" value="<?= $editar['id'] ?>">
            <?php endif; ?>
            
            <label>Numero do Campo</label>
            <input type="text" name="numero_identificador" value="<?= $editar['numero_identificador'] ?? '' ?>" required>
            
            <label>Tipo de Campo</label>
            <select name="tipo_campo" required>
                <option value="">Seleciona...</option>
                <option value="Pádel Coberto" <?= ($editar['tipo_campo'] ?? '') == 'Pádel Coberto' ? 'selected' : '' ?>>Padel Coberto</option>
                <option value="Pádel Descoberto" <?= ($editar['tipo_campo'] ?? '') == 'Pádel Descoberto' ? 'selected' : '' ?>>Padel Descoberto</option>
                <option value="Ténis Terra Batida" <?= ($editar['tipo_campo'] ?? '') == 'Ténis Terra Batida' ? 'selected' : '' ?>>Tenis Terra Batida</option>
                <option value="Ténis Rápido" <?= ($editar['tipo_campo'] ?? '') == 'Ténis Rápido' ? 'selected' : '' ?>>Tenis Rapido</option>
            </select>
            
            <label>Estado</label>
            <select name="estado_campo" required>
                <option value="disponivel" <?= ($editar['estado_campo'] ?? '') == 'disponivel' ? 'selected' : '' ?>>Disponivel</option>
                <option value="manutencao" <?= ($editar['estado_campo'] ?? '') == 'manutencao' ? 'selected' : '' ?>>Manutencao</option>
            </select>
            
            <label>Descricao</label>
            <textarea name="descricao"><?= $editar['descricao'] ?? '' ?></textarea>
            
            <label>Valor base (€)</label>
            <input type="number" step="0.01" name="valor" value="<?= $editar['valor'] ?? '' ?>" required>
            
            <label>Custo iluminacao (€)</label>
            <input type="number" step="0.01" name="iluminacao" value="<?= $editar['iluminacao'] ?? '0' ?>">
            
            <label>Horario</label>
            <input type="text" name="horario" value="<?= $editar['horario'] ?? '' ?>" placeholder="ex: 08:00-23:00">
            
            <label>Custo aluguer material (€)</label>
            <input type="number" step="0.01" name="aluguer_material" value="<?= $editar['aluguer_material'] ?? '0' ?>">
            
            <button type="submit" class="btn-guardar">Guardar</button>
            <?php if ($editar): ?>
                <a href="campos.php" style="margin-left:10px; color:red;">Cancelar</a>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <tr>
            <th>Numero</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Valor</th>
            <th>Iluminacao</th>
            <th>Horario</th>
            <th>Acoes</th>
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