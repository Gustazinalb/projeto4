<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brasa-bar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background:#F29F05;">

    <div class="container-fluid">

        <a class="navbar-brand logo" href="index.php">
            <img src="img/logo.png" alt="Logo">
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menu">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="d-flex justify-content-center " id="menu">
            <form class="d-flex" method="GET" action="catalogo.php">
    <input
        class="form-control me-2 botaoPesquisa"
        type="search"
        name="busca"
        placeholder="🔍Pesquisar produto"
        style="width: 400px;">
        
    <button class="btn btn-dark">
        Buscar
    </button>
</form>

            <ul class="navbar-nav ms-auto">

                <li class="nav-item fs-4">
                    <a class="nav-link text-dark fw-bold" href="index.php">Home</a>
                </li>

                <li class="nav-item fs-4">
                    <a class="nav-link text-dark fw-bold" href="catalogo.php">Catálogo</a>
                </li>

                <li class="nav-item fs-4">
                    <a class="nav-link text-dark fw-bold" href="cronograma.php">Cronograma</a>
                </li>

                <li class="nav-item fs-4">
                    <a class="nav-link text-dark fw-bold" href="contato.php">Contato</a>
                </li>

                <li class="nav-item fs-4">
                    <a class="nav-link text-dark fw-bold" href="admin.php">Admin</a>
                </li>
                
            </ul>

        </div>

    </div>

</nav>
</body>
</html>