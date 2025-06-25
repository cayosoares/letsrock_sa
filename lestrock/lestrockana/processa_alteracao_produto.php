<?php
session_start();
require 'conexao.php';

// Verifica se o usuário tem permissão de ADM
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_produto = $_POST['id_produto'] ?? null;
    $nome_prod = $_POST['nome_prod'] ?? null;
    $descricao = $_POST['descricao'] ?? null;
    $qtde = $_POST['qtde'] ?? null;
    $valor_unit = $_POST['valor_unit'] ?? null;

    // Validações básicas
    if (!$id_produto || !$nome_prod || !$descricao || $qtde === null || $valor_unit === null) {
        echo "<script>alert('Por favor, preencha todos os campos!'); window.history.back();</script>";
        exit();
    }

    // Atualiza o produto no banco
    $sql = "UPDATE produto SET nome_prod = :nome_prod, descricao = :descricao, qtde = :qtde, valor_unit = :valor_unit WHERE id_produto = :id_produto";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_prod', $nome_prod);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':qtde', $qtde, PDO::PARAM_INT);
    $stmt->bindParam(':valor_unit', $valor_unit);
    $stmt->bindParam(':id_produto', $id_produto, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Produto alterado com sucesso!'); window.location.href='alterar_produto.php';</script>";
    } else {
        echo "<script>alert('Erro ao alterar produto!'); window.history.back();</script>";
    }
} else {
    // Se o acesso não foi via POST
    echo "<script>alert('Acesso inválido!'); window.location.href='alterar_produto.php';</script>";
}
