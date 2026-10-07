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
        "Location: produtos_admin.php?erro=Produto inválido."
    );

    exit;
}

$id = (int) $id;


/*
 * Verifica se o produto está relacionado
 * a algum evento.
 */

$stmt = $conexao->prepare("
    SELECT COUNT(*) AS total
    FROM evento_produto
    WHERE id_produto = ?
");

$stmt->bind_param("i", $id);

$stmt->execute();

$resultado = $stmt->get_result();

$dados = $resultado->fetch_assoc();

$totalEventos = (int) $dados['total'];


/*
 * Não permite excluir um produto
 * que esteja vinculado a eventos.
 */

if ($totalEventos > 0) {

    header(
        "Location: produtos_admin.php?erro=Não é possível excluir este produto porque ele está vinculado a um ou mais eventos."
    );

    exit;
}


/*
 * Exclui o produto.
 */

$stmt = $conexao->prepare("
    DELETE FROM produtos
    WHERE id_produto = ?
");

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        header(
            "Location: produtos_admin.php?sucesso=Produto excluído com sucesso."
        );

    } else {

        header(
            "Location: produtos_admin.php?erro=Produto não encontrado."
        );
    }

} else {

    header(
        "Location: produtos_admin.php?erro=Não foi possível excluir o produto."
    );
}

exit;