import { Produto } from "./tipos.js";

export const calcularEstoqueTotal = (produtos: Produto[]): number => {

    return produtos.reduce((total, produto) => {
        return total + produto.estoque;
    }, 0);
};


export const calcularValorEstoque = (produtos: Produto[]): number => {

    return produtos.reduce((total, produto) => {
        return total + (produto.preco * produto.estoque);
    }, 0);
};


export const filtrarEstoqueCritico = (produtos: Produto[]): Produto[] => {

    return produtos.filter((produto) => {
        return produto.estoque <= 5;
    });
};


export const formatarProdutos = (produtos: Produto[]): string[] => {

    return produtos.map((produto) => {
        return `${produto.nome_produto} - R$ ${produto.preco.toFixed(2)}`;
    });
};


export const encontrarProdutoDestaque = (
    produtos: Produto[]
): Produto | null => {

    if (produtos.length === 0) {
        return null;
    }

    return produtos.reduce((maior, produto) => {

        const valorProduto =
            produto.preco * produto.estoque;

        const valorMaior =
            maior.preco * maior.estoque;

        if (valorProduto > valorMaior) {
            return produto;
        }

        return maior;

    });
};