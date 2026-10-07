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
        "Location: eventos_admin.php?erro=Evento inválido."
    );

    exit;
}


/*
 * Remove os produtos vinculados ao evento.
 */

$stmt = $conexao->prepare("
    DELETE FROM evento_produto
    WHERE id_evento = ?
");

$stmt->bind_param("i", $id);

$stmt->execute();


/*
 * Agora remove o evento.
 */

$stmt = $conexao->prepare("
    DELETE FROM eventos
    WHERE id_evento = ?
");

$stmt->bind_param("i", $id);


if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        header(
            "Location: eventos_admin.php?sucesso=Evento excluído com sucesso."
        );

    } else {

        header(
            "Location: eventos_admin.php?erro=Evento não encontrado."
        );
    }

} else {

    header(
        "Location: eventos_admin.php?erro=Não foi possível excluir o evento."
    );
}

exit;