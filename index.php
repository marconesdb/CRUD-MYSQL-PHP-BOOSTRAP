<?php
require_once "conexao.php";

// Obter a estrutura da tabela pessoas
$table_info = $conn->query("DESCRIBE pessoas");
$columns = array();
while ($row = $table_info->fetch_assoc()) {
    $columns[] = $row['Field'];
}

$sql = "SELECT * FROM pessoas WHERE nome LIKE '%".(isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '')."%' OR endereco LIKE '%".(isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '')."%' OR telefone LIKE '%".(isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '')."%' OR email LIKE '%".(isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '')."%' OR cpf LIKE '%".(isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '')."%'";
$resultado = $conn->query($sql);


// 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = isset($_POST['nome']) ? $_POST['nome'] : '';
    $endereco = isset($_POST['endereco']) ? $_POST['endereco'] : '';
    $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $cpf = isset($_POST['cpf']) ? $_POST['cpf'] : '';

    $sql = "UPDATE pessoas SET nome = ?, endereco = ?, telefone = ?, email = ?, cpf = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $endereco, $telefone, $email, $cpf, $id);

    if ($stmt->execute()) {
       
        // var_dump($nome, $endereco, $telefone, $email, $cpf, $id);
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_POST['nomeEditar'])) {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $data = array();
    $set_clause = "";

    foreach ($columns as $column) {
        if ($column == 'id') {
            continue; // Ignorar a coluna id
        }
        $value = isset($_POST[$column]) ? mysqli_real_escape_string($conn, $_POST[$column]) : '';
        $data[$column] = $value;
        $set_clause .= "$column = '$value',";
    }
    $set_clause = rtrim($set_clause, ',');

    if (empty($id)) {
        // Se o id estiver vazio, não atualize a linha
        echo "O ID não pode ser vazio.";
    } else {
        $sql = "UPDATE pessoas SET $set_clause WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            // Atualização bem-sucedida
        } else {
            echo "Erro: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD PHP, Bootstrap e MySQL</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Usuários</h2>
        <a href="criar.php" class="btn btn-primary mb-3">Criar Novo Usuário</a>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
            <div class="form-group">
                <label for="pesquisa">Pesquisar:</label>
                <input type="text" class="form-control" id="pesquisa" name="pesquisa" value="<?php echo isset($_GET['pesquisa']) ? $_GET['pesquisa'] : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Pesquisar</button>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>CPF</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['nome']; ?></td>
                        <td><?php echo $row['endereco']; ?></td>
                        <td><?php echo $row['telefone']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['cpf']; ?></td>
                        <td>
                        <a href="#" data-toggle="modal" data-target="#modalEditar" data-id="<?php echo $row['id']; ?>" data-nome="<?php echo $row['nome']; ?>" data-endereco="<?php echo $row['endereco']; ?>" data-telefone="<?php echo $row['telefone']; ?>" data-email="<?php echo $row['email']; ?>" data-cpf="<?php echo $row['cpf']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="excluir.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">Editar Usuário</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditar" action="" method="post">
                    <input type="hidden" id="idEditar" name="id">
                    <div class="form-group">
                        <label for="nomeEditar">Nome:</label>
                        <input type="text" class="form-control" id="nomeEditar" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="enderecoEditar">Endereço:</label>
                        <input type="text" class="form-control" id="enderecoEditar" name="endereco" required>
                    </div>
                    <div class="form-group">
                        <label for="telefoneEditar">Telefone:</label>
                        <input type="text" class="form-control" id="telefoneEditar" name="telefone" required>
                    </div>
                    <div class="form-group">
                        <label for="emailEditar">Email:</label>
                        <input type="email" class="form-control" id="emailEditar" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="cpfEditar">CPF:</label>
                        <input type="text" class="form-control" id="cpfEditar" name="cpf" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEditar" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
$(document).ready(function() {
    $('#modalEditar').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Botão que acionou o modal
        var id = button.data('id'); // Extrair o ID do atributo data-id
        var nome = button.data('nome'); // Extrair o nome do atributo data-nome
        var endereco = button.data('endereco'); // Extrair o endereço do atributo data-endereco
        var telefone = button.data('telefone'); // Extrair o telefone do atributo data-telefone
        var email = button.data('email'); // Extrair o email do atributo data-email
        var cpf = button.data('cpf'); // Extrair o CPF do atributo data-cpf

        var modal = $(this);
        modal.find('#idEditar').val(id);
        modal.find('#nomeEditar').val(nome);
        modal.find('#enderecoEditar').val(endereco);
        modal.find('#telefoneEditar').val(telefone);
        modal.find('#emailEditar').val(email);
        modal.find('#cpfEditar').val(cpf);
    });
});
</script>


</body>
</html>