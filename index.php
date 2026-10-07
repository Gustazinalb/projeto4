<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brasa-bar </title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
    
</head>

<body>
    <?php 
        include 'header.php'; 
    ?>
</body>
<main>
        <div>
            <img src="img/home.png" alt="fundo">
            
                <a href="catalogo.php" class="botao botao-laranja">VER CATÁLOGO</a>
                <a href="cronograma.php" class="botao botao-borda">VER EVENTOS</a>
            
        </div>
</main>
<footer class="footer">
    <?php 
        include 'footer.php'; 
    ?>
</footer>

</html>