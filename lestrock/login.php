<?php
session_start();
require_once 'conexao.php';

$mensagem = '';
$erro = '';

// Para debug - remover em produção
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurações do administrador
define('ADMIN_EMAIL', 'admin@letsrock.com');
define('ADMIN_SENHA', 'admin123');

// Verificar se usuário já está logado
if (isset($_SESSION['usuario_id'])) {
    if (!empty($_SESSION['is_admin'])) {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}

// Verificar mensagem do cadastro
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        $erro = 'Email e senha são obrigatórios!';
    } else {
        // Login de administrador
        if ($email === ADMIN_EMAIL && $senha === ADMIN_SENHA) {
            $_SESSION['usuario_id'] = 'admin';
            $_SESSION['usuario_nome'] = 'Administrador';
            $_SESSION['usuario_email'] = ADMIN_EMAIL;
            $_SESSION['is_admin'] = true;

            echo "<script>
                alert('Login de administrador realizado com sucesso!');
                window.location.href = 'admin_dashboard.php';
            </script>";
            exit;
        }

        // Login de usuário comum
        try {
            $sql = "SELECT id, nome, email, senha_hash FROM usuarios WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
        
            if ($stmt->rowCount() > 0) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $login_valido = false;
                
                // MÉTODO 1: Tentar password_verify() (para senhas criadas no cadastro)
                if (password_verify($senha, $usuario['senha_hash'])) {
                    $login_valido = true;
                    error_log("Login com password_hash para: " . $email);
                }
                
                // MÉTODO 2: Tentar SHA256 (para senhas do banco original)
                if (!$login_valido) {
                    $senhaHashSHA256 = hash('sha256', $senha);
                    if ($senhaHashSHA256 === $usuario['senha_hash']) {
                        $login_valido = true;
                        error_log("Login com SHA256 para: " . $email);
                        
                        // OPCIONAL: Migrar automaticamente para password_hash
                        try {
                            $novo_hash = password_hash($senha, PASSWORD_DEFAULT);
                            $sql_update = "UPDATE usuarios SET senha_hash = :novo_hash WHERE id = :id";
                            $stmt_update = $pdo->prepare($sql_update);
                            $stmt_update->bindParam(':novo_hash', $novo_hash);
                            $stmt_update->bindParam(':id', $usuario['id']);
                            $stmt_update->execute();
                            error_log("Senha migrada automaticamente para password_hash: " . $email);
                        } catch (Exception $e) {
                            error_log("Erro na migração automática: " . $e->getMessage());
                        }
                    }
                }
                
                if ($login_valido) {
                    // Login OK
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_email'] = $usuario['email'];
                    $_SESSION['is_admin'] = false;
        
                    echo "<script>
                            alert('Login realizado com sucesso!');
                            window.location.href = 'dashboard.php';
                          </script>";
                    exit;
                } else {
                    $erro = 'Email ou senha incorretos!';
                    error_log("Falha no login para: " . $email);
                }
        
            } else {
                $erro = 'Email ou senha incorretos!';
                error_log("Usuário não encontrado: " . $email);
            }
        
        } catch (PDOException $e) {
            $erro = 'Erro no banco de dados: ' . $e->getMessage();
            error_log("Erro PDO: " . $e->getMessage());
        }
    }
}    
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Let's Rock Disco Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/login.css"/>
    <script src="js/login.js" defer></script>
</head>
<body>
    <div class="particles" id="particles"></div>

    <header class="header">
        <div class="header-content">
            <h1 class="main-title">FAÇA SEU LOGIN</h1>
            <img src="imagens/logo.png" alt="Logo da página" title="Logo da página">
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <h2 class="form-title">LOGIN</h2>

            <?php if (!empty($mensagem)): ?>
                <div class="mensagem sucesso"><?php echo htmlspecialchars($mensagem); ?></div>
            <?php endif; ?>

            <?php if (!empty($erro)): ?>
                <div class="mensagem erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="E-mail"
                        value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <input type="password" name="senha" class="form-input" placeholder="Senha" required>
                </div>
                <div class="form-group">
                    <div class="checkbox-container">
                        <input type="checkbox" id="lembrar" name="lembrar" class="checkbox-input">
                        <label for="lembrar" class="checkbox-label">Lembrar de mim</label>
                    </div>
                </div>
                <button type="submit" class="submit-btn">ENTRAR</button>
            </form>

            <div class="login-links">
                <a href="cadastro.php" class="link">Não tem conta? Cadastre-se</a>
            </div>

            <div style="margin-top: 20px; padding: 15px; background: rgba(229, 184, 126, 0.1); border-radius: 10px; border: 1px solid rgba(229, 184, 126, 0.3);">
                <p style="color: rgba(235, 208, 164, 0.8); text-align: center; font-size: 0.85rem; margin: 0;">
                    <strong>🔧 SISTEMA HÍBRIDO ATIVO</strong><br>
                    <strong>Admin:</strong> admin@letsrock.com / admin123<br>
                    <strong>Usuários antigos:</strong> kaiodudu10@gmail.com / 123456<br>
                    <strong>Novos cadastros:</strong> Funcionam automaticamente<br>
                    <em>Sistema detecta automaticamente o tipo de senha</em>
                </p>
            </div>
        </div>
    </div>
</body>
</html>