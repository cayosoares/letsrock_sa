<?php
session_start();
require_once 'conexao.php';

$forma_pagamento = $_POST['forma_pagamento'] ?? null;
if (!$forma_pagamento) {
    echo "<script>alert('Selecione uma forma de pagamento.'); window.history.back();</script>";
    exit();
}


if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Você precisa estar logado para finalizar a compra.'); window.location='login.php';</script>";
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Buscar carrinho ativo
$sql = "SELECT id FROM carrinhos WHERE usuario_id = ? AND finalizado = 0 LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id]);
$carrinho = $stmt->fetch();

if (!$carrinho) {
    echo "<script>alert('Nenhum carrinho ativo encontrado.'); window.location='carrinho.php';</script>";
    exit();
}

$carrinho_id = $carrinho['id'];

// Buscar itens do carrinho
$sql = "SELECT disco_id, quantidade FROM itens_carrinho WHERE carrinho_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$carrinho_id]);
$itens = $stmt->fetchAll();

if (empty($itens)) {
    echo "<script>alert('Carrinho vazio.'); window.location='carrinho.php';</script>";
    exit();
}

// Calcular total
$total = 0;
foreach ($itens as $item) {
    $sqlPreco = "SELECT preco, estoque FROM discos WHERE id = ?";
    $stmtPreco = $pdo->prepare($sqlPreco);
    $stmtPreco->execute([$item['disco_id']]);
    $disco = $stmtPreco->fetch();

    if (!$disco || $disco['estoque'] < $item['quantidade']) {
        echo "<script>alert('Estoque insuficiente para um dos itens.'); window.location='carrinho.php';</script>";
        exit();
    }

    $total += $disco['preco'] * $item['quantidade'];
}

// Criar pedido
$sql = "INSERT INTO pedidos (usuario_id, carrinho_id, total, data_pedido) VALUES (?, ?, ?, NOW())";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id, $carrinho_id, $total]);
$pedido_id = $pdo->lastInsertId();

$sql = "INSERT INTO pagamentos (
  pedido_id,
  usuario_id,
  forma_pagamento,
  valor_pago,
  status_pagamento,
  data_pagamento
) VALUES (?, ?, ?, ?, 'aguardando', NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([$pedido_id, $usuario_id, $forma_pagamento, $total]);


// Atualizar estoque
foreach ($itens as $item) {
    $sql = "UPDATE discos SET estoque = estoque - ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$item['quantidade'], $item['disco_id']]);
}

// Finalizar carrinho
$sql = "UPDATE carrinhos SET finalizado = 1 WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$carrinho_id]);

echo "<script>alert('Compra finalizada com sucesso!'); window.location='meus_pedidos.php';</script>";
exit();
?>
