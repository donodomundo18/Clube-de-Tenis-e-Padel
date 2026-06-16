<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function verificarLogin() {
    if (!isset($_SESSION['atleta_id']) && !isset($_SESSION['operador_id'])) {
        header('Location: ../login.php');
        exit();
    }
}

function verificarOperador() {
    if (!isset($_SESSION['operador_id'])) {
        header('Location: ../login.php');
        exit();
    }
}

function verificarGestor() {
    if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'gestor') {
        header('Location: ../login.php');
        exit();
    }
}

function estaLogado() {
    return isset($_SESSION['atleta_id']) || isset($_SESSION['operador_id']);
}
?>