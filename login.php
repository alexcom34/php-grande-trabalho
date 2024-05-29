<?php
// login.php
include '../includes/db.php';
include '../includes/functions.php';
redirect_if_logged_in();
?>

<!DOCTYPE html>

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>Login</h2>
    <form action="../actions/login_action.php" method="post">
        <label for="username">Nome de Usuário:</label>
        <input type="text" name="username" required>
        <label for="password">Senha:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <p><a href="forgot_password.php">Esqueci minha senha</a></p>
    <p>Não tem uma conta? <a href="register.php">Registrar</a></p>
</body>
</html>
