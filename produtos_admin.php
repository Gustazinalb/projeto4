<?php

session_start();

if (!isset($_SESSION['admin'])) {

    header("Location: admin.php");
    exit;

}

require_once 'config.php';

$sql = "
    SELECT
        p.id_produto,
        p.nome_produto,
        p.preco,
        p.estoque,
        p.imagem,
        c.nome_categoria
    FROM produtos p
    INNER JOIN categorias c
        ON p.id_categoria = c.id_categoria
    ORDER BY p.id_produto
";

$resultado = $conexao->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Gerenciar Produtos - Brasa-Bar</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background-color: #f5f5f5;
        }

        .produtos-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .topo {
            
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .topo h1 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .topo p {
            color: #777;
            margin: 0;
        }

        .btn-laranja {
            background-color: #F29F05;
            border: none;
            color: #fff;
            font-weight: 600;
        }

        .btn-laranja:hover {
            background-color: #d98d00;
            color: #fff;
        }

        .tabela-container {
            background-color: #fff;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        }

        .table {
            vertical-align: middle;
        }

        .produto-imagem {
            width: 55px;
            height: 55px;
            object-fit: contain;
        }

        .acoes {
            display: flex;
            gap: 8px;
        }

        .btn-editar {
            background-color: #F29F05;
            color: #fff;
            border: none;
        }

        .btn-editar:hover {
            background-color: #d98d00;
            color: #fff;
        }

    </style>

</head>

<body>

<main class="produtos-container">

    <div class="topo">

        <div>

            <h1>Gerenciar Produtos</h1>

            <p>
                Cadastre, edite e remova produtos do catálogo.
            </p>

            <a
                href="admin.php"
                class="btn btn-secondary"
>
                ← Voltar
            </a>
        </div>

        <a
            href="produto_form.php"
            class="btn btn-laranja"
        >
            + Novo Produto
        </a>

    </div>


    <?php if (isset($_GET['sucesso'])): ?>

        <div class="alert alert-success">
            <?= htmlspecialchars($_GET['sucesso']) ?>
        </div>

    <?php endif; ?>


    <?php if (isset($_GET['erro'])): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($_GET['erro']) ?>
        </div>

    <?php endif; ?>


    <div class="tabela-container">

        <div class="table-responsive">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>Produto</th>

                        <th>Categoria</th>

                        <th>Preço</th>

                        <th>Estoque</th>

                        <th>Ações</th>

                    </tr>

                </thead>

                <tbody>

                <?php if ($resultado && $resultado->num_rows > 0): ?>

                    <?php while ($produto = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>

                                <div class="d-flex align-items-center gap-3">

                                    <?php if (!empty($produto['imagem'])): ?>

                                        <img
                                            src="img/<?= htmlspecialchars($produto['imagem']) ?>"
                                            alt="<?= htmlspecialchars($produto['nome_produto']) ?>"
                                            class="produto-imagem"
                                        >

                                    <?php endif; ?>

                                    <strong>
                                        <?= htmlspecialchars($produto['nome_produto']) ?>
                                    </strong>

                                </div>

                            </td>

                            <td>
                                <?= htmlspecialchars($produto['nome_categoria']) ?>
                            </td>

                            <td>
                                R$ <?= number_format(
                                    $produto['preco'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                <?= (int) $produto['estoque'] ?>
                            </td>

                            <td>

                                <div class="acoes">

                                    <a
                                        href="produto_form.php?id=<?= $produto['id_produto'] ?>"
                                        class="btn btn-sm btn-editar"
                                    >
                                        Editar
                                    </a>

                                    <a
                                        href="produto_excluir.php?id=<?= $produto['id_produto'] ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Tem certeza que deseja excluir este produto?');"
                                    >
                                        Excluir
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="5"
                            class="text-center py-4"
                        >
                            Nenhum produto cadastrado.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

</body>

</html>