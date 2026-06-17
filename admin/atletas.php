<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarOperador();

// desativar atleta
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
        // editar atleta existente
        $stmt = $pdo->prepare("UPDATE atleta SET nome=?, jogador=?, email=?, documento_tipo=?, documento_numero=?, nif=?, estado_atleta=? WHERE id=?");
        $stmt->execute([$nome, $nome, $email, $documento_tipo, $documento_numero, $nif, $estado, $_POST['id']]);
    } else {
        // adicionar novo atleta - password padrao 123456
        $password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO atleta (nome, jogador, email, password, documento_tipo, documento_numero, nif, estado_atleta) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$nome, $nome, $email, $password, $documento_tipo, $documento_numero, $nif, $estado]);
    }
    header('Location: atletas.php');
    exit();
}

$atletas = $pdo->query("SELECT * FROM atleta ORDER BY nome")->fetchAll();

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
        input, select { width: 100%; padding: 8px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 13px; }
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
        .btn-inativar { background: red; color: white; }
        .btn-inativar:hover { background: darkred; }
        .inativo { color: red; }
        .ativo { color: #1a6b1a; font-weight: bold; }
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
    <h1>Gerir Atletas</h1>
    <p>Adiciona, edita e desativa atletas do clube</p>
</div>

<div class="container">
    <div class="formulario">
        <h3><?= $editar ? 'Editar Atleta' : 'Adicionar Atleta' ?></h3>
        <form method="POST">
            <?php if ($editar): ?>
                <input type="hidden" name="id" value="<?= $editar['id'] ?>">
            <?php endif; ?>

            <label>Nome completo</label>
            <input type="text" name="nome" value="<?= $editar['nome'] ?? '' ?>" placeholder="Nome do atleta" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= $editar['email'] ?? '' ?>" placeholder="email@exemplo.com" required>

            <label>Tipo de Documento</label>
            <select name="documento_tipo" required>
                <option value="">Seleciona...</option>
                <option value="Cartão de Cidadão" <?= ($editar['documento_tipo'] ?? '') == 'Cartão de Cidadão' ? 'selected' : '' ?>>Cartão de Cidadão</option>
                <option value="Passaporte" <?= ($editar['documento_tipo'] ?? '') == 'Passaporte' ? 'selected' : '' ?>>Passaporte</option>
                <option value="Outro" <?= ($editar['documento_tipo'] ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
            </select>

            <label>Número do Documento</label>
            <input type="text" name="documento_numero" value="<?= $editar['documento_numero'] ?? '' ?>" placeholder="Número do documento" required>

            <label>NIF (opcional)</label>
            <input type="text" name="nif" value="<?= $editar['nif'] ?? '' ?>" placeholder="Apenas para faturação" maxlength="9">

            <label>Estado</label>
            <select name="estado_atleta">
                <option value="ativo" <?= ($editar['estado_atleta'] ?? '') == 'ativo' ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo" <?= ($editar['estado_atleta'] ?? '') == 'inativo' ? 'selected' : '' ?>>Inativo</option>
            </select>

            <button type="submit" class="btn-guardar">Guardar</button>
            <?php if ($editar): ?>
                <a href="atletas.php" style="margin-left:10px; color:red; font-size:13px;">Cancelar</a>
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
            <th>Ações</th>
        </tr>
        <?php foreach ($atletas as $a): ?>
        <tr>
            <td><?= $a['nome'] ?></td>
            <td><?= $a['email'] ?></td>
            <td><?= $a['documento_tipo'] ?> — <?= $a['documento_numero'] ?></td>
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