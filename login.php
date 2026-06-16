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

    // Verificar se é atleta
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

    // Verificar se é operador (gestor ou rececionista)
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

    $erro = 'Email ou password incorretos!';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login - Clube de Ténis e Pádel</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f0f0; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: white; padding: 40px; border-radius: 10px; width: 350px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2c7a2c; }
        input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #2c7a2c; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #1e5e1e; }
        .erro { color: red; text-align: center; margin-bottom: 10px; }
        .link { text-align: center; margin-top: 15px; }
        a { color: #2c7a2c; }
    </style>
</head>
<body>
<div class="box">
    <h2>🎾 Clube de Ténis e Pádel</h2>
    <?php if ($erro): ?>
        <p class="erro"><?= $erro ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Entrar</button>
    </form>
    <div class="link">
        <a href="registro.php">Não tens conta? Regista-te aqui</a>
    </div>
</div>
</body>
</html>