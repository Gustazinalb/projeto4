<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard - Brasa-Bar</title>


    <!-- BOOTSTRAP -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- CSS DA DASHBOARD -->

    <link
        rel="stylesheet"
        href="dashboard.css"
    >

</head>


<body>


<main class="dashboard-container">


    <!-- =====================================
         TOPO
    ====================================== -->

    <section class="dashboard-topo">


        <div class="dashboard-titulo">

            <span class="dashboard-mini-titulo">
                BRASA-BAR
            </span>

            <h1>
                Painel Administrativo
            </h1>

            <p>
                Visão geral dos produtos e do estoque
            </p>

        </div>


        <div class="dashboard-botoes">

            <a
                href="admin.php"
                class="btn-voltar"
            >
                ← Voltar
            </a>


            <a
                href="logout.php"
                class="btn-sair"
            >
                Sair
            </a>

        </div>


    </section>



    <!-- =====================================
         INDICADORES
    ====================================== -->

    <section class="dashboard-cards">


        <!-- TOTAL DE PRODUTOS -->

        <div class="dashboard-card">

            <div class="card-cabecalho">

                <div class="card-icone">
                    🥃
                </div>

                <span class="card-tag">
                    Produtos
                </span>

            </div>


            <div class="dashboard-card-titulo">
                TOTAL DE PRODUTOS
            </div>


            <p
                id="totalProdutos"
                class="dashboard-card-valor"
            >
                0
            </p>


            <span class="card-legenda">
                Produtos cadastrados
            </span>

        </div>



        <!-- ESTOQUE TOTAL -->

        <div class="dashboard-card">

            <div class="card-cabecalho">

                <div class="card-icone">
                    📦
                </div>

                <span class="card-tag">
                    Estoque
                </span>

            </div>


            <div class="dashboard-card-titulo">
                ESTOQUE TOTAL
            </div>


            <p
                id="totalEstoque"
                class="dashboard-card-valor"
            >
                0
            </p>


            <span class="card-legenda">
                Unidades disponíveis
            </span>

        </div>



        <!-- VALOR DO ESTOQUE -->

        <div class="dashboard-card">

            <div class="card-cabecalho">

                <div class="card-icone">
                    💰
                </div>

                <span class="card-tag">
                    Financeiro
                </span>

            </div>


            <div class="dashboard-card-titulo">
                VALOR DO ESTOQUE
            </div>


            <p
                id="valorEstoque"
                class="dashboard-card-valor valor"
            >
                R$ 0,00
            </p>


            <span class="card-legenda">
                Valor dos produtos em estoque
            </span>

        </div>



        <!-- ESTOQUE CRÍTICO -->

        <div class="dashboard-card card-alerta">

            <div class="card-cabecalho">

                <div class="card-icone icone-alerta">
                    ⚠️
                </div>

                <span class="card-tag tag-alerta">
                    Atenção
                </span>

            </div>


            <div class="dashboard-card-titulo">
                ESTOQUE CRÍTICO
            </div>


            <p
                id="estoqueCritico"
                class="dashboard-card-valor"
            >
                0
            </p>


            <span class="card-legenda">
                Produtos com estoque baixo
            </span>

        </div>


    </section>



    <!-- =====================================
         PARTE INFERIOR
    ====================================== -->

    <section class="dashboard-inferior">


        <!-- PRODUTO DESTAQUE -->

        <div class="dashboard-destaque">


            <div class="destaque-icone">
                🏆
            </div>


            <div class="destaque-conteudo">

                <span class="destaque-label">
                    DESTAQUE DO ESTOQUE
                </span>


                <h2>
                    Produto em destaque
                </h2>


                <p
                    id="produtoDestaque"
                    class="dashboard-destaque-produto"
                >
                    Nenhum produto
                </p>

            </div>


        </div>



        <!-- ACESSO RÁPIDO -->

        <div class="dashboard-acesso">


            <h2>
                Acesso rápido
            </h2>


            <p>
                Gerencie os dados do sistema.
            </p>


            <div class="acesso-links">


                <a href="produtos_admin.php">

                    <span>🥃</span>

                    Produtos

                    <strong>→</strong>

                </a>


                <a href="categorias_admin.php">

                    <span>📂</span>

                    Categorias

                    <strong>→</strong>

                </a>


                <a href="eventos_admin.php">

                    <span>📅</span>

                    Eventos

                    <strong>→</strong>

                </a>


            </div>


        </div>


    </section>
    <section class="dashboard-criticos">

    <div class="criticos-cabecalho">

        <div>
            <span class="card-tag tag-alerta">
                ATENÇÃO
            </span>

            <h2>
                Produtos com estoque crítico
            </h2>

            <p>
                Produtos com 5 unidades ou menos em estoque.
            </p>
        </div>

    </div>

    <div
        id="listaEstoqueCritico"
        class="lista-criticos"
    >
        <p class="sem-criticos">
            Nenhum produto com estoque crítico.
        </p>
    </div>

</section>

</main>



<!-- =====================================
     JAVASCRIPT DA DASHBOARD
====================================== -->

<script
    type="module"
    src="./dist/dashboard.js"
></script>


</body>

</html>