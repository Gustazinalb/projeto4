```php
<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';
require_once 'auth.php';

try {
    // Exige um token JWT válido.
    autenticarJWT();

    $metodo = $_SERVER['REQUEST_METHOD'];

    // Recebe os dados enviados em JSON.
    $dados = json_decode(
        file_get_contents('php://input'),
        true
    );

    /*
     * GET: listar mesas com paginação.
     */
    if ($metodo === 'GET') {
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $limite = max(1, min(100, (int) ($_GET['limite'] ?? 10)));
        $offset = ($pagina - 1) * $limite;

        $resultadoTotal = $conexao->query(
            "SELECT COUNT(*) AS total FROM mesas"
        );

        $total = (int) $resultadoTotal->fetch_assoc()['total'];

        $stmt = $conexao->prepare(
            "SELECT id_mesa, numero, capacidade, status
             FROM mesas
             ORDER BY id_mesa
             LIMIT ? OFFSET ?"
        );

        $stmt->bind_param('ii', $limite, $offset);
        $stmt->execute();

        $mesas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'sucesso' => true,
            'pagina' => $pagina,
            'limite' => $limite,
            'total' => $total,
            'total_paginas' => (int) ceil($total / $limite),
            'dados' => $mesas
        ], JSON_UNESCAPED_UNICODE);

        $stmt->close();
        exit;
    }

    /*
     * POST: cadastrar mesa.
     */
    if ($metodo === 'POST') {
        $numero = filter_var(
            $dados['numero'] ?? null,
            FILTER_VALIDATE_INT
        );

        $capacidade = filter_var(
            $dados['capacidade'] ?? null,
            FILTER_VALIDATE_INT
        );

        $status = $dados['status'] ?? 'disponivel';

        if (
            $numero === false || $numero === null || $numero < 1 ||
            $capacidade === false || $capacidade === null || $capacidade < 1
        ) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Número e capacidade devem ser números inteiros positivos.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $statusPermitidos = [
            'disponivel',
            'ocupada',
            'reservada'
        ];

        if (!in_array($status, $statusPermitidos, true)) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Status inválido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Verifica se o número da mesa já está cadastrado.
        $stmt = $conexao->prepare(
            "SELECT id_mesa FROM mesas WHERE numero = ?"
        );

        $stmt->bind_param('i', $numero);
        $stmt->execute();

        $duplicada = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if ($duplicada) {
            http_response_code(409);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Já existe uma mesa com esse número.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt = $conexao->prepare(
            "INSERT INTO mesas (numero, capacidade, status)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            'iis',
            $numero,
            $capacidade,
            $status
        );

        $stmt->execute();
        $id = $conexao->insert_id;
        $stmt->close();

        http_response_code(201);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Mesa cadastrada com sucesso.',
            'id_mesa' => $id
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /*
     * PUT: editar mesa existente.
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
                'mensagem' => 'Informe um ID de mesa válido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $numero = filter_var(
            $dados['numero'] ?? null,
            FILTER_VALIDATE_INT
        );

        $capacidade = filter_var(
            $dados['capacidade'] ?? null,
            FILTER_VALIDATE_INT
        );

        $status = $dados['status'] ?? '';

        if (
            $numero === false || $numero === null || $numero < 1 ||
            $capacidade === false || $capacidade === null || $capacidade < 1
        ) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Número e capacidade devem ser números inteiros positivos.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $statusPermitidos = [
            'disponivel',
            'ocupada',
            'reservada'
        ];

        if (!in_array($status, $statusPermitidos, true)) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Status inválido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Confere se a mesa existe.
        $stmt = $conexao->prepare(
            "SELECT id_mesa FROM mesas WHERE id_mesa = ?"
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if (!$existe) {
            http_response_code(404);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Mesa não encontrada.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Impede que duas mesas tenham o mesmo número.
        $stmt = $conexao->prepare(
            "SELECT id_mesa
             FROM mesas
             WHERE numero = ? AND id_mesa <> ?"
        );

        $stmt->bind_param('ii', $numero, $id);
        $stmt->execute();

        $duplicada = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if ($duplicada) {
            http_response_code(409);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Já existe outra mesa com esse número.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt = $conexao->prepare(
            "UPDATE mesas
             SET numero = ?, capacidade = ?, status = ?
             WHERE id_mesa = ?"
        );

        $stmt->bind_param(
            'iisi',
            $numero,
            $capacidade,
            $status,
            $id
        );

        $stmt->execute();
        $stmt->close();

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Mesa atualizada com sucesso.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /*
     * DELETE: excluir mesa existente.
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
                'mensagem' => 'Informe um ID de mesa válido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt = $conexao->prepare(
            "SELECT id_mesa FROM mesas WHERE id_mesa = ?"
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if (!$existe) {
            http_response_code(404);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Mesa não encontrada.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Não permite excluir uma mesa utilizada em reservas.
        $stmt = $conexao->prepare(
            "SELECT id_reserva FROM reservas WHERE id_mesa = ? LIMIT 1"
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $temReservas = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if ($temReservas) {
            http_response_code(409);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não é possível excluir uma mesa que possui reservas.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt = $conexao->prepare(
            "DELETE FROM mesas WHERE id_mesa = ?"
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Mesa excluída com sucesso.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

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
