export const buscarProdutos = async () => {
    const resposta = await fetch("./api/produtos.php?limite=1000");
    if (!resposta.ok) {
        throw new Error("Erro ao buscar produtos.");
    }
    const dados = await resposta.json();
    if (!dados.sucesso) {
        throw new Error(dados.mensagem ??
            "Erro ao buscar produtos.");
    }
    return dados.produtos;
};
export const buscarDashboard = async () => {
    const resposta = await fetch("./api/dashboard.php");
    if (!resposta.ok) {
        throw new Error("Erro ao buscar dados da Dashboard.");
    }
    const dados = await resposta.json();
    if (!dados.sucesso) {
        throw new Error(dados.mensagem ??
            "Erro ao buscar dados da Dashboard.");
    }
    return dados.dashboard;
};
