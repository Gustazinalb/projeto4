<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';
require_once 'auth.php';

autenticarJWT();

try {

    $metodo = $_SERVER['REQUEST_METHOD'];

    /*
    |--------------------------------------------------------------------------
    | GET - LISTAR PRODUTOS COM PAGINAÇÃO
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

        if ($limite < 1) {
            $limite = 10;
        }

        if ($limite > 100) {
            $limite = 100;
        }

        $offset = ($pagina - 1) * $limite;

        /*
        |--------------------------------------------------------------------------
        | Conta o total de produtos
        |--------------------------------------------------------------------------
        */

        $resultadoTotal = $conexao->query(
            "SELECT COUNT(*) AS total FROM produtos"
        );

        $total = (int) $resultadoTotal
            ->fetch_assoc()['total'];

        /*
        |--------------------------------------------------------------------------
        | Busca os produtos + categoria
        |--------------------------------------------------------------------------
        */

        $stmt = $conexao->prepare(
            "SELECT
                p.id_produto,
                p.nome_produto,
                p.preco,
                p.estoque,
                p.imagem,
                p.id_categoria,
                c.nome_categoria
            FROM produtos p
            INNER JOIN categorias c
                ON p.id_categoria = c.id_categoria
            ORDER BY p.id_produto
            LIMIT ? OFFSET ?"
        );

        $stmt->bind_param(
            "ii",
            $limite,
            $offset
        );

        $stmt->execute();

        $resultado = $stmt->get_result();

        $produtos = [];

        while ($produto = $resultado->fetch_assoc()) {

            $produto['preco'] = (float) $produto['preco'];
            $produto['estoque'] = (int) $produto['estoque'];
            $produto['id_produto'] = (int) $produto['id_produto'];
            $produto['id_categoria'] = (int) $produto['id_categoria'];

            $produtos[] = $produto;
        }

        $totalPaginas = $total > 0
            ? ceil($total / $limite)
            : 1;

        echo json_encode([
            'sucesso' => true,
            'pagina' => $pagina,
            'limite' => $limite,
            'total' => $total,
            'total_paginas' => $totalPaginas,
            'dados' => $produtos
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | POST - CADASTRAR PRODUTO
    |--------------------------------------------------------------------------
    */

    if ($metodo === 'POST') {

        $dados = json_decode(
            file_get_contents("php://input"),
            true
        );

        $nome = trim($dados['nome_produto'] ?? '');
        $preco = $dados['preco'] ?? null;
        $estoque = $dados['estoque'] ?? null;
        $imagem = trim($dados['imagem'] ?? '');
        $idCategoria = $dados['id_categoria'] ?? null;

        if (
            $nome === '' ||
            $preco === null ||
            $estoque === null ||
            $idCategoria === null
        ) {
            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Todos os campos obrigatórios devem ser preenchidos.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        if (!is_numeric($preco) || $preco < 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Preço inválido.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        if (!is_numeric($estoque) || $estoque < 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Estoque inválido.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | Verifica se a categoria existe
        |--------------------------------------------------------------------------
        */

        $stmt = $conexao->prepare(
            "SELECT id_categoria
             FROM categorias
             WHERE id_categoria = ?"
        );

        $stmt->bind_param(
            "i",
            $idCategoria
        );

        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'A categoria informada não existe.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $stmt->close();

        /*
        |--------------------------------------------------------------------------
        | Insere o produto
        |--------------------------------------------------------------------------
        */

        $stmt = $conexao->prepare(
            "INSERT INTO produtos
            (
                nome_produto,
                preco,
                estoque,
                imagem,
                id_categoria
            )
            VALUES (?, ?, ?, ?, ?)"
        );

        $preco = (float) $preco;
        $estoque = (int) $estoque;
        $idCategoria = (int) $idCategoria;

        $stmt->bind_param(
            "sdisi",
            $nome,
            $preco,
            $estoque,
            $imagem,
            $idCategoria
        );

        /*
        |--------------------------------------------------------------------------
        | Corrige a string de tipos do bind_param
        |--------------------------------------------------------------------------
        */

        $stmt->bind_param(
            "sdisi",
            $nome,
            $preco,
            $estoque,
            $imagem,
            $idCategoria
        );

        if (!$stmt->execute()) {
            throw new Exception(
                "Erro ao cadastrar produto: " . $stmt->error
            );
        }

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Produto cadastrado com sucesso.',
            'id_produto' => $stmt->insert_id
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | PUT - EDITAR PRODUTO
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
                'mensagem' => 'ID do produto não informado.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        /*
        | Verifica se o produto existe
        */

        $stmt = $conexao->prepare(
            "SELECT id_produto
             FROM produtos
             WHERE id_produto = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {

            http_response_code(404);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Produto não encontrado.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $stmt->close();

        $dados = json_decode(
            file_get_contents("php://input"),
            true
        );

        $nome = trim($dados['nome_produto'] ?? '');
        $preco = $dados['preco'] ?? null;
        $estoque = $dados['estoque'] ?? null;
        $imagem = trim($dados['imagem'] ?? '');
        $idCategoria = $dados['id_categoria'] ?? null;

        if (
            $nome === '' ||
            $preco === null ||
            $estoque === null ||
            $idCategoria === null
        ) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Todos os campos obrigatórios devem ser preenchidos.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        if (!is_numeric($preco) || $preco < 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Preço inválido.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        if (!is_numeric($estoque) || $estoque < 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Estoque inválido.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        /*
        | Verifica categoria
        */

        $stmt = $conexao->prepare(
            "SELECT id_categoria
             FROM categorias
             WHERE id_categoria = ?"
        );

        $stmt->bind_param(
            "i",
            $idCategoria
        );

        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'A categoria informada não existe.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $stmt->close();

        /*
        | Atualiza
        */

        $stmt = $conexao->prepare(
            "UPDATE produtos
             SET
                nome_produto = ?,
                preco = ?,
                estoque = ?,
                imagem = ?,
                id_categoria = ?
             WHERE id_produto = ?"
        );

        $preco = (float) $preco;
        $estoque = (int) $estoque;
        $idCategoria = (int) $idCategoria;

        $stmt->bind_param(
            "sdisii",
            $nome,
            $preco,
            $estoque,
            $imagem,
            $idCategoria,
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception(
                "Erro ao atualizar produto: " . $stmt->error
            );
        }

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Produto atualizado com sucesso.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE - EXCLUIR PRODUTO
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
                'mensagem' => 'ID do produto não informado.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        /*
        | Verifica se existe
        */

        $stmt = $conexao->prepare(
            "SELECT id_produto
             FROM produtos
             WHERE id_produto = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {

            http_response_code(404);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Produto não encontrado.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $stmt->close();

        /*
        | Verifica relacionamento com eventos
        */

        $stmt = $conexao->prepare(
            "SELECT id_produto
             FROM evento_produto
             WHERE id_produto = ?
             LIMIT 1"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {

            http_response_code(409);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não é possível excluir o produto porque ele está relacionado a um evento.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $stmt->close();

        /*
        | Exclui
        */

        $stmt = $conexao->prepare(
            "DELETE FROM produtos
             WHERE id_produto = ?"
        );

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            throw new Exception(
                "Erro ao excluir produto: " . $stmt->error
            );
        }

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Produto excluído com sucesso.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | MÉTODO NÃO PERMITIDO
    |--------------------------------------------------------------------------
    */

    http_response_code(405);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Método não permitido.'
    ], JSON_UNESCAPED_UNICODE);


} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}