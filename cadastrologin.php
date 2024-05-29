
<?php
function check_connection_error($conn) {
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }
}

$conn = new mysqli('localhost', 'root', '', 'rh');
check_connection_error($conn);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $email = $_POST['email'];
        $pin_verification = $_POST['pin_verification'];
        
        // Verificar se o PIN de verificação está correto
        if ($pin_verification !== '1234') {  // Substitua '1234' pelo PIN de verificação desejado
            echo "PIN de verificação incorreto.";
        } else {
            // Verificar se o usuário já existe com base no e-mail
            $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows > 0) {
                echo "Usuário já existe com este e-mail.";
            } else {
                $nome_usuario = $_POST['nome_usuario'];
                $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
                
                $stmt_create = $conn->prepare("INSERT INTO usuarios (nome_usuario, email, senha) VALUES (?, ?, ?)");
                $stmt_create->bind_param("sss", $nome_usuario, $email, $senha);
                $stmt_create->execute();
                $stmt_create->close();
            }
            
            $stmt_check->close();
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $nome_usuario = $_POST['nome_usuario'];
        $email = $_POST['email'];
        
        if (!empty($_POST['senha'])) {
            $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET nome_usuario = ?, email = ?, senha = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nome_usuario, $email, $senha, $id);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET nome_usuario = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome_usuario, $email, $id);
        }
        
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch users for display
$result = $conn->query("SELECT * FROM usuarios");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Usuários</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>Gerenciamento de Usuários</h2>
    <form action="" method="post">
        <input type="hidden" name="id" id="user_id">
        <label for="nome_usuario">Nome de Usuário:</label>
        <input type="text" name="nome_usuario" id="nome_usuario" required pattern="[A-Za-z]+" title="Apenas letras são permitidas">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha">
        <label for="pin_verification">PIN de Verificação:</label>
        <input type="text" name="pin_verification" id="pin_verification" required>
        <button type="submit" name="create">Criar</button>
        <button type="submit" name="update">Atualizar</button>
        <button type="submit" name="delete">Deletar</button>
    </form>
    <h3>Lista de Usuários</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nome de Usuário</th>
            <th>Email</th>
            <th>Ação</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['nome_usuario']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td>
                <button onclick="editUser('<?php echo $row['id']; ?>', '<?php echo $row['nome_usuario']; ?>', '<?php echo $row['email']; ?>')">Editar</button>
                <button onclick="deleteUser('<?php echo $row['id']; ?>')">Deletar</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <script>
        function editUser(id, nome_usuario, email) {
            document.getElementById('user_id').value = id;
            document.getElementById('nome_usuario').value = nome_usuario;
            document.getElementById('email').value = email;
            document.getElementById('senha').value = '';
        }

        function deleteUser(id) {
            document.getElementById('user_id').value = id;
            document.querySelector('button[name="delete"]').click();
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
