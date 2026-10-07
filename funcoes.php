<?php

function buscarProdutos($conexao, $busca = '')
{
    $sql = "
        SELECT
            p.nome_produto,
            p.preco,
            p.estoque,
            p.imagem,
            c.nome_categoria
        FROM produtos p
        INNER JOIN categorias c
            ON p.id_categoria = c.id_categoria
    ";

    if (!empty($busca)) {
        $sql .= " WHERE p.nome_produto LIKE ? OR c.nome_categoria LIKE ?";
    }

    $sql .= " ORDER BY c.id_categoria, p.nome_produto";

    $stmt = $conexao->prepare($sql);

    if (!empty($busca)) {
        $termo = "%{$busca}%";
        $stmt->bind_param("ss", $termo, $termo);
    }

    $stmt->execute();

    return $stmt->get_result();
}

function organizarPorCategoria($resultado)
{
    $produtos = [];

    while ($linha = $resultado->fetch_assoc()) {
        $produtos[$linha['nome_categoria']][] = $linha;
    }

    return $produtos;
}

function estoqueDisponivel($estoque)
{
    return $estoque > 0;
}