<?php
require_once 'config/db.php';
require_once 'includes/session.php';

if (estaLogado()) {
    header('Location: index.php');
    exit();
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmar = $_POST['confirmar'];
    $documento_tipo = $_POST['documento_tipo'];
    $documento_numero = trim($_POST['documento_numero']);
    $nif = trim($_POST['nif']);

    if ($password !== $confirmar) {
        $erro = 'As passwords que introduziste não coincidem!';
    } else {
        // verificar se o email já está registado
        $stmt = $pdo->prepare("SELECT id FROM atleta WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erro = 'Este email já está registado. Tenta fazer login!';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO atleta (jogador, nome, email, password, documento_tipo, documento_numero, nif) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $nome, $email, $hash, $documento_tipo, $documento_numero, $nif ?: null]);
            $sucesso = 'Conta criada com sucesso! Já podes fazer login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Registo - Clube de Ténis e Pádel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #1e3a5f; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .box { background: white; padding: 40px; border-radius: 10px; width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        .titulo { text-align: center; color: #1e3a5f; font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .subtitulo { text-align: center; color: #888; font-size: 13px; margin-bottom: 25px; }
        label { font-size: 13px; color: #555; display: block; margin-bottom: 4px; }
        input, select { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; box-sizing: border-box; }
        input:focus, select:focus { border-color: #1e3a5f; outline: none; }
        button { width: 100%; padding: 12px; background: #f47c3c; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; }
        button:hover { background: #d4622c; }
        .erro { background: #ffe0e0; color: red; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; text-align: center; }
        .sucesso { background: #e0ffe0; color: #1a5c1a; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; text-align: center; }
        .link { text-align: center; margin-top: 15px; font-size: 13px; }
        a { color: #f47c3c; }
        a:hover { color: #d4622c; }
    </style>
</head>
<body>
<div class="box">
    <p class="titulo">Clube de Ténis e Pádel</p>
    <p class="subtitulo">Regista-te para poderes reservar campos</p>
    <?php if ($erro): ?>
        <div class="erro"><?= $erro ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="sucesso"><?= $sucesso ?></div>
    <?php endif; ?>
    <form method="POST">
        <label>Nome completo</label>
        <input type="text" name="nome" placeholder="O teu nome completo" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="email@exemplo.com" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Escolhe uma password" required>

        <label>Confirmar Password</label>
        <input type="password" name="confirmar" placeholder="Repete a password" required>

        <label>Tipo de Documento</label>
        <select name="documento_tipo" required>
            <option value="">Seleciona o tipo...</option>
            <option value="Cartão de Cidadão">Cartão de Cidadão</option>
            <option value="Passaporte">Passaporte</option>
            <option value="Outro">Outro</option>
        </select>

        <label>Número do Documento</label>
        <input type="text" name="documento_numero" placeholder="Número do documento" required>

        <label>NIF (opcional)</label>
        <input type="text" name="nif" placeholder="Apenas para faturação" maxlength="9">

        <button type="submit">Criar Conta</button>
    </form>
    <div class="link">
        <a href="login.php">Já tens conta? Faz login aqui</a>
    </div>
</div>
</body>
</html>