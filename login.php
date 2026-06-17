<?php
require_once 'config/db.php';
require_once 'includes/session.php';

if (estaLogado()) {
    header('Location: index.php');
    exit();
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // verificar se é atleta
    $stmt = $pdo->prepare("SELECT * FROM atleta WHERE email = ? AND estado_atleta = 'ativo'");
    $stmt->execute([$email]);
    $atleta = $stmt->fetch();

    if ($atleta && password_verify($password, $atleta['password'])) {
        $_SESSION['atleta_id'] = $atleta['id'];
        $_SESSION['nome'] = $atleta['nome'];
        $_SESSION['tipo'] = 'atleta';
        header('Location: index.php');
        exit();
    }

    // verificar se é gestor ou rececionista
    $stmt = $pdo->prepare("SELECT * FROM operador WHERE email = ? AND estado = 'ativo'");
    $stmt->execute([$email]);
    $operador = $stmt->fetch();

    if ($operador && password_verify($password, $operador['password'])) {
        $_SESSION['operador_id'] = $operador['id'];
        $_SESSION['nome'] = $operador['nome'];
        $_SESSION['tipo'] = $operador['tipo'];
        header('Location: admin/dashboard.php');
        exit();
    }

    $erro = 'Email ou password incorretos. Tenta novamente!';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login - Clube de Ténis e Pádel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #1e3a5f; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .box { background: white; padding: 40px; border-radius: 10px; width: 370px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        .titulo { text-align: center; color: #1e3a5f; font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .subtitulo { text-align: center; color: #888; font-size: 13px; margin-bottom: 25px; }
        label { font-size: 13px; color: #555; display: block; margin-bottom: 4px; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; }
        input:focus { border-color: #1e3a5f; outline: none; }
        button { width: 100%; padding: 12px; background: #f47c3c; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; }
        button:hover { background: #d4622c; }
        .erro { background: #ffe0e0; color: red; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; text-align: center; }
        .link { text-align: center; margin-top: 15px; font-size: 13px; }
        a { color: #f47c3c; }
        a:hover { color: #d4622c; }
    </style>
</head>
<body>
<div class="box">
    <p class="titulo">Clube de Ténis e Pádel</p>
    <p class="subtitulo">Inicia sessão para acederes à tua conta</p>
    <?php if ($erro): ?>
        <div class="erro"><?= $erro ?></div>
    <?php endif; ?>
    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" placeholder="o-teu-email@exemplo.com" required>
        <label>Password</label>
        <input type="password" name="password" placeholder="A tua password" required>
        <button type="submit">Entrar</button>
    </form>
    <div class="link">
        <a href="registro.php">Não tens conta? Regista-te aqui</a>
    </div>
</div>
</body>
</html>