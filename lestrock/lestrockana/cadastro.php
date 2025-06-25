<?php
session_start();
require_once 'conexao.php';

$mensagem = '';
$erro = '';

// Para debug - remover em produção
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $nome = trim($_POST['nome']);
    $nascimento = trim($_POST['nascimento']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $senha = $_POST['senha'];
    
    // Validações básicas
    if (empty($nome) || empty($email) || empty($nascimento) || empty($telefone) || empty($senha)) {
        $erro = 'Todos os campos são obrigatórios!';
    } else {
        // Validar formato da data de nascimento
        if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $nascimento)) {
            $erro = 'Data de nascimento deve estar no formato DD/MM/AAAA!';
        } else {
            // Converter data de DD/MM/AAAA para YYYY-MM-DD (formato MySQL)
            $data_parts = explode('/', $nascimento);
            $nascimento_mysql = $data_parts[2] . '-' . $data_parts[1] . '-' . $data_parts[0];
            
            // Verificar se a data é válida
            if (!checkdate($data_parts[1], $data_parts[0], $data_parts[2])) {
                $erro = 'Data de nascimento inválida!';
            } else {
                // Hash da senha
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                
                try {
                    // Verificar se email já existe
                    $sql_check = "SELECT id FROM usuarios WHERE email = :email";
                    $stmt_check = $pdo->prepare($sql_check);
                    $stmt_check->bindParam(':email', $email);
                    $stmt_check->execute();
                    
                    if ($stmt_check->rowCount() > 0) {
                        $erro = 'Este email já está cadastrado!';
                    } else {
                        // Inserir no banco de dados
                        $sql = "INSERT INTO usuarios(nome, nascimento, email, telefone, senha_hash) VALUES(:nome, :nascimento, :email, :telefone, :senha_hash)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':nome', $nome);
                        $stmt->bindParam(':nascimento', $nascimento_mysql);
                        $stmt->bindParam(':email', $email);
                        $stmt->bindParam(':telefone', $telefone);
                        $stmt->bindParam(':senha_hash', $senha_hash);

                        if($stmt->execute()){
                            $mensagem = 'Usuário cadastrado com sucesso!';
                            // Limpar variáveis após sucesso
                            $nome = $nascimento = $email = $telefone = '';
                        } else {
                            $erro = 'Erro ao cadastrar usuário. Tente novamente.';
                        }
                    }
                } catch(PDOException $e) {
                    if ($e->getCode() == 23000) { // Código de erro para duplicate entry
                        $erro = 'Este email já está cadastrado!';
                    } else {
                        $erro = 'Erro no banco de dados: ' . $e->getMessage();
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Let's Rock Disco Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
    <div class="particles" id="particles"></div>
    
    <header class="header">
        <div class="header-content">
            <h1 class="main-title">CADASTRE E DIVIRTA-SE</h1>
            <img src="imagens/logo.png" alt="Logo da página" title="Logo da página">
        </div>
    </header>

    <div class="container">
        <div class="contact-info">
            <h2 class="contact-title">Qualquer dúvida ou sugestão nos contate</h2>
            <div class="contact-email">
                <a href="mailto:contato@letsrock.com">LETSROCKDISOSTORE@GMAIL.COM</a>
            </div>
        </div>

        <div class="form-container">
            <h2 class="form-title">CADASTRO</h2>
            
            <?php if ($mensagem): ?>
                <div class="mensagem sucesso"><?php echo htmlspecialchars($mensagem); ?></div>
            <?php endif; ?>
            
            <?php if ($erro): ?>
                <div class="mensagem erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            
            <form id="cadastroForm" method="POST" action="">
                <div class="form-group">
                    <input type="text" name="nome" class="form-input" placeholder="Seu nome" 
                           value="<?php echo isset($nome) ? htmlspecialchars($nome) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <input type="text" name="nascimento" class="form-input" placeholder="Nascimento: DD/MM/AAAA" 
                           value="<?php echo isset($nascimento) ? htmlspecialchars($nascimento) : ''; ?>" 
                           pattern="\d{2}/\d{2}/\d{4}" title="Use o formato DD/MM/AAAA" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="E-mail" 
                           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <input type="tel" name="telefone" class="form-input" placeholder="Número de Telefone" 
                           value="<?php echo isset($telefone) ? htmlspecialchars($telefone) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <input type="password" name="senha" class="form-input" placeholder="Senha" required>
                </div>
                <button type="submit" class="submit-btn">CADASTRAR</button>
            </form>
            
            <div class="login-links">
                <a href="login.php" class="link">Já tem conta? Faça login</a>
            </div>
        </div>

        <div class="social-section">
            <h2 class="social-title">NOS ACOMPANHE EM NOSSAS<br>REDES SOCIAIS</h2>
            <div class="social-icons">
                <a href="https://instagram.com/letsrock_discostore" class="social-icon">@</a>
                <a href="https://x.com/letsrock_ds" class="social-icon">X</a>
            </div>
        </div>
    </div>
    <script src="js/js.js"></script>
</body>
</html>