<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

require_once 'config.php';

$id = $_GET['id'] ?? null;

$produto = null;

if ($id) {

    $stmt = $conexao->prepare("
        SELECT
            id_produto,
            nome_produto,
            preco,
            estoque,
            imagem,
            id_categoria
        FROM produtos
        WHERE id_produto = ?
    ");

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $resultado = $stmt->get_result();

    $produto = $resultado->fetch_assoc();

    if (!$produto) {
        header("Location: produtos_admin.php?erro=Produto não encontrado.");
        exit;
    }
}


$categorias = $conexao->query("
    SELECT
        id_categoria,
        nome_categoria
    FROM categorias
    ORDER BY nome_categoria
");


$modoEdicao = $produto !== null;

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
        <?= $modoEdicao ? 'Editar Produto' : 'Novo Produto' ?>
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
            <?= $modoEdicao ? 'Editar Produto' : 'Novo Produto' ?>
        </h1>

        <p class="text-muted">
            <?= $modoEdicao
                ? 'Altere as informações do produto.'
                : 'Cadastre um novo produto no catálogo.'
            ?>
        </p>

    </div>


    <div class="card shadow-sm">

        <div class="card-body p-4">

            <form
                action="produto_salvar.php"
                method="POST"
            >

                <?php if ($modoEdicao): ?>

                    <input
                        type="hidden"
                        name="id_produto"
                        value="<?= $produto['id_produto'] ?>"
                    >

                <?php endif; ?>


                <div class="mb-3">

                    <label class="form-label">
                        Nome do produto
                    </label>

                    <input
                        type="text"
                        name="nome_produto"
                        class="form-control"
                        required
                        maxlength="100"
                        value="<?= htmlspecialchars(
                            $produto['nome_produto'] ?? ''
                        ) ?>"
                    >

                </div>


                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Preço
                        </label>

                        <input
                            type="number"
                            name="preco"
                            class="form-control"
                            required
                            min="0"
                            step="0.01"
                            value="<?= $produto['preco'] ?? '' ?>"
                        >

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Estoque
                        </label>

                        <input
                            type="number"
                            name="estoque"
                            class="form-control"
                            required
                            min="0"
                            value="<?= $produto['estoque'] ?? '' ?>"
                        >

                    </div>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Categoria
                    </label>

                    <select
                        name="id_categoria"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Selecione uma categoria
                        </option>

                        <?php while ($categoria = $categorias->fetch_assoc()): ?>

                            <option
                                value="<?= $categoria['id_categoria'] ?>"
                                <?= (
                                    isset($produto['id_categoria']) &&
                                    $produto['id_categoria'] == $categoria['id_categoria']
                                )
                                    ? 'selected'
                                    : ''
                                ?>
                            >

                                <?= htmlspecialchars(
                                    $categoria['nome_categoria']
                                ) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="mb-4">

                    <label class="form-label">
                        Nome da imagem
                    </label>

                    <input
                        type="text"
                        name="imagem"
                        class="form-control"
                        maxlength="255"
                        placeholder="exemplo.png"
                        value="<?= htmlspecialchars(
                            $produto['imagem'] ?? ''
                        ) ?>"
                    >

                    <small class="text-muted">
                        Informe apenas o nome do arquivo que está dentro da pasta img.
                    </small>

                </div>


                <div class="d-flex gap-2">

                    <a
                        href="produtos_admin.php"
                        class="btn btn-secondary"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn"
                        style="background-color:#F29F05;color:white;"
                    >
                        <?= $modoEdicao ? 'Salvar Alterações' : 'Cadastrar Produto' ?>
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>

</html>