<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';

/*
|--------------------------------------------------------------------------
| Funções para JWT
|--------------------------------------------------------------------------
*/

function base64UrlEncode($dados)
{
    return rtrim(strtr(base64_encode($dados), '+/', '-_'), '=');
}

function gerarJWT($idUsuario, $email)
{
    // Temporário para desenvolvimento local.
    // Depois vamos colocar essa chave nas variáveis de ambiente do Docker.
    $chaveSecreta = getenv('JWT_SECRET');

if ($chaveSecreta === false || $chaveSecreta === '') {
    throw new Exception('Chave JWT não configurada.');
}

    $cabecalho = [
        'alg' => 'HS256',
        'typ' => 'JWT'
    ];

    $agora = time();

    $payload = [
        'sub' => $idUsuario,
        'email' => $email,
        'iat' => $agora,
        'exp' => $agora + (60 * 60)
    ];

    $parteCabecalho = base64UrlEncode(
        json_encode($cabecalho)
    );

    $partePayload = base64UrlEncode(
        json_encode($payload)
    );

    $assinatura = hash_hmac(
        'sha256',
        $parteCabecalho . '.' . $partePayload,
        $chaveSecreta,
        true
    );

    $parteAssinatura = base64UrlEncode($assinatura);

    return $parteCabecalho . '.' .
           $partePayload . '.' .
           $parteAssinatura;
}

/*
|--------------------------------------------------------------------------
| Recebe os dados
|--------------------------------------------------------------------------
*/

try {

    $dados = json_decode(
        file_get_contents("php://input"),
        true
    );

    $email = trim($dados['email'] ?? '');
    $senha = $dados['senha'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | Validação dos campos
    |--------------------------------------------------------------------------
    */

    if ($email === '' || $senha === '') {

        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'E-mail e senha são obrigatórios.'
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
    | Busca o usuário pelo e-mail
    |--------------------------------------------------------------------------
    */

    $stmt = $conexao->prepare(
        "SELECT id_usuario, nome, email, senha
         FROM usuarios
         WHERE email = ?"
    );

    if (!$stmt) {
        throw new Exception(
            'Erro ao preparar consulta: ' . $conexao->error
        );
    }

    $stmt->bind_param("s", $email);

    $stmt->execute();

    $resultado = $stmt->get_result();

    /*
    |--------------------------------------------------------------------------
    | Verifica se o usuário existe
    |--------------------------------------------------------------------------
    */

    if ($resultado->num_rows === 0) {

        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'E-mail ou senha inválidos.'
        ], JSON_UNESCAPED_UNICODE);

        $stmt->close();

        exit;
    }

    $usuario = $resultado->fetch_assoc();

    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | Verifica a senha
    |--------------------------------------------------------------------------
    */

    if (!password_verify($senha, $usuario['senha'])) {

        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'E-mail ou senha inválidos.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Gera o JWT
    |--------------------------------------------------------------------------
    */

    $token = gerarJWT(
        $usuario['id_usuario'],
        $usuario['email']
    );

    /*
    |--------------------------------------------------------------------------
    | Retorna o resultado
    |--------------------------------------------------------------------------
    */

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Login realizado com sucesso.',
        'token' => $token,
        'usuario' => [
            'id_usuario' => $usuario['id_usuario'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email']
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro interno no servidor.',
        'erro' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}