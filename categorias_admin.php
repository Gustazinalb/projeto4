<?php

session_start();

if (!isset($_SESSION['admin'])) {

    header("Location: admin.php");
    exit;

}

require_once 'config.php';

$sql = "
    SELECT
        id_categoria,
        nome_categoria
    FROM categorias
    ORDER BY nome_categoria
";

$resultado = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciar Categorias - Brasa-Bar</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background-color: #f5f5f5;
        }

        .categorias-container {
            max-width: 900px;
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

        .acoes {
            display: flex;
            gap: 8px;
        }

        .btn-editar {
            background-color: #F29F05;
            border: none;
            color: #fff;
        }

        .btn-editar:hover {
            background-color: #d98d00;
            color: #fff;
        }

    </style>

</head>

<body>

<main class="categorias-container">

    <div class="topo">

        <div>

            <h1>Gerenciar Categorias</h1>

            <p>
                Cadastre, edite e remova categorias.
            </p>
        <a
            href="admin.php"
            class="btn btn-secondary"
        >
            ← Voltar
        </a>
        </div>

        <a
            href="categoria_form.php"
            class="btn btn-laranja"
        >
            + Nova Categoria
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

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Categoria</th>

                        <th>Ações</th>

                    </tr>

                </thead>

                <tbody>

                <?php if ($resultado && $resultado->num_rows > 0): ?>

                    <?php while ($categoria = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= (int) $categoria['id_categoria'] ?>
                            </td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars(
                                        $categoria['nome_categoria']
                                    ) ?>
                                </strong>
                            </td>

                            <td>

                                <div class="acoes">

                                    <a
                                        href="categoria_form.php?id=<?= $categoria['id_categoria'] ?>"
                                        class="btn btn-sm btn-editar"
                                    >
                                        Editar
                                    </a>

                                    <a
                                        href="categoria_excluir.php?id=<?= $categoria['id_categoria'] ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Tem certeza que deseja excluir esta categoria?');"
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
                            colspan="3"
                            class="text-center py-4"
                        >
                            Nenhuma categoria cadastrada.
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