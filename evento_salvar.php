<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

require_once 'config.php';


$id = $_POST['id_evento'] ?? null;

$nome = trim($_POST['nome_evento'] ?? '');

$data = $_POST['data_evento'] ?? '';

$hora_evento = $_POST['hora_evento'] ?? '';

$descricao = trim($_POST['descricao'] ?? '');

$produtos = $_POST['produtos'] ?? [];


/*
 * Validação básica.
 */

if (
    $nome === '' ||
    $data === '' ||
    $hora_evento === ''
) {

    header(
        "Location: eventos_admin.php?erro=Preencha os campos obrigatórios."
    );

    exit;
}


/*
 * Garante que produtos seja um array.
 */

if (!is_array($produtos)) {

    $produtos = [];

}


/*
 * Inicia uma transação.
 *
 * Se alguma operação der errado,
 * nada será salvo pela metade.
 */

$conexao->begin_transaction();


try {


    /*
     * CADASTRO
     */

    if (!$id) {

        $stmt = $conexao->prepare("
            INSERT INTO eventos
            (
                nome_evento,
                data_evento,
                hora_evento,
                descricao
            )
            VALUES (?, ?, ?, ?)
        ");


        $stmt->bind_param(
            "ssss",
            $nome,
            $data,
            $hora_evento,
            $descricao
        );


        $stmt->execute();


        /*
         * Pega o ID do evento criado.
         */

        $id = $conexao->insert_id;


    } else {


        /*
         * EDIÇÃO
         */

        $stmt = $conexao->prepare("
            UPDATE eventos
            SET
                nome_evento = ?,
                data_evento = ?,
                hora_evento = ?,
                descricao = ?
            WHERE id_evento = ?
        ");


        $stmt->bind_param(
            "ssssi",
            $nome,
            $data,
            $hora_evento,
            $descricao,
            $id
        );


        $stmt->execute();


        /*
         * Remove os vínculos antigos.
         */

        $stmt = $conexao->prepare("
            DELETE FROM evento_produto
            WHERE id_evento = ?
        ");


        $stmt->bind_param(
            "i",
            $id
        );


        $stmt->execute();

    }


    /*
     * Insere os novos produtos.
     */

    if (count($produtos) > 0) {

        $stmt = $conexao->prepare("
            INSERT INTO evento_produto
            (
                id_evento,
                id_produto
            )
            VALUES (?, ?)
        ");


        foreach ($produtos as $idProduto) {

            $idProduto = (int) $idProduto;


            $stmt->bind_param(
                "ii",
                $id,
                $idProduto
            );


            $stmt->execute();

        }

    }


    /*
     * Confirma todas as alterações.
     */

    $conexao->commit();


    /*
     * Mensagem de sucesso.
     */

    if ($_POST['id_evento'] ?? null) {

        header(
            "Location: eventos_admin.php?sucesso=Evento atualizado com sucesso."
        );

    } else {

        header(
            "Location: eventos_admin.php?sucesso=Evento cadastrado com sucesso."
        );

    }


} catch (Exception $e) {


    /*
     * Se alguma coisa der errado,
     * desfaz tudo.
     */

    $conexao->rollback();


    header(
        "Location: eventos_admin.php?erro=Não foi possível salvar o evento."
    );

}


exit;