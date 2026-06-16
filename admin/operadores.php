<?php
require_once '../config/db.php';
require_once '../includes/session.php';
verificarGestor();

$sucesso = '';
$erro = '';

// apagar operador
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
        // editar
        $stmt = $pdo->prepare("UPDATE operador SET nome=?, email=?, tipo=?, estado=? WHERE id=?");
        $stmt->execute([$nome, $email, $tipo, $estado, $_POST['id']]);
    } else {
        // adicionar - password padrao 123456
        $password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO operador (nome, email, password, tipo, estado) VALUES (?,?,?,?,?)");
        $stmt->execute([$nome, $email, $password, $tipo, $estado]);
    }
    header('Location: operadores.php');
    exit();
}

// buscar operadores
$operadores = $pdo->query("SELECT * FROM operador ORDER BY nome")->fetchAll();

// buscar operador para editar
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
        body { font-family: Arial; margin: 0; background: #f5f5f5; }
        nav { background: green; padding: 10px 20px; color: white; display: flex; justify-content: space-between; }
        nav a { color: white; margin-left: 10px; text-decoration: none; }
        .container { max-width: 900px; margin: 30px auto; padding: 20px; }
        h2 { color: green; }
        p { color: #888; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 13px; }
        th { background: green; color: white; }
        .btn { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; }
        .btn-editar { background: orange; color: white; }
        .btn-inativar { background: red; color: white; }
        .formulario { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd; }
        input, select { width: 100%; padding: 8px; margin: 5px 0 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn-guardar { background: green; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .ativo { color: green; }
        .inativo { color: red; }
    </style>
</head>
<body>
<nav>
    <span>Backoffice - Operadores</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="../logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <h2>Gerir Operadores</h2>
    <p>So o gestor pode aceder a esta pagina.</p>

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
                <a href="operadores.php" style="margin-left:10px; color:red;">Cancelar</a>
            <?php endif; ?>
        </form>
    </div>

    <h2>Lista de Operadores</h2>
    <table>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Acoes</th>
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