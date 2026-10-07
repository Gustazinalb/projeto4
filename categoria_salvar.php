<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

require_once 'config.php';

$id = $_POST['id_categoria'] ?? null;

$nome = trim($_POST['nome_categoria'] ?? '');


if ($nome === '') {

    header(
        "Location: categorias_admin.php?erro=Informe o nome da categoria."
    );

    exit;
}


if ($id) {

    $stmt = $conexao->prepare("
        UPDATE categorias
        SET nome_categoria = ?
        WHERE id_categoria = ?
    ");

    $stmt->bind_param(
        "si",
        $nome,
        $id
    );

    if ($stmt->execute()) {

        header(
            "Location: categorias_admin.php?sucesso=Categoria atualizada com sucesso."
        );

    } else {

        header(
            "Location: categorias_admin.php?erro=Não foi possível atualizar a categoria."
        );
    }

} else {

    $stmt = $conexao->prepare("
        INSERT INTO categorias
        (
            nome_categoria
        )
        VALUES (?)
    ");

    $stmt->bind_param(
        "s",
        $nome
    );

    if ($stmt->execute()) {

        header(
            "Location: categorias_admin.php?sucesso=Categoria cadastrada com sucesso."
        );

    } else {

        header(
            "Location: categorias_admin.php?erro=Não foi possível cadastrar a categoria."
        );
    }
}

exit;