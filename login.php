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
        body { font-family: Arial; background: #f0f0f0; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: white; padding: 40px; border-radius: 8px; width: 350px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: green; margin-bottom: 5px; }
        .subtitulo { text-align: center; color: #888; font-size: 13px; margin-bottom: 20px; }
        input { width: 100%; padding: 10px; margin: 6px 0 12px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: green; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; }
        button:hover { background: darkgreen; }
        .erro { color: red; text-align: center; margin-bottom: 10px; font-size: 13px; }
        .link { text-align: center; margin-top: 15px; font-size: 13px; }
        a { color: green; }
    </style>
</head>
<body>
<div class="box">
    <h2>Clube de Ténis e Pádel</h2>
    <p class="subtitulo">Inicia sessão para acederes à tua conta</p>
    <?php if ($erro): ?>
        <p class="erro"><?= $erro ?></p>
    <?php endif; ?>
    <form method="POST">
        <label style="font-size:13px;">Email</label>
        <input type="email" name="email" placeholder="o-teu-email@exemplo.com" required>
        <label style="font-size:13px;">Password</label>
        <input type="password" name="password" placeholder="A tua password" required>
        <button type="submit">Entrar</button>
    </form>
    <div class="link">
        <a href="registro.php">Não tens conta? Regista-te aqui</a>
    </div>
</div>
</body>
</html>