<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';

try {

    $busca = $_GET['busca'] ?? '';

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

    $offset = ($pagina - 1) * $limite;

    $stmt = $conexao->prepare(
        "CALL BuscarProdutos(?, ?, ?)"
    );

    if (!$stmt) {
        throw new Exception(
            "Erro ao preparar a consulta: " . $conexao->error
        );
    }

    $stmt->bind_param(
        "sii",
        $busca,
        $limite,
        $offset
    );

    if (!$stmt->execute()) {
        throw new Exception(
            "Erro ao executar a procedure: " . $stmt->error
        );
    }

    $resultado = $stmt->get_result();

    $produtos = [];

    while ($produto = $resultado->fetch_assoc()) {

        $produtos[] = [

            'id_produto' =>
                (int) $produto['id_produto'],

            'nome_produto' =>
                $produto['nome_produto'],

            'preco' =>
                (float) $produto['preco'],

            'estoque' =>
                (int) $produto['estoque'],

            'imagem' =>
                $produto['imagem'],

            'id_categoria' =>
                (int) $produto['id_categoria'],

            'nome_categoria' =>
                $produto['nome_categoria']

        ];
    }

    $stmt->close();

    echo json_encode([

        'sucesso' => true,

        'pagina' => $pagina,

        'limite' => $limite,

        'produtos' => $produtos

    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([

        'sucesso' => false,

        'mensagem' => $e->getMessage()

    ], JSON_UNESCAPED_UNICODE);

}