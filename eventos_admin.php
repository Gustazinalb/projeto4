<?php

session_start();

if (!isset($_SESSION['admin'])) {

    header("Location: admin.php");
    exit;

}

require_once 'config.php';

$sql = "
    SELECT
        id_evento,
        nome_evento,
        data_evento,
        descricao
    FROM eventos
    ORDER BY data_evento DESC
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

    <title>Gerenciar Eventos - Brasa-Bar</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background-color: #f5f5f5;
        }

        .eventos-container {
            max-width: 1100px;
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
            color: white;
            font-weight: 600;
        }

        .btn-laranja:hover {
            background-color: #d98d00;
            color: white;
        }

        .tabela-container {
            background-color: white;
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
            color: white;
        }

        .btn-editar:hover {
            background-color: #d98d00;
            color: white;
        }

    </style>

</head>

<body>

<main class="eventos-container">

    <div class="topo">

        <div>

            <h1>Gerenciar Eventos</h1>

            <p>
                Cadastre, edite e remova eventos do Brasa-Bar.
            </p>

            <a
                href="admin.php"
                class="btn btn-secondary"
            >
                ← Voltar
            </a>
        </div>

        <a
            href="evento_form.php"
            class="btn btn-laranja"
        >
            + Novo Evento
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

                        <th>Evento</th>

                        <th>Data</th>

                        <th>Descrição</th>

                        <th>Ações</th>

                    </tr>

                </thead>

                <tbody>

                <?php if ($resultado && $resultado->num_rows > 0): ?>

                    <?php while ($evento = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?= htmlspecialchars(
                                        $evento['nome_evento']
                                    ) ?>
                                </strong>
                            </td>

                            <td>

                                <?=
                                    date(
                                        'd/m/Y',
                                        strtotime($evento['data_evento'])
                                    )
                                ?>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $evento['descricao'] ?? ''
                                ) ?>

                            </td>

                            <td>

                                <div class="acoes">

                                    <a
                                        href="evento_form.php?id=<?= $evento['id_evento'] ?>"
                                        class="btn btn-sm btn-editar"
                                    >
                                        Editar
                                    </a>

                                    <a
                                        href="evento_excluir.php?id=<?= $evento['id_evento'] ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Tem certeza que deseja excluir este evento?');"
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
                            colspan="4"
                            class="text-center py-4"
                        >
                            Nenhum evento cadastrado.
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