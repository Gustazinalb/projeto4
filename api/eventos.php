<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';

try {

    $resultado = $conexao->query("
        SELECT
            id_evento,
            nome_evento,
            data_evento,
            hora_evento,
            descricao,
            imagem
        FROM eventos
        ORDER BY data_evento, hora_evento
    ");

    if (!$resultado) {
        throw new Exception(
            "Erro ao buscar eventos: " . $conexao->error
        );
    }

    $eventos = [];

    while ($evento = $resultado->fetch_assoc()) {

        $eventos[] = [
            'id_evento' => (int) $evento['id_evento'],
            'nome_evento' => $evento['nome_evento'],
            'data_evento' => $evento['data_evento'],
            'hora_evento' => $evento['hora_evento'],
            'descricao' => $evento['descricao'],
            'imagem' => $evento['imagem']
        ];
    }

    echo json_encode([
        'sucesso' => true,
        'eventos' => $eventos
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}