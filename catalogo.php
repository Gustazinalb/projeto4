<?php

require_once 'config.php';
require_once 'funcoes.php';

$busca = $_GET['busca'] ?? '';

$resultado = buscarProdutos($conexao, $busca);

$produtos = organizarPorCategoria($resultado);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Brasa-bar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.
    8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main>

    <?php if (empty($produtos)): ?>

        <div class="cabeçalio">
            <h2>Nenhum produto encontrado.</h2>
        </div>

    <?php else: ?>

        <?php foreach ($produtos as $categoria => $itens): ?>

            <div class="cabeçalio">
                <h2><?= htmlspecialchars($categoria) ?></h2>
            </div>

            <div class="container">

                <?php foreach ($itens as $produto): ?>

                    <div class="item">
                        <img src="img/<?= htmlspecialchars($produto['imagem']) ?>" alt="">

                        <h2><?= htmlspecialchars($produto['nome_produto']) ?></h2>

                        <p>Estoque: <?= $produto['estoque'] ?></p>

<?php if (estoqueDisponivel($produto['estoque'])): ?>

    <p class="text-success fw-bold">Disponível</p>

<?php else: ?>

    <p class="text-danger fw-bold">Indisponível</p>

<?php endif; ?>
                        
                    </div>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</main>
    

<footer class="footer">
    <?php 
        include 'footer.php'; 
    ?>
</footer>
</body>
</html>