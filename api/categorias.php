<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';

try {

    $resultado = $conexao->query("CALL BuscarCategorias()");

    if (!$resultado) {
        throw new Exception("Erro ao buscar categorias.");
    }

    $categorias = [];

    while ($categoria = $resultado->fetch_assoc()) {

        $categorias[] = [
            'id_categoria' => (int) $categoria['id_categoria'],
            'nome_categoria' => $categoria['nome_categoria'],
            'descricao' => $categoria['descricao']
        ];
    }

    echo json_encode($categorias, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'erro' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}