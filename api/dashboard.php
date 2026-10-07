<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';

try {

    $resultado = $conexao->query("
        SELECT
            total_produtos,
            total_estoque,
            preco_medio,
            maior_preco,
            menor_preco
        FROM vw_dashboard
    ");

    if (!$resultado) {
        throw new Exception(
            "Erro ao buscar dados da dashboard: " . $conexao->error
        );
    }

    $dados = $resultado->fetch_assoc();

    if (!$dados) {
        $dados = [
            'total_produtos' => 0,
            'total_estoque' => 0,
            'preco_medio' => 0,
            'maior_preco' => 0,
            'menor_preco' => 0
        ];
    }

    echo json_encode([
        'sucesso' => true,
        'dashboard' => [
            'total_produtos' => (int) $dados['total_produtos'],
            'total_estoque' => (int) $dados['total_estoque'],
            'preco_medio' => (float) $dados['preco_medio'],
            'maior_preco' => (float) $dados['maior_preco'],
            'menor_preco' => (float) $dados['menor_preco']
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}