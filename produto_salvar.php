<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

require_once 'config.php';


$id = $_POST['id_produto'] ?? null;

$nome = trim($_POST['nome_produto'] ?? '');

$preco = $_POST['preco'] ?? '';

$estoque = $_POST['estoque'] ?? '';

$imagem = trim($_POST['imagem'] ?? '');

$id_categoria = $_POST['id_categoria'] ?? '';


if (
    $nome === '' ||
    $preco === '' ||
    $estoque === '' ||
    $id_categoria === ''
) {

    header(
        "Location: produtos_admin.php?erro=Preencha todos os campos obrigatórios."
    );

    exit;
}


if ($preco < 0 || $estoque < 0) {

    header(
        "Location: produtos_admin.php?erro=Preço e estoque não podem ser negativos."
    );

    exit;
}


if ($id) {

    $stmt = $conexao->prepare("
        UPDATE produtos
        SET
            nome_produto = ?,
            preco = ?,
            estoque = ?,
            imagem = ?,
            id_categoria = ?
        WHERE id_produto = ?
    ");

    $stmt->bind_param(
        "sdisii",
        $nome,
        $preco,
        $estoque,
        $imagem,
        $id_categoria,
        $id
    );

    if ($stmt->execute()) {

        header(
            "Location: produtos_admin.php?sucesso=Produto atualizado com sucesso."
        );

    } else {

        header(
            "Location: produtos_admin.php?erro=Não foi possível atualizar o produto."
        );
    }

} else {

    $stmt = $conexao->prepare("
        INSERT INTO produtos
        (
            nome_produto,
            preco,
            estoque,
            imagem,
            id_categoria
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sdisi",
        $nome,
        $preco,
        $estoque,
        $imagem,
        $id_categoria
    );

    if ($stmt->execute()) {

        header(
            "Location: produtos_admin.php?sucesso=Produto cadastrado com sucesso."
        );

    } else {

        header(
            "Location: produtos_admin.php?erro=Não foi possível cadastrar o produto."
        );
    }
}

exit;