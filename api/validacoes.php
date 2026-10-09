```php
<?php

function validarCPF($cpf)
{
    // Remove pontos, traços e outros caracteres não numéricos.
    $cpf = preg_replace('/\D/', '', $cpf);

    // O CPF precisa ter exatamente 11 dígitos.
    if (strlen($cpf) !== 11) {
        return false;
    }

    // Rejeita CPFs com todos os dígitos iguais.
    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    // Calcula o primeiro dígito verificador.
    $soma = 0;

    for ($i = 0; $i < 9; $i++) {
        $soma += (int) $cpf[$i] * (10 - $i);
    }

    $resto = ($soma * 10) % 11;

    if ($resto === 10) {
        $resto = 0;
    }

    if ($resto !== (int) $cpf[9]) {
        return false;
    }

    // Calcula o segundo dígito verificador.
    $soma = 0;

    for ($i = 0; $i < 10; $i++) {
        $soma += (int) $cpf[$i] * (11 - $i);
    }

    $resto = ($soma * 10) % 11;

    if ($resto === 10) {
        $resto = 0;
    }

    if ($resto !== (int) $cpf[10]) {
        return false;
    }

    return true;
}