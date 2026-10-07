<?php

session_start();

if (isset($_SESSION['admin'])) {

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Administração - Brasa-Bar</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        .admin-container {
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #1f1f1f;
            color: white;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
        }

        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo h2 {
            color: #F29F05;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .logo span {
            color: #aaa;
            font-size: 13px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .menu a {
            text-decoration: none;
            color: #ddd;
            padding: 13px 15px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .menu a:hover {
            background-color: #F29F05;
            color: white;
        }

        .sair {
            margin-top: auto;
        }

        .sair a {
            display: block;
            text-align: center;
            text-decoration: none;
            color: white;
            border: 1px solid #555;
            padding: 10px;
            border-radius: 8px;
        }

        .sair a:hover {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .conteudo {
            flex: 1;
            padding: 40px;
        }

        .topo {
            margin-bottom: 35px;
        }

        .topo h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .topo p {
            color: #777;
            margin: 0;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            max-width: 900px;
        }

        .card-admin {
            background-color: white;
            border-radius: 14px;
            padding: 25px;
            text-decoration: none;
            color: #222;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            transition: 0.2s;
            border: 1px solid #eee;
        }

        .card-admin:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.10);
        }

        .icone {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff1d6;
            border-radius: 10px;
            font-size: 25px;
            margin-bottom: 18px;
        }

        .card-admin h3 {
            font-size: 20px;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .card-admin p {
            color: #777;
            margin: 0;
            font-size: 14px;
        }

        @media (max-width: 768px) {

            .admin-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 20px;
            }

            .logo {
                margin-bottom: 20px;
            }

            .menu {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .menu a {
                flex: 1;
                min-width: 130px;
                text-align: center;
            }

            .sair {
                margin-top: 20px;
            }

            .conteudo {
                padding: 25px 20px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<div class="admin-container">

    <aside class="sidebar">

        <div class="logo">

            <h2>BRASA-BAR</h2>

            <span>Painel Administrativo</span>

        </div>

        <nav class="menu">

            <a href="dashboard.php">
                📊 Dashboard
            </a>

            <a href="produtos_admin.php">
                🥃 Produtos
            </a>

            <a href="categorias_admin.php">
                📂 Categorias
            </a>

            <a href="eventos_admin.php">
                📅 Eventos
            </a>

        </nav>

        <div class="sair">

            <a href="logout.php">
                🚪 Sair
            </a>

        </div>

    </aside>


    <main class="conteudo">

        <div class="topo">

            <h1>
                Painel Administrativo
            </h1>

            <p>
                Gerencie os produtos, categorias e eventos do Brasa-Bar.
            </p>

        </div>


        <div class="cards">

            <a
                href="dashboard.php"
                class="card-admin"
            >

                <div class="icone">
                    📊
                </div>

                <h3>
                    Dashboard
                </h3>

                <p>
                    Visualize os principais dados do sistema.
                </p>

            </a>


            <a
                href="produtos_admin.php"
                class="card-admin"
            >

                <div class="icone">
                    🥃
                </div>

                <h3>
                    Produtos
                </h3>

                <p>
                    Cadastre, edite e exclua produtos.
                </p>

            </a>


            <a
                href="categorias_admin.php"
                class="card-admin"
            >

                <div class="icone">
                    📂
                </div>

                <h3>
                    Categorias
                </h3>

                <p>
                    Gerencie as categorias dos produtos.
                </p>

            </a>


            <a
                href="eventos_admin.php"
                class="card-admin"
            >

                <div class="icone">
                    📅
                </div>

                <h3>
                    Eventos
                </h3>

                <p>
                    Cadastre eventos e associe produtos.
                </p>

            </a>

        </div>

    </main>

</div>

</body>

</html>

<?php

} else {

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>BRASA-BAR - Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="admin.css"
    >

</head>

<body>

<?php include 'header.php'; ?>


<section class="admin-container">

    <div class="login-card">

        <img
            src="img/logo.png"
            class="logo-admin"
        >

        <h1>
            <span>BRASA</span>-BAR
        </h1>

        <h3>
            Painel Administrativo
        </h3>


        <?php if (isset($_GET['erro'])): ?>

            <div class="alert alert-danger">
                Usuário ou senha incorretos.
            </div>

        <?php endif; ?>


        <form
            id="loginForm"
            action="login.php"
            method="POST"
        >

            <div class="campo">

                <label>
                    Usuário
                </label>

                <div class="input-box">

                    <input
                        type="text"
                        name="usuario"
                        placeholder="Digite seu usuário"
                        required
                    >

                </div>

            </div>


            <div class="campo">

                <label>
                    Senha
                </label>

                <div class="input-box">

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                        required
                    >

                    <span id="mostrarSenha">
                        👁
                    </span>

                </div>

            </div>


            <button
                type="submit"
                class="botao-login"
            >
                Entrar
            </button>

        </form>


        <p class="restrito">
            🔒 Acesso restrito. Apenas administradores.
        </p>

    </div>

</section>


<script src="admin.js"></script>

</body>

</html>

<?php

}

?>