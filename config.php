<?php

// Carrega o .env no ambiente local, se o arquivo existir.
$arquivoEnv = __DIR__ . '/.env';

if (is_file($arquivoEnv)) {
    $linhasEnv = file(
        $arquivoEnv,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach ($linhasEnv as $linhaEnv) {
        $linhaEnv = trim($linhaEnv);

        if ($linhaEnv === '' || str_starts_with($linhaEnv, '#')) {
            continue;
        }

        $partesEnv = explode('=', $linhaEnv, 2);

        if (count($partesEnv) !== 2) {
            continue;
        }

        $nomeEnv = trim($partesEnv[0]);
        $valorEnv = trim($partesEnv[1]);

        // Preserva variáveis fornecidas pelo ambiente, como no Docker.
        if (getenv($nomeEnv) === false) {
            putenv($nomeEnv . '=' . $valorEnv);
        }
    }
}

// Usa variáveis de ambiente ou os padrões do XAMPP.
$host = getenv('DB_HOST') ?: 'localhost';
$usuario = getenv('DB_USER') ?: 'root';
$senhaEnv = getenv('DB_PASSWORD');
$senha = ($senhaEnv === false) ? '' : $senhaEnv;
$banco = getenv('DB_NAME') ?: 'proj';

// Mantém a conexão MySQL usada pelo projeto.
$conexao = new mysqli($host, $usuario, $senha, $banco);
$conexao->set_charset('utf8mb4');
