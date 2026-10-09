```php
<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';
require_once 'auth.php';

try {
    // Todas as operações exigem um token JWT válido.
    autenticarJWT();

    $metodo = $_SERVER['REQUEST_METHOD'];

    // Recebe os dados enviados em JSON.
    $dados = json_decode(
        file_get_contents('php://input'),
        true
    );

    /*
     * GET: listar eventos com paginação.
     */
    if ($metodo === 'GET') {
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $limite = max(1, min(100, (int) ($_GET['limite'] ?? 10)));
        $offset = ($pagina - 1) * $limite;

        // Conta quantos eventos existem.
        $resultadoTotal = $conexao->query(
            "SELECT COUNT(*) AS total FROM eventos"
        );

        $total = (int) $resultadoTotal->fetch_assoc()['total'];

        // Busca somente os eventos da página solicitada.
        $stmt = $conexao->prepare(
            "SELECT id_evento, nome_evento, data_evento,
                    hora_evento, descricao, imagem
             FROM eventos
             ORDER BY id_evento
             LIMIT ? OFFSET ?"
        );

        $stmt->bind_param('ii', $limite, $offset);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $eventos = $resultado->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'sucesso' => true,
            'pagina' => $pagina,
            'limite' => $limite,
            'total' => $total,
            'total_paginas' => (int) ceil($total / $limite),
            'dados' => $eventos
        ], JSON_UNESCAPED_UNICODE);

        $stmt->close();
        exit;
    }

    /*
     * POST: cadastrar evento.
     */
    if ($metodo === 'POST') {
        $nome = trim($dados['nome_evento'] ?? '');
        $data = trim($dados['data_evento'] ?? '');
        $hora = trim($dados['hora_evento'] ?? '');
        $descricao = trim($dados['descricao'] ?? '');
        $imagem = trim($dados['imagem'] ?? '');

        if ($nome === '' || $data === '' || $hora === '' || $descricao === '') {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Nome, data, hora e descrição são obrigatórios.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $dataValida = DateTime::createFromFormat('!Y-m-d', $data);

        if (!$dataValida || $dataValida->format('Y-m-d') !== $data) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Data inválida. Utilize AAAA-MM-DD.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $hora)) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Hora inválida. Utilize HH:MM ou HH:MM:SS.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt = $conexao->prepare(
            "INSERT INTO eventos
                (nome_evento, data_evento, hora_evento, descricao, imagem)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'sssss',
            $nome,
            $data,
            $hora,
            $descricao,
            $imagem
        );

        $stmt->execute();
        $id = $conexao->insert_id;
        $stmt->close();

        http_response_code(201);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Evento cadastrado com sucesso.',
            'id_evento' => $id
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /*
     * PUT: editar evento existente.
     */
    if ($metodo === 'PUT') {
        $id = filter_var(
            $_GET['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Informe um ID de evento válido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $nome = trim($dados['nome_evento'] ?? '');
        $data = trim($dados['data_evento'] ?? '');
        $hora = trim($dados['hora_evento'] ?? '');
        $descricao = trim($dados['descricao'] ?? '');
        $imagem = trim($dados['imagem'] ?? '');

        if ($nome === '' || $data === '' || $hora === '' || $descricao === '') {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Todos os campos obrigatórios devem ser preenchidos.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $dataValida = DateTime::createFromFormat('!Y-m-d', $data);

        if (!$dataValida || $dataValida->format('Y-m-d') !== $data) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Data inválida. Utilize AAAA-MM-DD.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $hora)) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Hora inválida. Utilize HH:MM ou HH:MM:SS.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Confere se o evento existe antes de editar.
        $stmt = $conexao->prepare(
            "SELECT id_evento FROM eventos WHERE id_evento = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if (!$existe) {
            http_response_code(404);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Evento não encontrado.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt = $conexao->prepare(
            "UPDATE eventos
             SET nome_evento = ?, data_evento = ?,
                 hora_evento = ?, descricao = ?, imagem = ?
             WHERE id_evento = ?"
        );

        $stmt->bind_param(
            'sssssi',
            $nome,
            $data,
            $hora,
            $descricao,
            $imagem,
            $id
        );

        $stmt->execute();
        $stmt->close();

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Evento atualizado com sucesso.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /*
     * DELETE: excluir evento existente.
     */
    if ($metodo === 'DELETE') {
        $id = filter_var(
            $_GET['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Informe um ID de evento válido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt = $conexao->prepare(
            "SELECT id_evento FROM eventos WHERE id_evento = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if (!$existe) {
            http_response_code(404);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Evento não encontrado.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Não permite excluir eventos associados a produtos.
        $stmt = $conexao->prepare(
            "SELECT id_evento FROM evento_produto WHERE id_evento = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $temProdutos = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if ($temProdutos) {
            http_response_code(409);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não é possível excluir um evento associado a produtos.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt = $conexao->prepare(
            "DELETE FROM eventos WHERE id_evento = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Evento excluído com sucesso.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Qualquer outro método HTTP não é aceito.
    header('Allow: GET, POST, PUT, DELETE');
    http_response_code(405);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Método não permitido.'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro interno ao processar a solicitação.'
    ], JSON_UNESCAPED_UNICODE);
}