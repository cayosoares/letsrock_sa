<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário é admin (exemplo simples com email fixo)
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_email'] !== 'admin@letsrock.com') {
    echo "<script>alert('Acesso restrito!');window.location.href='login.php';</script>";
    exit;
}

// Atualização do estoque
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disco_id'], $_POST['novo_estoque'])) {
    $disco_id = $_POST['disco_id'];
    $novo_estoque = $_POST['novo_estoque'];

    $sql = "UPDATE discos SET estoque = :estoque WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['estoque' => $novo_estoque, 'id' => $disco_id]);

    echo "<script>alert('Estoque atualizado com sucesso!');</script>";
}

// Buscar discos
$discos = $pdo->query("SELECT id, titulo, estoque FROM discos ORDER BY titulo")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - Estoque</title>
    <link rel="stylesheet" href="css/estoque.css">
</head>
<body>
    <div class="container">
        <h2>Gerenciar Estoque</h2>
        <table>
            <tr>
                <th>Título</th>
                <th>Estoque Atual</th>
                <th>Alterar Estoque</th>
            </tr>
            <?php foreach ($discos as $disco): ?>
            <tr>
                <td><?= htmlspecialchars($disco['titulo']) ?></td>
                <td><?= $disco['estoque'] ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="disco_id" value="<?= $disco['id'] ?>">
                        <input type="number" name="novo_estoque" min="0" value="<?= $disco['estoque'] ?>" required>
                        <button type="submit">Atualizar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>

