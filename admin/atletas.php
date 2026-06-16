<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarOperador();

// apagar atleta
if (isset($_GET['apagar'])) {
    $id = $_GET['apagar'];
    $stmt = $pdo->prepare("UPDATE atleta SET estado_atleta = 'inativo' WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: atletas.php');
    exit();
}

// adicionar ou editar atleta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $documento_tipo = $_POST['documento_tipo'];
    $documento_numero = $_POST['documento_numero'];
    $nif = $_POST['nif'];
    $estado = $_POST['estado_atleta'];

    if (isset($_POST['id']) && $_POST['id'] != '') {
        // editar
        $stmt = $pdo->prepare("UPDATE atleta SET nome=?, jogador=?, email=?, documento_tipo=?, documento_numero=?, nif=?, estado_atleta=? WHERE id=?");
        $stmt->execute([$nome, $nome, $email, $documento_tipo, $documento_numero, $nif, $estado, $_POST['id']]);
    } else {
        // adicionar
        $password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO atleta (nome, jogador, email, password, documento_tipo, documento_numero, nif, estado_atleta) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$nome, $nome, $email, $password, $documento_tipo, $documento_numero, $nif, $estado]);
    }
    header('Location: atletas.php');
    exit();
}

// buscar atletas
$atletas = $pdo->query("SELECT * FROM atleta")->fetchAll();

// buscar atleta para editar
$editar = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM atleta WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $editar = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Atletas - Backoffice</title>
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
        .btn-inativar { background: red; color: white; }
        .formulario { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd; }
        input, select { width: 100%; padding: 8px; margin: 5px 0 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn-guardar { background: green; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .inativo { color: red; }
        .ativo { color: green; }
    </style>
</head>
<body>
<nav>
    <span>Backoffice - Atletas</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="../logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <h2>Gerir Atletas</h2>

    <div class="formulario">
        <h3><?= $editar ? 'Editar Atleta' : 'Adicionar Atleta' ?></h3>
        <form method="POST">
            <?php if ($editar): ?>
                <input type="hidden" name="id" value="<?= $editar['id'] ?>">
            <?php endif; ?>

            <label>Nome</label>
            <input type="text" name="nome" value="<?= $editar['nome'] ?? '' ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= $editar['email'] ?? '' ?>" required>

            <label>Tipo de Documento</label>
            <select name="documento_tipo" required>
                <option value="">Seleciona...</option>
                <option value="Cartão de Cidadão" <?= ($editar['documento_tipo'] ?? '') == 'Cartão de Cidadão' ? 'selected' : '' ?>>Cartao de Cidadao</option>
                <option value="Passaporte" <?= ($editar['documento_tipo'] ?? '') == 'Passaporte' ? 'selected' : '' ?>>Passaporte</option>
                <option value="Outro" <?= ($editar['documento_tipo'] ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
            </select>

            <label>Numero do Documento</label>
            <input type="text" name="documento_numero" value="<?= $editar['documento_numero'] ?? '' ?>" required>

            <label>NIF (opcional)</label>
            <input type="text" name="nif" value="<?= $editar['nif'] ?? '' ?>" maxlength="9">

            <label>Estado</label>
            <select name="estado_atleta">
                <option value="ativo" <?= ($editar['estado_atleta'] ?? '') == 'ativo' ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo" <?= ($editar['estado_atleta'] ?? '') == 'inativo' ? 'selected' : '' ?>>Inativo</option>
            </select>

            <button type="submit" class="btn-guardar">Guardar</button>
            <?php if ($editar): ?>
                <a href="atletas.php" style="margin-left:10px; color:red;">Cancelar</a>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Documento</th>
            <th>NIF</th>
            <th>Estado</th>
            <th>Acoes</th>
        </tr>
        <?php foreach ($atletas as $a): ?>
        <tr>
            <td><?= $a['nome'] ?></td>
            <td><?= $a['email'] ?></td>
            <td><?= $a['documento_tipo'] ?> - <?= $a['documento_numero'] ?></td>
            <td><?= $a['nif'] ?? '-' ?></td>
            <td class="<?= $a['estado_atleta'] ?>"><?= $a['estado_atleta'] ?></td>
            <td>
                <a href="atletas.php?editar=<?= $a['id'] ?>" class="btn btn-editar">Editar</a>
                <a href="atletas.php?apagar=<?= $a['id'] ?>" class="btn btn-inativar" onclick="return confirm('Tens a certeza que queres desativar este atleta?')">Desativar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>