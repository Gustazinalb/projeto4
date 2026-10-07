import { Produto } from "./tipos.js";


interface RespostaProdutos {

    sucesso: boolean;
    pagina: number;
    limite: number;
    produtos: Produto[];
    mensagem?: string;

}


interface Dashboard {

    total_produtos: number;
    total_estoque: number;
    preco_medio: number;
    maior_preco: number;
    menor_preco: number;

}


interface RespostaDashboard {

    sucesso: boolean;
    dashboard: Dashboard;
    mensagem?: string;

}


export const buscarProdutos =
    async (): Promise<Produto[]> => {

        const resposta = await fetch(
            "./api/produtos.php?limite=1000"
        );


        if (!resposta.ok) {

            throw new Error(
                "Erro ao buscar produtos."
            );

        }


        const dados: RespostaProdutos =
            await resposta.json();


        if (!dados.sucesso) {

            throw new Error(
                dados.mensagem ??
                "Erro ao buscar produtos."
            );

        }


        return dados.produtos;

    };


export const buscarDashboard =
    async (): Promise<Dashboard> => {

        const resposta = await fetch(
            "./api/dashboard.php"
        );


        if (!resposta.ok) {

            throw new Error(
                "Erro ao buscar dados da Dashboard."
            );

        }


        const dados: RespostaDashboard =
            await resposta.json();


        if (!dados.sucesso) {

            throw new Error(
                dados.mensagem ??
                "Erro ao buscar dados da Dashboard."
            );

        }


        return dados.dashboard;

    };