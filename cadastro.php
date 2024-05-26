<?php

function check_connection_error($conn) {
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }
}


$conn = new mysqli('localhost', 'root', 'xandre@2024', 'rh');
check_connection_error($conn);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $matricula = isset($_POST['matricula']) ? intval($_POST['matricula']) : 0; 
        $nome = isset($_POST['nome']) ? $_POST['nome'] : '';
        $idade = isset($_POST['idade']) ? $_POST['idade'] : '';
        $cargo = isset($_POST['cargo']) ? $_POST['cargo'] : '';
        $beneficio_vt = isset($_POST['beneficio_vt']) ? $_POST['beneficio_vt'] : '';
        $beneficio_vr = isset($_POST['beneficio_vr']) ? $_POST['beneficio_vr'] : '';
        $plano_saude = isset($_POST['plano_saude']) ? $_POST['plano_saude'] : '';

        if ($_POST['action'] == 'create') {
            
            $stmt = $conn->prepare("INSERT INTO funcionarios (nome, idade, cargo, beneficio_vt, beneficio_vr, plano_saude) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sissss", $nome, $idade, $cargo, $beneficio_vt, $beneficio_vr, $plano_saude);
            if ($stmt->execute()) {
                echo "Funcionário cadastrado com sucesso!";
            } else {
                echo "Erro ao cadastrar o funcionário: " . $stmt->error;
            }
            $stmt->close();
        } elseif ($_POST['action'] == 'edit') {
         
            $stmt = $conn->prepare("UPDATE funcionarios SET nome=?, idade=?, cargo=?, beneficio_vt=?, beneficio_vr=?, plano_saude=? WHERE matricula=?");
            $stmt->bind_param("sissssi", $nome, $idade, $cargo, $beneficio_vt, $beneficio_vr, $plano_saude, $matricula);
            if ($stmt->execute()) {
                echo "Funcionário atualizado com sucesso!";
            } else {
                echo "Erro ao atualizar o funcionário: " . $stmt->error;
            }
            $stmt->close();
        } elseif ($_POST['action'] == 'delete') {
         
            $stmt = $conn->prepare("DELETE FROM funcionarios WHERE matricula=?");
            $stmt->bind_param("i", $matricula);
            if ($stmt->execute()) {
                echo "Funcionário excluído com sucesso!";
            } else {
                echo "Erro ao excluir o funcionário: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}


function get_employee_data($conn, $matricula) {
    $stmt = $conn->prepare("SELECT * FROM funcionarios WHERE matricula=?");
    $stmt->bind_param("i", $matricula);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
    $stmt->close();
    return $employee;
}

$employee_to_edit = null;
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['matricula'])) {
    $employee_to_edit = get_employee_data($conn, intval($_GET['matricula']));
}
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
    <form action="" method="post">
        <input type="hidden" name="action" value="<?php echo $employee_to_edit ? 'edit' : 'create'; ?>">
        <input type="hidden" name="matricula" value="<?php echo $employee_to_edit ? htmlspecialchars($employee_to_edit['matricula']) : ''; ?>">
        <label for="nome">Nome:</label><br>
        <input type="text" id="nome" name="nome" value="<?php echo $employee_to_edit ? htmlspecialchars($employee_to_edit['nome']) : ''; ?>"><br>
        <label for="idade">Idade:</label><br>
        <input type="number" id="idade" name="idade" value="<?php echo $employee_to_edit ? htmlspecialchars($employee_to_edit['idade']) : ''; ?>"><br>
        <label for="cargo">Cargo:</label><br>
        <input type="text" id="cargo" name="cargo" value="<?php echo $employee_to_edit ? htmlspecialchars($employee_to_edit['cargo']) : ''; ?>"><br>
        <label for="beneficio_vt">Benefício VT:</label><br>
        <input type="text" id="beneficio_vt" name="beneficio_vt" value="<?php echo $employee_to_edit ? htmlspecialchars($employee_to_edit['beneficio_vt']) : ''; ?>"><br>
        <label for="beneficio_vr">Benefício VR:</label><br>
        <input type="text" id="beneficio_vr" name="beneficio_vr" value="<?php echo $employee_to_edit ? htmlspecialchars($employee_to_edit['beneficio_vr']) : ''; ?>"><br>
        <label for="plano_saude">Plano de Saúde:</label><br>
        <input type="text" id="plano_saude" name="plano_saude" value="<?php echo $employee_to_edit ? htmlspecialchars($employee_to_edit['plano_saude']) : ''; ?>"><br><br>
        <input type="submit" value="<?php echo $employee_to_edit ? 'Atualizar' : 'Cadastrar'; ?>">
    </form>

    <h2>Lista de Funcionários</h2>
    <table border="1">
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
     
        $sql_select = "SELECT * FROM funcionarios";
        $result = $conn->query($sql_select);

       
        if ($result->num_rows > 0) {
         
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                echo "<td>" . htmlspecialchars($row['idade']) . "</td>";
                echo "<td>" . htmlspecialchars($row['cargo']) . "</td>";
                echo "<td>" . htmlspecialchars($row['beneficio_vt']) . "</td>";
                echo "<td>" . htmlspecialchars($row['beneficio_vr']) . "</td>";
                echo "<td>" . htmlspecialchars($row['plano_saude']) . "</td>";
            
                echo "<td>
                        <a href='?action=edit&matricula=" . htmlspecialchars($row['matricula']) . "'>Editar</a>
                        <form action='' method='post' style='display:inline'>
                            <input type='hidden' name='action' value='delete'>
                            <input type='hidden' name='matricula' value='" . htmlspecialchars($row['matricula']) . "'>
                            <input type='submit' value='Excluir'>
                        </form>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Nenhum funcionário cadastrado</td></tr>";
        }

        $conn->close();
        ?>
    </table>
</body>
</html>
