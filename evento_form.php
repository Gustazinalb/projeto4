<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

require_once 'config.php';

$id = $_GET['id'] ?? null;

$evento = null;


/*
 * Busca o evento quando estamos editando.
 */

if ($id) {

    $stmt = $conexao->prepare("
       SELECT
    id_evento,
    nome_evento,
    data_evento,
    hora_evento,
    descricao
FROM eventos
WHERE id_evento = ?
    ");

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $resultado = $stmt->get_result();

    $evento = $resultado->fetch_assoc();

    if (!$evento) {

        header(
            "Location: eventos_admin.php?erro=Evento não encontrado."
        );

        exit;
    }
}


/*
 * Busca todos os produtos.
 */

$produtos = $conexao->query("
    SELECT
        id_produto,
        nome_produto,
        preco,
        estoque
    FROM produtos
    ORDER BY nome_produto
");


/*
 * Guarda os produtos que já estão vinculados
 * ao evento.
 */

$produtosSelecionados = [];


if ($id) {

    $stmt = $conexao->prepare("
        SELECT
            id_produto
        FROM evento_produto
        WHERE id_evento = ?
    ");

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $resultadoProdutos = $stmt->get_result();

    while ($produto = $resultadoProdutos->fetch_assoc()) {

        $produtosSelecionados[] = $produto['id_produto'];

    }
}


$modoEdicao = $evento !== null;

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
        <?= $modoEdicao ? 'Editar Evento' : 'Novo Evento' ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background-color: #f5f5f5;
        }

        .container-principal {
            max-width: 900px;
            margin: 0 auto;
        }

        .card {
            border: none;
            border-radius: 14px;
        }

        .titulo {
            font-weight: 700;
        }

        .produtos-box {
            background-color: #f8f8f8;
            border-radius: 10px;
            padding: 20px;
            max-height: 350px;
            overflow-y: auto;
        }

        .produto-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 8px;
        }

        .produto-item:hover {
            background-color: #fff8eb;
        }

        .produto-nome {
            font-weight: 600;
        }

        .produto-info {
            color: #777;
            font-size: 14px;
        }

    </style>

</head>

<body>

<div class="container py-5 container-principal">

    <div class="mb-4">

        <h1 class="titulo">

            <?= $modoEdicao
                ? 'Editar Evento'
                : 'Novo Evento'
            ?>

        </h1>

        <p class="text-muted">

            <?= $modoEdicao
                ? 'Altere as informações e os produtos do evento.'
                : 'Cadastre um novo evento e escolha os produtos.'
            ?>

        </p>

    </div>


    <div class="card shadow-sm">

        <div class="card-body p-4">

            <form
                action="evento_salvar.php"
                method="POST"
            >


                <?php if ($modoEdicao): ?>

                    <input
                        type="hidden"
                        name="id_evento"
                        value="<?= $evento['id_evento'] ?>"
                    >

                <?php endif; ?>


                <!-- NOME -->

                <div class="mb-3">

                    <label class="form-label">
                        Nome do evento
                    </label>

                    <input
                        type="text"
                        name="nome_evento"
                        class="form-control"
                        required
                        maxlength="100"
                        value="<?= htmlspecialchars(
                            $evento['nome_evento'] ?? ''
                        ) ?>"
                    >

                </div>


                <!-- DATA -->

                <div class="mb-3">

                    <label class="form-label">
                        Data do evento
                    </label>

                    <input
                        type="date"
                        name="data_evento"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars(
                            $evento['data_evento'] ?? ''
                        ) ?>"
                    >

                </div>

                <div class="mb-3">

                <label for="hora_evento" class="form-label">
                    Horário do Evento
                </label>

                <input
                    type="time"
                    class="form-control"
                    id="hora_evento"
                    name="hora_evento"
                    value="<?= isset($evento['hora_evento']) ? htmlspecialchars($evento['hora_evento']) : '' ?>"
                    required
                >

                </div>


                <!-- DESCRIÇÃO -->

                <div class="mb-4">

                    <label class="form-label">
                        Descrição
                    </label>

                    <textarea
                        name="descricao"
                        class="form-control"
                        rows="4"
                        maxlength="500"
                    ><?= htmlspecialchars(
                        $evento['descricao'] ?? ''
                    ) ?></textarea>

                </div>


                <!-- PRODUTOS -->

                <div class="mb-4">

                    <h5 class="mb-3">
                        Produtos do evento
                    </h5>

                    <p class="text-muted">
                        Selecione os produtos que estarão disponíveis neste evento.
                    </p>


                    <div class="produtos-box">

                        <?php if ($produtos && $produtos->num_rows > 0): ?>

                            <?php while ($produto = $produtos->fetch_assoc()): ?>

                                <div class="produto-item">

                                    <div class="form-check">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="produtos[]"
                                            value="<?= $produto['id_produto'] ?>"
                                            id="produto_<?= $produto['id_produto'] ?>"

                                            <?= in_array(
                                                $produto['id_produto'],
                                                $produtosSelecionados
                                            )
                                                ? 'checked'
                                                : ''
                                            ?>
                                        >

                                        <label
                                            class="form-check-label"
                                            for="produto_<?= $produto['id_produto'] ?>"
                                        >

                                            <span class="produto-nome">

                                                <?= htmlspecialchars(
                                                    $produto['nome_produto']
                                                ) ?>

                                            </span>

                                            <br>

                                            <span class="produto-info">

                                                R$
                                                <?= number_format(
                                                    $produto['preco'],
                                                    2,
                                                    ',',
                                                    '.'
                                                ) ?>

                                                &nbsp; | &nbsp;

                                                Estoque:
                                                <?= (int) $produto['estoque'] ?>

                                            </span>

                                        </label>

                                    </div>

                                </div>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <p class="text-muted mb-0">
                                Nenhum produto cadastrado.
                            </p>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- BOTÕES -->

                <div class="d-flex gap-2">

                    <a
                        href="eventos_admin.php"
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
                            : 'Cadastrar Evento'
                        ?>

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>

</html>