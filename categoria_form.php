<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

require_once 'config.php';

$id = $_GET['id'] ?? null;

$categoria = null;

if ($id) {

    $stmt = $conexao->prepare("
        SELECT
            id_categoria,
            nome_categoria
        FROM categorias
        WHERE id_categoria = ?
    ");

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $resultado = $stmt->get_result();

    $categoria = $resultado->fetch_assoc();

    if (!$categoria) {

        header(
            "Location: categorias_admin.php?erro=Categoria não encontrada."
        );

        exit;
    }
}

$modoEdicao = $categoria !== null;

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= $modoEdicao ? 'Editar Categoria' : 'Nova Categoria' ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container py-5">

    <div class="mb-4">

        <h1>
            <?= $modoEdicao ? 'Editar Categoria' : 'Nova Categoria' ?>
        </h1>

        <p class="text-muted">
            <?= $modoEdicao
                ? 'Altere o nome da categoria.'
                : 'Cadastre uma nova categoria.'
            ?>
        </p>

    </div>


    <div class="card shadow-sm">

        <div class="card-body p-4">

            <form
                action="categoria_salvar.php"
                method="POST"
            >

                <?php if ($modoEdicao): ?>

                    <input
                        type="hidden"
                        name="id_categoria"
                        value="<?= $categoria['id_categoria'] ?>"
                    >

                <?php endif; ?>


                <div class="mb-4">

                    <label class="form-label">
                        Nome da categoria
                    </label>

                    <input
                        type="text"
                        name="nome_categoria"
                        class="form-control"
                        required
                        maxlength="50"
                        value="<?= htmlspecialchars(
                            $categoria['nome_categoria'] ?? ''
                        ) ?>"
                    >

                </div>


                <div class="d-flex gap-2">

                    <a
                        href="categorias_admin.php"
                        class="btn btn-secondary"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn"
                        style="background-color:#F29F05;color:white;"
                    >
                        <?= $modoEdicao
                            ? 'Salvar Alterações'
                            : 'Cadastrar Categoria'
                        ?>
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>

</html>