<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado e é admin (perfil_id = 1)
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['perfil_id']) || $_SESSION['perfil_id'] != 1) {
    header('Location: login.php');
    exit();
}

$nomes_perfis = [
    1 => 'Super Administrador',
    2 => 'Gerente de Vendas',
    3 => 'Gerente de Estoque',
    4 => 'Vendedor'
];

// Pegando o ID do perfil do usuário logado
$id_perfil = $_SESSION['perfil_id'];

if (!$id_perfil) {
    die('Perfil do usuário não definido.');
}

// Definição das permissões por perfil
$permissoes = [
    1 => [
        "Cadastrar" => [
            "cadastro_usuario.php",
            "cadastro_cliente.php",
            "cadastro_produto.php"
        ],
        "Buscar" => [
            "buscar_usuario.php",
            "buscar_cliente.php",
            "buscar_produto.php"
        ],
        "Alterar" => [
            "alterar_usuario.php",
            "alterar_cliente.php",
            "alterar_disco.php"  // ALTERADO para alterar_disco.php
        ],
        "Excluir" => [
            "excluir_usuario.php",
            "excluir_cliente.php",
            "excluir_produto.php"
        ]
    ],
    2 => [
        "Cadastrar" => [
            "cadastro_cliente.php"
        ],
        "Buscar" => [
            "buscar_cliente.php",
            "buscar_produto.php"
        ],
        "Alterar" => [
            "alterar_cliente.php"
        ]
    ],
    3 => [
        "Cadastrar" => [
            "cadastro_produto.php"
        ],
        "Buscar" => [
            "buscar_cliente.php",
            "buscar_produto.php"
        ],
        "Alterar" => [
            "alterar_disco.php"  // ALTERADO para alterar_disco.php
        ],
        "Excluir" => [
            "excluir_produto.php"
        ]
    ],
    4 => [
        "Cadastrar" => [
            "cadastro_cliente.php"
        ],
        "Buscar" => [
            "buscar_produto.php"
        ],
        "Alterar" => [
            "alterar_cliente.php"
        ]
    ]
];

// Obter permissões para o perfil do usuário
$opcoes_menu = $permissoes[$id_perfil] ?? [];

// Obter nome do perfil
$nome_perfil = $nomes_perfis[$id_perfil] ?? 'Perfil desconhecido';

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Painel Principal</title>
    <link rel="stylesheet" href="css/admin.css" />
    <script src="js/js.js"></script>
</head>

<body>
    <h1>Painel Administrativo</h1>
    <header>
        <div class="saudacao">
        <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?>! Perfil: <?php echo htmlspecialchars($nome_perfil); ?></h2>
        </div>
        <div class="logout">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>
    <nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?= htmlspecialchars($categoria) ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($arquivos as $arquivo): ?>
                            <li>
                                <a href="<?= htmlspecialchars($arquivo) ?>">
                                    <?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</body>

</html>
