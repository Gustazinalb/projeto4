<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';
require_once 'validacoes.php';

try {

    // Recebe os dados enviados
    $dados = json_decode(file_get_contents("php://input"), true);

    $nome = trim($dados['nome'] ?? '');
    $email = trim($dados['email'] ?? '');
    $cpf = preg_replace('/\D/', '', $dados['cpf'] ?? '');
    $senha = $dados['senha'] ?? '';

    // Verifica campos obrigatórios
    if ($nome === '' || $email === '' || $cpf === '' || $senha === '') {
        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Todos os campos são obrigatórios.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Validação do e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'E-mail inválido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

  // Valida o CPF e os seus dígitos verificadores.
if (!validarCPF($cpf)) {
    http_response_code(400);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'CPF inválido.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

    // Validação do nível da senha
    if (strlen($senha) < 8) {
        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'A senha deve possuir pelo menos 8 caracteres.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Verifica se o e-mail já existe
    $stmt = $conexao->prepare(
        "SELECT id_usuario FROM usuarios WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        http_response_code(409);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Este e-mail já está cadastrado.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $stmt->close();

    // Verifica se o CPF já existe
    $stmt = $conexao->prepare(
        "SELECT id_usuario FROM usuarios WHERE cpf = ?"
    );

    $stmt->bind_param("s", $cpf);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        http_response_code(409);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Este CPF já está cadastrado.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $stmt->close();

    // Criptografa a senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere o usuário
    $stmt = $conexao->prepare(
        "INSERT INTO usuarios
        (nome, email, cpf, senha)
        VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssss",
        $nome,
        $email,
        $cpf,
        $senhaHash
    );

    if (!$stmt->execute()) {
        throw new Exception(
            "Erro ao cadastrar usuário: " . $stmt->error
        );
    }

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Usuário cadastrado com sucesso.',
        'id_usuario' => $stmt->insert_id
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}