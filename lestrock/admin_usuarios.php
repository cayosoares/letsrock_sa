<?php
session_start();
require_once 'conexao.php';

// Verifica se é administrador
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['is_admin'])) {
    echo "<script>alert('Acesso restrito!'); window.location.href='login.php';</script>";
    exit;
}

// Excluir usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $excluir_id = $_POST['excluir_id'];

    // Protege o admin
    if ($excluir_id === 'admin') {
        echo "<script>alert('Você não pode excluir o administrador.');</script>";
    } else {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $excluir_id]);
        echo "<script>alert('Usuário excluído com sucesso!'); window.location.href='admin_usuarios.php';</script>";
        exit;
    }
}

// Buscar usuários
$usuarios = $pdo->query("SELECT id, nome, email, telefone, data_cadastro FROM usuarios ORDER BY data_cadastro DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Admin - Usuários</title>
    <link rel="stylesheet" href="css/admin_usuarios.css">
</head>
<body>
    <div class="container">
        <h1> Usuários Cadastrados</h1>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Data de Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['nome']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($usuario['telefone']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($usuario['data_cadastro'])) ?></td>
                    <td style="white-space: nowrap;">
                        <form method="post" action="admin_editar_usuario.php" style="display:inline;">

                            <input type="hidden" name="editar_id" value="<?= $usuario['id'] ?>">
                            <button type="submit" class="btn-editar">Editar</button>

                        </form>

                        <form method="post" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');" style="display:inline;">
                            <input type="hidden" name="excluir_id" value="<?= $usuario['id'] ?>">
                            <button type="submit" class="btn-excluir">Excluir</button>
                        </form>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="admin_dashboard.php" class="voltar-btn">← Voltar ao Painel</a>
    </div>
</body>
</html>
