<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cronograma - Brasa Bar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="destaques.css">

    <!-- Icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

    <?php include 'header.php'; ?>


    <main>

        <section class="cronograma">

            <div class="cabeçalio">
                <h2>Cronograma de Eventos</h2>
            </div>


            <div class="grid-eventos" id="grid-eventos">

                <!-- Os eventos serão carregados aqui pelo JavaScript -->

            </div>

        </section>


        <!-- DESTAQUES -->

        <section class="destaques">

            <div class="destaque-card">
                <i class="fa-solid fa-calendar-days"></i>
                <h3>Atualizado</h3>
                <p>Eventos toda semana</p>
            </div>

            <div class="destaque-card">
                <i class="fa-solid fa-beer-mug-empty"></i>
                <h3>Happy Hour</h3>
                <p>Promoções especiais</p>
            </div>

            <div class="destaque-card">
                <i class="fa-solid fa-ticket"></i>
                <h3>Eventos</h3>
                <p>Entrada gratuita</p>
            </div>

            <div class="destaque-card">
                <i class="fa-solid fa-bell"></i>
                <h3>Novidades</h3>
                <p>Toda semana</p>
            </div>

        </section>


        <!-- EVENTO DA SEMANA -->

        <section class="evento-semana">

            <div class="evento-info">

                <h2>🎸 Evento da Semana</h2>

                <h3>CONFIRA NOSSO CRONOGRAMA</h3>

                <p>
                    Confira todos os eventos da semana
                    e venha curtir o Brasa Bar.
                </p>

                <a href="catalogo.php" class="botao-evento">
                    VER CATÁLOGO
                </a>

            </div>

        </section>


        <!-- INSTAGRAM -->

        <section class="instagram">

            <h2>📸 SIGA NOSSO INSTAGRAM</h2>

            <div class="insta-grid">

                <img src="img/jack-apple.png" alt="">
                <img src="img/skol.png" alt="">
                <img src="img/perola.png" alt="">
                <img src="img/intencion-pink.png" alt="">

            </div>

            <p>@brasa.bar</p>

        </section>

    </main>


    <footer class="footer">

        <?php include 'footer.php'; ?>

    </footer>


    <script>

        async function carregarEventos() {

            const gridEventos = document.getElementById('grid-eventos');

            try {

                const resposta = await fetch('api/eventos.php');

                if (!resposta.ok) {
                    throw new Error('Erro ao buscar eventos.');
                }

                const dados = await resposta.json();

                if (!dados.sucesso) {
                    throw new Error(dados.mensagem);
                }

                const eventos = dados.eventos;


                if (eventos.length === 0) {

                    gridEventos.innerHTML = `
                        <div class="card">

                            <div class="topo-card">
                                <h2>Eventos</h2>
                            </div>

                            <div class="conteudo-card">

                                <i class="fa-solid fa-calendar-xmark icone"></i>

                                <h3>Nenhum evento cadastrado</h3>

                            </div>

                        </div>
                    `;

                    return;
                }


                gridEventos.innerHTML = '';


                eventos.forEach(evento => {

                    const data = new Date(
                        evento.data_evento + 'T00:00:00'
                    );


                    const diasSemana = [

                        'Domingo',
                        'Segunda',
                        'Terça',
                        'Quarta',
                        'Quinta',
                        'Sexta',
                        'Sábado'

                    ];


                    const diaSemana =
                        diasSemana[data.getDay()];


                    gridEventos.innerHTML += `

                        <div class="card">

                            <div class="topo-card">

                                <h2>${diaSemana}</h2>

                            </div>


                            <div class="conteudo-card">

                                <i class="fa-solid fa-calendar-check icone"></i>

                                <h4>${evento.hora_evento}</h4>

                                <h3>${evento.nome_evento}</h3>

                                <p>${evento.descricao ?? ''}</p>

                            </div>

                        </div>

                    `;

                });

            }

            catch (erro) {

                console.error(erro);


                gridEventos.innerHTML = `

                    <div class="card">

                        <div class="topo-card">

                            <h2>Erro</h2>

                        </div>


                        <div class="conteudo-card">

                            <i class="fa-solid fa-triangle-exclamation icone"></i>

                            <h3>Não foi possível carregar os eventos</h3>

                        </div>

                    </div>

                `;

            }

        }


        carregarEventos();

    </script>


</body>

</html>