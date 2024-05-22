
<?php
// Conectar ao banco de dados
$conn = new mysqli('localhost', 'root', 'xandre@2024', 'rh_db');
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verifica se o ID foi passado para editar o registro
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $idade = $_POST['idade'];
    $cargo = $_POST['cargo'];
    $beneficio_vt = $_POST['beneficio_vt'];
    $beneficio_vr = $_POST['beneficio_vr'];
    $plano_saude = $_POST['plano_saude'];

    // Atualiza os dados do funcionário
    $sql = "UPDATE funcionarios SET nome='$nome', idade='$idade', cargo='$cargo', beneficio_vt='$beneficio_vt', beneficio_vr='$beneficio_vr', plano_saude='$plano_saude' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Funcionário atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar o funcionário: " . $conn->error;
    }
} else {
    // Caso não tenha sido passado um ID, trata-se de um novo cadastro
    $nome = $_POST['nome'];
    $idade = $_POST['idade'];
    $cargo = $_POST['cargo'];
    $beneficio_vt = $_POST['beneficio_vt'];
    $beneficio_vr = $_POST['beneficio_vr'];
    $plano_saude = $_POST['plano_saude'];

    // Insere um novo funcionário
    $sql = "INSERT INTO funcionarios (nome, idade, cargo, beneficio_vt, beneficio_vr, plano_saude) VALUES ('$nome', '$idade', '$cargo', '$beneficio_vt', '$beneficio_vr', '$plano_saude')";
    if ($conn->query($sql) === TRUE) {
        echo "Funcionário cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar o funcionário: " . $conn->error;
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionários</title>
</head>
<body>
    <h2>Cadastro de Funcionários</h2>
    <form action="register_process.php" method="post">
        <input type="hidden" name="id" value="">
        <label for="nome">Nome:</label><br>
        <input type="text" id="nome" name="nome" required><br>
        <label for="idade">Idade:</label><br>
        <input type="number" id="idade" name="idade"><br>
        <label for="cargo">Cargo:</label><br>
        <input type="text" id="cargo" name="cargo"><br>
        <label for="beneficio_vt">Benefício VT:</label><br>
        <input type="text" id="beneficio_vt" name="beneficio_vt"><br>
        <label for="beneficio_vr">Benefício VR:</label><br>
        <input type="text" id="beneficio_vr" name="beneficio_vr"><br>
        <label for="plano_saude">Plano de Saúde:</label><br>
        <input type="text" id="plano_saude" name="plano_saude"><br><br>
        <input type="submit" value="Cadastrar">
    </form>

    <h2>Lista de Funcionários</h2>
    <table>
        <tr>
            <th>Nome</th>
            <th>Idade</th>
            <th>Cargo</th>
            <th>Benefício VT</th>
            <th>Benefício VR</th>
            <th>Plano de Saúde</th>
            <th>Ações</th>
        </tr>
        <?php
        // Conectar ao banco de dados
        $conn = new mysqli('localhost', 'root', 'xandre@2024', 'rh_db');
        if ($conn->connect_error) {
            die("Erro de conexão: " . $conn->connect_error);
        }

        // Consulta SQL para selecionar todos os registros da tabela funcionarios
        $sql_select = "SELECT * FROM funcionarios";
        $result = $conn->query($sql_select);

        // Verifica se existem registros
        if ($result->num_rows > 0) {
            // Loop através de cada linha de resultado
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['nome'] . "</td>";
                echo "<td>" . $row['idade'] . "</td>";
                echo "<td>" . $row['cargo'] . "</td>";
                echo "<td>" . $row['beneficio_vt'] . "</td>";
                echo "<td>" . $row['beneficio_vr'] . "</td>";
                echo "<td>" . $row['plano_saude'] . "</td>";
                // Adiciona links de edição e exclusão com o ID do funcionário como parâmetro na URL
                echo "<td><a href='edit.php?id=" . $row['id'] . "'>Editar</a> | <a href='delete.php?id=" . $row['id'] . "'>Excluir</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Nenhum funcionário cadastrado</td></tr>";
        }
        ?>
    </table>
</body>
</html>
