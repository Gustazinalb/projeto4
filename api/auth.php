<?php

function base64UrlDecode($dados)
{
    $dados = strtr($dados, '-_', '+/');

    $padding = strlen($dados) % 4;

    if ($padding > 0) {
        $dados .= str_repeat('=', 4 - $padding);
    }

    return base64_decode($dados);
}

function autenticarJWT()
{
    /*
    |--------------------------------------------------------------------------
    | Obtém o cabeçalho Authorization
    |--------------------------------------------------------------------------
    */

    $cabecalho = '';

    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $cabecalho = $_SERVER['HTTP_AUTHORIZATION'];
    }

    if (
        $cabecalho === '' &&
        isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])
    ) {
        $cabecalho = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }

    if (
        $cabecalho === '' &&
        function_exists('getallheaders')
    ) {
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            $cabecalho = $headers['Authorization'];
        }

        if (isset($headers['authorization'])) {
            $cabecalho = $headers['authorization'];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Verifica se o token foi enviado
    |--------------------------------------------------------------------------
    */

    if ($cabecalho === '') {

        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Token não informado.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Verifica o formato Bearer
    |--------------------------------------------------------------------------
    */

    if (!preg_match('/Bearer\s+(.+)/i', $cabecalho, $matches)) {

        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Formato do token inválido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $token = trim($matches[1]);

    /*
    |--------------------------------------------------------------------------
    | Divide o JWT em 3 partes
    |--------------------------------------------------------------------------
    */

    $partes = explode('.', $token);

    if (count($partes) !== 3) {

        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Token inválido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    [
        $parteCabecalho,
        $partePayload,
        $parteAssinatura
    ] = $partes;

    /*
    |--------------------------------------------------------------------------
    | Chave secreta
    |--------------------------------------------------------------------------
    */

    $chaveSecreta = getenv('JWT_SECRET');

if ($chaveSecreta === false || $chaveSecreta === '') {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Configuração de autenticação indisponível.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

    /*
    |--------------------------------------------------------------------------
    | Calcula a assinatura esperada
    |--------------------------------------------------------------------------
    */

    $assinaturaEsperada = hash_hmac(
        'sha256',
        $parteCabecalho . '.' . $partePayload,
        $chaveSecreta,
        true
    );

    /*
    |--------------------------------------------------------------------------
    | Decodifica a assinatura recebida
    |--------------------------------------------------------------------------
    */

    $assinaturaRecebida = base64UrlDecode(
        $parteAssinatura
    );

    /*
    |--------------------------------------------------------------------------
    | Compara as assinaturas
    |--------------------------------------------------------------------------
    */

    if (
        $assinaturaRecebida === false ||
        !hash_equals(
            $assinaturaEsperada,
            $assinaturaRecebida
        )
    ) {

        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Token inválido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Decodifica o payload
    |--------------------------------------------------------------------------
    */

    $payload = json_decode(
        base64UrlDecode($partePayload),
        true
    );

    if (!$payload) {

        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Payload do token inválido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Verifica expiração
    |--------------------------------------------------------------------------
    */

    if (
        !isset($payload['exp']) ||
        $payload['exp'] < time()
    ) {

        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Token expirado.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Verifica identificação do usuário
    |--------------------------------------------------------------------------
    */

    if (!isset($payload['sub'])) {

        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Token sem identificação do usuário.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Retorna os dados do usuário presentes no JWT
    |--------------------------------------------------------------------------
    */

    return $payload;
}