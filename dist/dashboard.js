import { buscarProdutos, buscarDashboard } from "./api.js";
import { calcularValorEstoque, filtrarEstoqueCritico, formatarProdutos, encontrarProdutoDestaque } from "./calculos.js";
const formatarMoeda = (valor) => {
    return valor.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });
};
const carregarDashboard = async () => {
    const totalProdutos = document.querySelector("#totalProdutos");
    const totalEstoque = document.querySelector("#totalEstoque");
    const valorEstoque = document.querySelector("#valorEstoque");
    const estoqueCritico = document.querySelector("#estoqueCritico");
    const produtoDestaque = document.querySelector("#produtoDestaque");
    const listaEstoqueCritico = document.querySelector("#listaEstoqueCritico");
    if (!totalProdutos ||
        !totalEstoque ||
        !valorEstoque ||
        !estoqueCritico ||
        !produtoDestaque ||
        !listaEstoqueCritico) {
        console.error("Elementos da Dashboard não foram encontrados.");
        return;
    }
    try {
        /*
         * BUSCA OS DADOS DAS APIs
         */
        const [produtos, dashboard] = await Promise.all([
            buscarProdutos(),
            buscarDashboard()
        ]);
        /*
         * DADOS VINDOS DA VIEW
         *
         * vw_dashboard
         *
         * ↓
         *
         * api/dashboard.php
         */
        totalProdutos.textContent =
            dashboard.total_produtos.toString();
        totalEstoque.textContent =
            dashboard.total_estoque.toString();
        /*
         * Caso não existam produtos
         */
        if (produtos.length === 0) {
            valorEstoque.textContent =
                "R$ 0,00";
            estoqueCritico.textContent =
                "0";
            produtoDestaque.textContent =
                "Nenhum produto";
            listaEstoqueCritico.innerHTML = `

                    <p class="sem-criticos">

                        Nenhum produto com estoque crítico.

                    </p>

                `;
            return;
        }
        /*
         * REDUCE
         *
         * Calcula o valor total
         * do estoque.
         */
        const valorTotal = calcularValorEstoque(produtos);
        /*
         * FILTER
         *
         * Encontra produtos com
         * estoque crítico.
         */
        const produtosCriticos = filtrarEstoqueCritico(produtos);
        /*
         * LISTA DE ESTOQUE CRÍTICO
         */
        if (produtosCriticos.length === 0) {
            listaEstoqueCritico.innerHTML = `

                    <p class="sem-criticos">

                        Nenhum produto com estoque crítico.

                    </p>

                `;
        }
        else {
            listaEstoqueCritico.innerHTML =
                produtosCriticos
                    .map((produto) => {
                    return `

                                    <div class="produto-critico">

                                        <div>

                                            <strong>

                                                ${produto.nome_produto}

                                            </strong>


                                            <span>

                                                ${produto.nome_categoria}

                                            </span>

                                        </div>


                                        <strong>

                                            ${produto.estoque} unidades

                                        </strong>

                                    </div>

                                `;
                })
                    .join("");
        }
        /*
         * MAP
         *
         * Formata produtos.
         */
        const produtosFormatados = formatarProdutos(produtos);
        /*
         * RANKING
         *
         * Encontra o produto
         * em destaque.
         */
        const produtoDestaqueAtual = encontrarProdutoDestaque(produtos);
        /*
         * VALOR DO ESTOQUE
         */
        valorEstoque.textContent =
            formatarMoeda(valorTotal);
        /*
         * ESTOQUE CRÍTICO
         */
        estoqueCritico.textContent =
            produtosCriticos.length.toString();
        /*
         * PRODUTO DESTAQUE
         */
        if (produtoDestaqueAtual) {
            const valorDestaque = produtoDestaqueAtual.preco *
                produtoDestaqueAtual.estoque;
            produtoDestaque.textContent =
                `${produtoDestaqueAtual.nome_produto} - ${formatarMoeda(valorDestaque)}`;
        }
        else {
            produtoDestaque.textContent =
                "Nenhum produto";
        }
        /*
         * MAP EXECUTADO
         */
        console.log("Produtos formatados:", produtosFormatados);
    }
    catch (erro) {
        console.error("Erro ao carregar a Dashboard:", erro);
        totalProdutos.textContent =
            "Erro";
        totalEstoque.textContent =
            "Erro";
        valorEstoque.textContent =
            "Erro";
        estoqueCritico.textContent =
            "Erro";
        produtoDestaque.textContent =
            "Não foi possível carregar os dados.";
    }
};
carregarDashboard();
