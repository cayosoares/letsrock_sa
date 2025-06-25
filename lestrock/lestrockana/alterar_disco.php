<?php
session_start();
require 'conexao.php';

// Verifica se o usuário tem permissão de ADM (perfil_id = 1)
if (!isset($_SESSION['perfil_id']) || $_SESSION['perfil_id'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href='admin_dashboard.php';</script>";
    exit();
}

// Inicializa variável
$disco = null;

// Se o formulário for enviado, busca o disco pelo ID ou nome
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['busca_disco'])) {
        $busca = trim($_POST['busca_disco']);

        if (is_numeric($busca)) {
            $sql = "SELECT * FROM discos WHERE id = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM discos WHERE titulo LIKE :busca_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
        }

        $stmt->execute();
        $disco = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$disco) {
            echo "<script>alert('Disco não encontrado!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alterar Disco</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js"></script>
</head>
<body>
    <h2>Alterar Disco</h2>

    <!-- Formulário para buscar disco pelo ID ou Nome -->
    <form action="alterar_disco.php" method="POST">
        <label for="busca_disco">Digite o ID ou Título do Disco:</label>
        <input type="text" id="busca_disco" name="busca_disco" required onkeyup="buscarSugestoes()">

        <div id="sugestoes"></div>

        <button type="submit">Buscar</button>
    </form>

    <?php if ($disco): ?>
        <!-- Formulário para alterar disco -->
        <form action="processa_alteracao_disco.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($disco['id']) ?>">

            <label for="titulo">Nome:</label>
            <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($disco['titulo']) ?>" required>

            <label for="artista">Artista:</label>
            <textarea id="artista" name="artista" required><?= htmlspecialchars($disco['artista']) ?></textarea>

            <label for="ano">Ano:</label>
            <input type="number" id="ano" name="ano" value="<?= htmlspecialchars($disco['ano']) ?>" required min="0">

            <label for="preco">Preço:</label>
            <input type="number" step="0.01" id="preco" name="preco" value="<?= htmlspecialchars($disco['preco']) ?>" required min="0">

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>

    <a href="admin_dashboard.php">Voltar</a>
</body>
</html>
