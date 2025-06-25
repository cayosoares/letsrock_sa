<?php
require_once 'conexao.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado.");
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = "";

// --- TRATAMENTO DO FORMULÁRIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Excluir conta
    if (isset($_POST['excluir'])) {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        session_destroy();
        header("Location: dashboard.php");
        exit;
    }

    // Atualização dos dados - apenas se botão salvar foi clicado
    if (isset($_POST['salvar'])) {
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $confirmar = $_POST['confirmar_senha'] ?? '';

        if (!empty($senha) && $senha !== $confirmar) {
            $mensagem = "As senhas não coincidem.";
        } else {
            // Atualizar usuário (sem idioma)
            if (!empty($senha)) {
                $sql = "UPDATE usuarios SET nome = ?, email = ?, telefone = ?, senha_hash = SHA2(?, 256) WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $nome, $email, $telefone, $senha, $usuario_id);
            } else {
                $sql = "UPDATE usuarios SET nome = ?, email = ?, telefone = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $nome, $email, $telefone, $usuario_id);
            }
            $stmt->execute();

            // Cliente (endereço)
            $sqlVerifica = "SELECT id FROM clientes WHERE usuario_id = ?";
            $stmtVerifica = $conn->prepare($sqlVerifica);
            $stmtVerifica->bind_param("i", $usuario_id);
            $stmtVerifica->execute();
            $resultado = $stmtVerifica->get_result();

            if ($resultado->num_rows > 0) {
                $sqlCliente = "UPDATE clientes SET endereco = ? WHERE usuario_id = ?";
                $stmtCliente = $conn->prepare($sqlCliente);
                $stmtCliente->bind_param("si", $endereco, $usuario_id);
            } else {
                $sqlCliente = "INSERT INTO clientes (usuario_id, nome, telefone, endereco) VALUES (?, ?, ?, ?)";
                $stmtCliente = $conn->prepare($sqlCliente);
                $stmtCliente->bind_param("isss", $usuario_id, $nome, $telefone, $endereco);
            }
            $stmtCliente->execute();

            $mensagem = "Configurações atualizadas com sucesso.";
        }
    }
}

// --- CARREGAR DADOS PARA EXIBIÇÃO ---
$sqlUsuario = "SELECT nome, email, telefone FROM usuarios WHERE id = ?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("i", $usuario_id);
$stmtUsuario->execute();
$resultUsuario = $stmtUsuario->get_result();
$usuario = $resultUsuario->fetch_assoc();

$sqlCliente = "SELECT endereco FROM clientes WHERE usuario_id = ?";
$stmtCliente = $conn->prepare($sqlCliente);
$stmtCliente->bind_param("i", $usuario_id);
$stmtCliente->execute();
$resultCliente = $stmtCliente->get_result();
$cliente = $resultCliente->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Configurações da Conta</title>
  <link rel="stylesheet" href="css/configg.css">
</head>
<body>
<header class="topo">
          <div class=".logo">
          <a href="dashboard.php" target="_blank">
          <img src="imgs/voltar.png" alt="Voltar" class="logo">
          </a>
        </div>
</header>
  <div class="container">
    <h1>Configurações da Conta</h1>

    <?php if (!empty($mensagem)): ?>
      <p style="color: green;"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <form method="POST">
      <!-- Dados Pessoais -->
      <section class="config-section">
        <h2>Dados Pessoais</h2>
        <label for="nome">Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

        <label for="telefone">Telefone:</label>
        <input type="tel" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>">
      </section>

      <!-- Endereço -->
      <section class="config-section">
        <h2>Endereço</h2>
          <label for="endereco">CEP:</label>
          <input type="text" id="endereco" name="endereco" maxlength="9" placeholder="00000-000" required>

          <label for="logradouro">Logradouro:</label>
          <input type="text" id="logradouro" name="logradouro" readonly>

          <label for="bairro">Bairro:</label>
          <input type="text" id="bairro" name="bairro" readonly>

          <label for="cidade">Cidade:</label>
          <input type="text" id="cidade" name="cidade" readonly>

          <label for="estado">Estado:</label>
          <input type="text" id="estado" name="estado" readonly>

          <label for="numero">Número:</label>
          <input type="text" id="numero" name="numero" placeholder="Digite o número da casa" required>
  

      </section>

      <!-- Segurança -->
      <section class="config-section">
        <h2>Segurança</h2>
        <label for="senha">Nova Senha:</label>
        <input type="password" name="senha">

        <label for="confirmar_senha">Confirmar Senha:</label>
        <input type="password" name="confirmar_senha">
      </section>

      <div class="actions">
        <button type="submit" name="salvar">Salvar</button>
        <button type="submit" name="excluir" onclick="return confirm('Tem certeza que deseja excluir sua conta?');" class="excluir">Excluir conta</button>
      </div>
    </form>
  </div>
  <script src="js/cep.js"></script>
</body>
</html>