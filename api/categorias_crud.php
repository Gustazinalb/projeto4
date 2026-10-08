<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';
require_once 'auth.php';

try {

    // Toda operação exige JWT
    autenticarJWT();

    $metodo = $_SERVER['REQUEST_METHOD'];

    /*
    |--------------------------------------------------------------------------
    | GET - Listar categorias com paginação
    |--------------------------------------------------------------------------
    */

    if ($metodo === 'GET') {

        $pagina = isset($_GET['pagina'])
            ? (int) $_GET['pagina']
            : 1;

        $limite = isset($_GET['limite'])
            ? (int) $_GET['limite']
            : 10;

        if ($pagina < 1) {
            $pagina = 1;
        }

        if ($limite < 1 || $limite > 100) {
            $limite = 10;
        }

        $offset = ($pagina - 1) * $limite;

        // Total de categorias
        $resultadoTotal = $conexao->query(
            "SELECT COUNT(*) AS total FROM categorias"
        );

        $total = (int) $resultadoTotal
            ->fetch_assoc()['total'];

        // Categorias da página atual
        $stmt = $conexao->prepare(
            "SELECT id_categoria, nome_categoria
             FROM categorias
             ORDER BY id_categoria
             LIMIT ? OFFSET ?"
        );

        $stmt->bind_param(
            "ii",
            $limite,
            $offset
        );

        $stmt->execute();

        $resultado = $stmt->get_result();

        $categorias = [];

        while ($categoria = $resultado->fetch_assoc()) {
            $categorias[] = $categoria;
        }

        $stmt->close();

        echo json_encode([
            'sucesso' => true,
            'pagina' => $pagina,
            'limite' => $limite,
            'total' => $total,
            'total_paginas' => $total > 0
                ? ceil($total / $limite)
                : 0,
            'dados' => $categorias
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | POST - Criar categoria
    |--------------------------------------------------------------------------
    */

    if ($metodo === 'POST') {

        $dados = json_decode(
            file_get_contents("php://input"),
            true
        );

        $nome = trim(
            $dados['nome_categoria'] ?? ''
        );

        if ($nome === '') {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'O nome da categoria é obrigatório.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $stmt = $conexao->prepare(
            "INSERT INTO categorias
             (nome_categoria)
             VALUES (?)"
        );

        $stmt->bind_param("s", $nome);

        if (!$stmt->execute()) {
            throw new Exception(
                'Erro ao criar categoria: ' . $stmt->error
            );
        }

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Categoria criada com sucesso.',
            'id_categoria' => $stmt->insert_id
        ], JSON_UNESCAPED_UNICODE);

        $stmt->close();

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | PUT - Editar categoria
    |--------------------------------------------------------------------------
    */

    if ($metodo === 'PUT') {

        $id = isset($_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($id <= 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'ID da categoria inválido.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $dados = json_decode(
            file_get_contents("php://input"),
            true
        );

        $nome = trim(
            $dados['nome_categoria'] ?? ''
        );

        if ($nome === '') {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'O nome da categoria é obrigatório.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        // Verifica se existe
        $stmt = $conexao->prepare(
            "SELECT id_categoria
             FROM categorias
             WHERE id_categoria = ?"
        );

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {

            http_response_code(404);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Categoria não encontrada.'
            ], JSON_UNESCAPED_UNICODE);

            $stmt->close();

            exit;
        }

        $stmt->close();

        // Atualiza
        $stmt = $conexao->prepare(
            "UPDATE categorias
             SET nome_categoria = ?
             WHERE id_categoria = ?"
        );

        $stmt->bind_param(
            "si",
            $nome,
            $id
        );

        $stmt->execute();

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Categoria atualizada com sucesso.'
        ], JSON_UNESCAPED_UNICODE);

        $stmt->close();

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE - Excluir categoria
    |--------------------------------------------------------------------------
    */

    if ($metodo === 'DELETE') {

        $id = isset($_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($id <= 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'ID da categoria inválido.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        // Verifica se existe
        $stmt = $conexao->prepare(
            "SELECT id_categoria
             FROM categorias
             WHERE id_categoria = ?"
        );

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {

            http_response_code(404);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Categoria não encontrada.'
            ], JSON_UNESCAPED_UNICODE);

            $stmt->close();

            exit;
        }

        $stmt->close();

        // Verifica se existem produtos vinculados
        $stmt = $conexao->prepare(
            "SELECT id_produto
             FROM produtos
             WHERE id_categoria = ?
             LIMIT 1"
        );

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {

            http_response_code(409);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não é possível excluir uma categoria que possui produtos.'
            ], JSON_UNESCAPED_UNICODE);

            $stmt->close();

            exit;
        }

        $stmt->close();

        // Exclui
        $stmt = $conexao->prepare(
            "DELETE FROM categorias
             WHERE id_categoria = ?"
        );

        $stmt->bind_param("i", $id);

        $stmt->execute();

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Categoria excluída com sucesso.'
        ], JSON_UNESCAPED_UNICODE);

        $stmt->close();

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Método não permitido
    |--------------------------------------------------------------------------
    */

    http_response_code(405);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Método HTTP não permitido.'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro interno no servidor.',
        'erro' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}