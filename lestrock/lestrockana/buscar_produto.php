<?php

session_start();
require_once 'conexao.php';


//Verifica se o usuario tem permissao
//Supondo que o perfil seja o Administrador
if($_SESSION['perfil'] != 1 && $_SESSION['perfil']!=2){
    echo "<script>alert('ACESSO NEGADO');window.location.href='principal.php';</script>";
    exit();
}

$produtos = []; // Inicializa variável

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);
    if (is_numeric($busca)) {
        $sql = "SELECT * FROM produto WHERE id_produto = :busca ORDER BY nome_prod ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM produto WHERE nome_prod LIKE :busca_nome_prod ORDER BY nome_prod ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome_prod', "%$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT * FROM produto ORDER BY nome_prod ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Produto</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js"></script>
</head>
<body>
    <h1>Ana Beatriz Alves</h1>
    <h2>Lista de Produtos</h2>
    <!-- Formulário para buscar produtos -->
    <form action="buscar_produto.php" method="POST">
        <label for="busca">Digite o ID ou nome (OPCIONAL):</label>
        <input type="text" id="busca" name="busca" required>

        <button type="submit">Pesquisar</button>
    </form>
    <?php if (!empty($produtos)): ?>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Quantidade</th>
            <th>Valor Un</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($produtos as $produto): ?>
        <tr>
            <td><?= htmlspecialchars($produto['id_produto']); ?></td>
            <td><?= htmlspecialchars($produto['nome_prod']); ?></td>
            <td><?= htmlspecialchars($produto['descricao']); ?></td>
            <td><?= htmlspecialchars($produto['qtde']); ?></td>
            <td><?= htmlspecialchars($produto['valor_unit']); ?></td>
            <td>
                <a href="alterar_produto.php?id=<?= htmlspecialchars($produto['id_produto']); ?>">Alterar</a>
                <a href="excluir_produto.php?id=<?= htmlspecialchars($produto['id_produto']); ?>" onclick="return confirm('Tem certeza que deseja excluir esse produto?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Nenhum Produto Encontrado</p>
<?php endif; ?>

    <a href="principal.php">Voltar</a>
</body>
</html>