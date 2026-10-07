<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

require_once 'config.php';

$id = $_GET['id'] ?? null;


if (!$id || !is_numeric($id)) {

    header(
        "Location: categorias_admin.php?erro=Categoria inválida."
        
    );

    exit;
}


/*
 * Verifica se existem produtos usando esta categoria.
 */

$stmt = $conexao->prepare("
    SELECT COUNT(*) AS total
    FROM produtos
    WHERE id_categoria = ?
");

$stmt->bind_param("i", $id);

$stmt->execute();

$resultado = $stmt->get_result();

$dados = $resultado->fetch_assoc();

$totalProdutos = (int) $dados['total'];


if ($totalProdutos > 0) {

    header(
        "Location: categorias_admin.php?erro=Não é possível excluir esta categoria porque existem produtos vinculados a ela."
    );

    exit;
}


/*
 * Se não houver produtos vinculados,
 * podemos excluir a categoria.
 */

$stmt = $conexao->prepare("
    DELETE FROM categorias
    WHERE id_categoria = ?
");

$stmt->bind_param("i", $id);


if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        header(
            "Location: categorias_admin.php?sucesso=Categoria excluída com sucesso."
        );

    } else {

        header(
            "Location: categorias_admin.php?erro=Categoria não encontrada."
        );
    }

} else {

    header(
        "Location: categorias_admin.php?erro=Não foi possível excluir a categoria."
    );
}

exit;