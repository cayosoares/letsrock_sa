<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado e tem perfil de administrador (supondo que perfil 1 seja admin)
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 1) {
    echo "Acesso negado";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_prod = $_POST['nome_prod'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $qtde = $_POST['qtde'] ?? '';
    $valor_unit = $_POST['valor_unit'] ?? '';

    // Verifica se os campos obrigatórios estão preenchidos
    if (empty($nome_prod) || empty($descricao) || empty($qtde) || empty($valor_unit)) {
        echo "<script>alert('Todos os campos são obrigatórios.');</script>";
    }

        $sql = "INSERT INTO produto (nome_prod, descricao, qtde, valor_unit) VALUES (:nome_prod, :descricao, :qtde, :valor_unit)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome_prod', $nome_prod);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':qtde', $qtde);
        $stmt->bindParam(':valor_unit', $valor_unit);

        if ($stmt->execute()) {
            echo "<script>alert('Produto cadastrado com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar produto!');</script>";
        }
    }

?>


<!DOCTYPE html>
<html lang="pt-be">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produto</title>
    <link rel = "stylesheet" href = "styles.css">
</head>
<body>
    <h1>Ana Beatriz Alves</h1>
    <h2> Cadastrar Produto </h2> 
    <form action = "cadastro_produto.php" method = "POST">

    <label for="nome">Nome:</label><br>
    <input type="text" id="nome_prod" name="nome_prod" required><br><br>

    <label for="descricao">Descrição:</label><br>
    <input type="text" id="descricao" name="descricao" required><br><br>

    <label for="qtde">Quantidade:</label><br>
    <input type="number" id="qtde" name="qtde" required><br><br>

    <label for="valor_unit">Valor Unitário:</label><br>
    <input type="number" id="valor_unit" name="valor_unit" required><br><br>

        <button type = "submit">Cadastrar</button>
        <button type = "reset"> Cancelar </button>
</form>
<a href = "principal.php"> Voltar</a>

</body>
</html>
