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
        $erro = 'As passwords não coincidem!';
    } else {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT id FROM atleta WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erro = 'Este email já está registado!';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO atleta (jogador, nome, email, password, documento_tipo, documento_numero, nif) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $nome, $email, $hash, $documento_tipo, $documento_numero, $nif ?: null]);
            $sucesso = 'Conta criada com sucesso! Podes fazer login agora.';
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
        body { font-family: Arial, sans-serif; background: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .box { background: white; padding: 40px; border-radius: 10px; width: 400px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2c7a2c; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #2c7a2c; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #1e5e1e; }
        .erro { color: red; text-align: center; margin-bottom: 10px; }
        .sucesso { color: green; text-align: center; margin-bottom: 10px; }
        .link { text-align: center; margin-top: 15px; }
        a { color: #2c7a2c; }
        label { font-size: 13px; color: #555; }
    </style>
</head>
<body>
<div class="box">
    <h2>🎾 Criar Conta</h2>
    <?php if ($erro): ?>
        <p class="erro"><?= $erro ?></p>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <p class="sucesso"><?= $sucesso ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Nome completo</label>
        <input type="text" name="nome" placeholder="O teu nome" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="email@exemplo.com" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Password" required>

        <label>Confirmar Password</label>
        <input type="password" name="confirmar" placeholder="Confirmar password" required>

        <label>Tipo de Documento</label>
        <select name="documento_tipo" required>
            <option value="">Seleciona...</option>
            <option value="Cartão de Cidadão">Cartão de Cidadão</option>
            <option value="Passaporte">Passaporte</option>
            <option value="Outro">Outro</option>
        </select>

        <label>Número do Documento</label>
        <input type="text" name="documento_numero" placeholder="Número do documento" required>

        <label>NIF (opcional, apenas para faturação)</label>
        <input type="text" name="nif" placeholder="NIF (opcional)" maxlength="9">

        <button type="submit">Criar Conta</button>
    </form>
    <div class="link">
        <a href="login.php">Já tens conta? Faz login aqui</a>
    </div>
</div>
</body>
</html>