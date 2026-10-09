<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';
require_once 'auth.php';
require_once 'validacoes.php';

try {

    /*
    |--------------------------------------------------------------------------
    | Exige JWT
    |--------------------------------------------------------------------------
    */

    $usuarioToken = autenticarJWT();

    $idUsuario = (int) $usuarioToken['sub'];

    /*
    |--------------------------------------------------------------------------
    | Recebe os dados
    |--------------------------------------------------------------------------
    */

    $dados = json_decode(
        file_get_contents("php://input"),
        true
    );

    $nome = trim($dados['nome'] ?? '');
    $email = trim($dados['email'] ?? '');
    $cpf = preg_replace('/\D/', '', $dados['cpf'] ?? '');
    $senha = $dados['senha'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | Todos os campos são obrigatórios
    |--------------------------------------------------------------------------
    */

    if (
        $nome === '' ||
        $email === '' ||
        $cpf === '' ||
        $senha === ''
    ) {

        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Todos os campos são obrigatórios.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Validação do e-mail
    |--------------------------------------------------------------------------
    */

    if (!preg_match(
        '/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
        $email
    )) {

        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'E-mail inválido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Validação do CPF
    |--------------------------------------------------------------------------
    */

    // Valida o CPF e os seus dígitos verificadores.
if (!validarCPF($cpf)) {
    http_response_code(400);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'CPF inválido.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

    /*
    |--------------------------------------------------------------------------
    | Validação da senha
    |--------------------------------------------------------------------------
    */

    if (strlen($senha) < 8) {

        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'A senha deve possuir pelo menos 8 caracteres.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Busca o usuário autenticado
    |--------------------------------------------------------------------------
    */

    $stmt = $conexao->prepare(
        "SELECT id_usuario, email
         FROM usuarios
         WHERE id_usuario = ?"
    );

    $stmt->bind_param("i", $idUsuario);

    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {

        http_response_code(404);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Usuário não encontrado.'
        ], JSON_UNESCAPED_UNICODE);

        $stmt->close();

        exit;
    }

    $usuarioAtual = $resultado->fetch_assoc();

    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | E-mail não pode ser alterado
    |--------------------------------------------------------------------------
    */

    if ($email !== $usuarioAtual['email']) {

        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'O e-mail não pode ser alterado.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Verifica CPF em outro usuário
    |--------------------------------------------------------------------------
    */

    $stmt = $conexao->prepare(
        "SELECT id_usuario
         FROM usuarios
         WHERE cpf = ?
         AND id_usuario <> ?"
    );

    $stmt->bind_param(
        "si",
        $cpf,
        $idUsuario
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        http_response_code(409);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Este CPF já está cadastrado para outro usuário.'
        ], JSON_UNESCAPED_UNICODE);

        $stmt->close();

        exit;
    }

    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | Criptografa a nova senha
    |--------------------------------------------------------------------------
    */

    $senhaHash = password_hash(
        $senha,
        PASSWORD_DEFAULT
    );

    /*
    |--------------------------------------------------------------------------
    | Atualiza somente o próprio usuário
    |--------------------------------------------------------------------------
    */

    $stmt = $conexao->prepare(
        "UPDATE usuarios
         SET nome = ?,
             cpf = ?,
             senha = ?
         WHERE id_usuario = ?"
    );

    $stmt->bind_param(
        "sssi",
        $nome,
        $cpf,
        $senhaHash,
        $idUsuario
    );

    if (!$stmt->execute()) {
        throw new Exception(
            'Erro ao atualizar usuário: ' . $stmt->error
        );
    }

    $stmt->close();

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Usuário atualizado com sucesso.'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro interno no servidor.',
        'erro' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}