<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarGestor();

$sucesso = '';
$erro = '';

// inativar operador
if (isset($_GET['apagar'])) {
    $id = $_GET['apagar'];
    $stmt = $pdo->prepare("UPDATE operador SET estado = 'inativo' WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: operadores.php');
    exit();
}

// adicionar ou editar operador
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];

    if (isset($_POST['id']) && $_POST['id'] != '') {
        // editar operador existente
        $stmt = $pdo->prepare("UPDATE operador SET nome=?, email=?, tipo=?, estado=? WHERE id=?");
        $stmt->execute([$nome, $email, $tipo, $estado, $_POST['id']]);
    } else {
        // adicionar novo operador - password padrao 123456
        $password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO operador (nome, email, password, tipo, estado) VALUES (?,?,?,?,?)");
        $stmt->execute([$nome, $email, $password, $tipo, $estado]);
    }
    header('Location: operadores.php');
    exit();
}

$operadores = $pdo->query("SELECT * FROM operador ORDER BY nome")->fetchAll();

$editar = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM operador WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $editar = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Operadores - Backoffice</title>
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
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .formulario { background: white; padding: 22px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd; }
        .formulario h3 { color: #1e3a5f; margin-bottom: 15px; font-size: 15px; }
        label { font-size: 13px; color: #555; display: block; margin-bottom: 4px; }
        input, select { width: 100%; padding: 8px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 13px; }
        input:focus, select:focus { border-color: #1e3a5f; outline: none; }
        .btn-guardar { background: #f47c3c; color: white; padding: 10px 22px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn-guardar:hover { background: #d4622c; }
        .secao-titulo { color: #1e3a5f; font-size: 15px; margin: 20px 0 12px 0; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 13px; text-align: left; }
        th { background: #1e3a5f; color: white; }
        tr:hover { background: #f9f9f9; }
        .btn { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; }
        .btn-editar { background: #f47c3c; color: white; }
        .btn-editar:hover { background: #d4622c; }
        .btn-inativar { background: red; color: white; }
        .btn-inativar:hover { background: darkred; }
        .ativo { color: #1a6b1a; font-weight: bold; }
        .inativo { color: red; }
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
    <h1>Gerir Operadores</h1>
    <p>Apenas o gestor tem acesso a esta página</p>
</div>

<div class="container">
    <div class="formulario">
        <h3><?= $editar ? 'Editar Operador' : 'Adicionar Operador' ?></h3>
        <form method="POST">
            <?php if ($editar): ?>
                <input type="hidden" name="id" value="<?= $editar['id'] ?>">
            <?php endif; ?>

            <label>Nome</label>
            <input type="text" name="nome" value="<?= $editar['nome'] ?? '' ?>" placeholder="Nome do operador" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= $editar['email'] ?? '' ?>" placeholder="email@clube.pt" required>

            <label>Tipo</label>
            <select name="tipo" required>
                <option value="rececionista" <?= ($editar['tipo'] ?? '') == 'rececionista' ? 'selected' : '' ?>>Rececionista</option>
                <option value="gestor" <?= ($editar['tipo'] ?? '') == 'gestor' ? 'selected' : '' ?>>Gestor</option>
            </select>

            <label>Estado</label>
            <select name="estado">
                <option value="ativo" <?= ($editar['estado'] ?? '') == 'ativo' ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo" <?= ($editar['estado'] ?? '') == 'inativo' ? 'selected' : '' ?>>Inativo</option>
            </select>

            <button type="submit" class="btn-guardar">Guardar</button>
            <?php if ($editar): ?>
                <a href="operadores.php" style="margin-left:10px; color:red; font-size:13px;">Cancelar</a>
            <?php endif; ?>
        </form>
    </div>

    <p class="secao-titulo">Lista de Operadores</p>
    <table>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($operadores as $o): ?>
        <tr>
            <td><?= $o['nome'] ?></td>
            <td><?= $o['email'] ?></td>
            <td><?= $o['tipo'] ?></td>
            <td class="<?= $o['estado'] ?>"><?= $o['estado'] ?></td>
            <td>
                <a href="operadores.php?editar=<?= $o['id'] ?>" class="btn btn-editar">Editar</a>
                <a href="operadores.php?apagar=<?= $o['id'] ?>" class="btn btn-inativar" onclick="return confirm('Tens a certeza que queres inativar este operador?')">Inativar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>