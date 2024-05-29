<?php
session_start();

function check_connection_error($conn) {
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }
}

function redirect($url) {
    header("Location: $url");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'rh');
check_connection_error($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE nome_usuario = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                setcookie("user", $username, time() + (86400 * 30), "/"); // 86400 = 1 day
                redirect('cadastro.php'); // Alterado para redirecionar para cadastro.php
            } else {
                $error = "Senha incorreta.";
            }
        } else {
            $error = "Usuário não encontrado.";
        }

        $stmt->close();
    } else {
        $error = "Erro na preparação da consulta: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <form method="post" action="">
        <label for="username">Usuário:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Senha:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
    <p style="color:red;"><?php echo isset($error) ? $error : ''; ?></p>
    <p><a href="cadastrologin.php">Cadastrar</a></p>
</body>
</html>


<?php
/*
session_start();
//include 'db.php';
function check_connection_error($conn) {
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function check_login() {
    if (!isset($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

$conn = new mysqli('localhost', 'root', '', 'rh');
check_connection_error($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE nome_usuario = ?");

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();


    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            setcookie("user", $username, time() + (86400 * 30), "/"); // 86400 = 1 day
            redirect('index.php');
        } else {
            $error = "Senha incorreta.";
        }
    } else {
        $error = "Usuário não encontrado.";
    }

    $stmt->close();
} else {
    $error = "Erro na preparação da consulta: " . $conn->error;
}
}
$conn->close();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" 
    content="width=device-width, initial-scale=1.0">
    <title>Login</title>

</head>
<body>
    <h2>Login</h2>
    <form method="post" action="">
        <label for="username">Usuário:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Senha:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
    <p style="color:red;"><?php echo $error; ?></p>
    <p><a href="cadastrologin.php">Cadastrar</a> 
</body>
</html>*/
?>