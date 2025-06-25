<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Buscar dados do usuário
$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

// Buscar alguns discos em destaque
$discos_destaque = [];
try {
    $sql_discos = "SELECT * FROM discos ORDER BY RAND() LIMIT 6";
    $stmt_discos = $pdo->prepare($sql_discos);
    $stmt_discos->execute();
    $discos_destaque = $stmt_discos->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Se não houver discos, continuará com array vazio
}

// Buscar carrinho do usuário
$itens_carrinho = 0;
try {
    $sql_carrinho = "SELECT COUNT(*) as total FROM carrinhos c 
                     JOIN itens_carrinho ic ON c.id = ic.carrinho_id 
                     WHERE c.usuario_id = :usuario_id AND c.finalizado = 0";
    $stmt_carrinho = $pdo->prepare($sql_carrinho);
    $stmt_carrinho->bindParam(':usuario_id', $usuario_id);
    $stmt_carrinho->execute();
    $resultado = $stmt_carrinho->fetch(PDO::FETCH_ASSOC);
    $itens_carrinho = $resultado['total'];
} catch(PDOException $e) {
    // Se houver erro, manterá 0
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Início - Let's Rock - Disco Store</title>
    <link rel="stylesheet" type="text/css" href="css/site.css"/> 
</head>
<body>
    <header class="topo">
        <div class="logo">
        <img src="imgs/logo.png" alt="Logo da página" title="Logo da página"> <!-- logo da let's rock-->
        </div>

        <div id="form-busca">
        <input type="text" id="busca" placeholder="Digite o nome do artista ou álbum...">
        <button id="botao-buscar">Buscar</button>

    <a href="config.php" id="botao-configuracoes" title="Configurações">
        <img src="imgs/config.png" alt="Configurações" />
    </a>

    <a href="carrinho.php" id="botao-carrinho" title="Carrinho">
        <img src="imgs/carrinho.png" alt="Carrinho" />
    </a>

    <a href="logout.php" id="botao-logout" title="Logout">
        <img src="imgs/logout.png" alt="Logout"/>

    <a href="meus_pedidos.php" id="botao-meuspedidos" title="Meus Pedidos">
        <img src="imgs/box.png" alt="Meus pedidos"/>
    </a>

</div>
    </header>
    <br>
    <br>
    <script src="js/index.js"></script>

    <div id="olauser">
        <h3>Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</h3>
    </div>

    <main id="lista-produtos" class="produtos-container">
    <!-- Produtos vão aparecer por js -->
    </main>

      <button id="carregar-mais" style="display: block; margin: 20px auto;">Carregar mais</button>


    <br>
    <br>  

    <!-- início do footer/cabeçalho -->
    <footer>
      <div class="footer-container">
        <!-- link - redes sociais -->
        <div class="footer-section">
          <a href="https://instagram.com/letsrock_discostore" target="_blank">
            <img src="imgs/instagram.png" alt="Instagram" class="icon">
          </a>
          <a href="https://x.com/letsrock_ds" target="_blank">
            <img src="imgs/twitter.png" alt="X (Twitter)" class="icon">
          </a>
          <p>SIGA-NOS</p>
        </div>
    
        <!-- logo enorme no meio -->
        <div class=".logo">
          <a href="dashboard.php" target="_blank">
          <img src="imgs/logo.png" alt="Logo Let's Rock Disco Store" class="logo">
          </a>
        </div>
    
        <!-- nossos contatos -->
        <div class="footer-section">
          <a href="mailto:contato@letsrock.com">
            <img src="imgs/gmail.png" alt="Email" class="icon">
          </a>
          <a href="https://wa.me/5547999169146" target="_blank">
            <img src="imgs/whatsapp.png" alt="WhatsApp" class="icon">
          </a>
          <p>CONTATE-NOS</p>
        </div>
      </div>
    </footer> <!-- fim do footer/cabeçalho -->
</body>
</html>

