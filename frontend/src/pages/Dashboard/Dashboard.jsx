import { useCallback, useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import ErrorMessage from "../../components/ErrorMessage.jsx";
import Loading from "../../components/Loading.jsx";
import api from "../../services/api.js";
import { listAllResources } from "../../services/resources.js";
import { getApiErrorMessage } from "../../utils/apiError.js";
import "./Dashboard.css";

const formatarMoeda = (valor) => Number(valor || 0).toLocaleString("pt-BR", {
  style: "currency",
  currency: "BRL",
});

export default function Dashboard() {
  const [produtos, setProdutos] = useState([]);
  const [dadosDashboard, setDadosDashboard] = useState(null);
  const [carregando, setCarregando] = useState(true);
  const [erro, setErro] = useState("");
  const [tentativa, setTentativa] = useState(0);

  const carregar = useCallback(async () => {
    setCarregando(true);
    setErro("");
    try {
      const [listaProdutos, respostaDashboard] = await Promise.all([
        listAllResources("produtos"),
        api.get("/dashboard.php"),
      ]);
      if (respostaDashboard.data?.sucesso !== true || !respostaDashboard.data.dashboard) {
        throw new Error("Não foi possível carregar os indicadores do sistema.");
      }
      setProdutos(listaProdutos);
      setDadosDashboard(respostaDashboard.data.dashboard);
    } catch (error) {
      setErro(getApiErrorMessage(error, "Não foi possível carregar os dados da dashboard."));
    } finally {
      setCarregando(false);
    }
  }, []);

  useEffect(() => {
    carregar();
  }, [carregar, tentativa]);

  const calculos = useMemo(() => {
    const totalEstoqueCalculado = produtos.reduce(
      (total, produto) => total + Number(produto.estoque || 0),
      0,
    );
    const valorTotal = produtos.reduce(
      (total, produto) => total + Number(produto.preco || 0) * Number(produto.estoque || 0),
      0,
    );
    const criticos = produtos.filter((produto) => Number(produto.estoque) <= 5);
    const destaque = produtos.reduce((melhor, produto) => {
      if (!melhor) return produto;
      return Number(produto.preco) * Number(produto.estoque)
        > Number(melhor.preco) * Number(melhor.estoque)
        ? produto
        : melhor;
    }, null);
    return { totalEstoqueCalculado, valorTotal, criticos, destaque };
  }, [produtos]);

  if (carregando) return <Loading mensagem="Carregando dashboard..." />;
  if (erro) return <ErrorMessage onRetry={() => setTentativa((atual) => atual + 1)}>{erro}</ErrorMessage>;

  const indicadores = [
    { emoji: "🥃", tipo: "Produtos", titulo: "TOTAL DE PRODUTOS", valor: dadosDashboard.total_produtos, legenda: "Produtos cadastrados" },
    { emoji: "📦", tipo: "Estoque", titulo: "ESTOQUE TOTAL", valor: dadosDashboard.total_estoque ?? calculos.totalEstoqueCalculado, legenda: "Unidades disponíveis" },
    { emoji: "💰", tipo: "Financeiro", titulo: "VALOR DO ESTOQUE", valor: formatarMoeda(calculos.valorTotal), legenda: "Valor dos produtos em estoque" },
    { emoji: "⚠️", tipo: "Atenção", titulo: "ESTOQUE CRÍTICO", valor: calculos.criticos.length, legenda: "Produtos com estoque baixo", alerta: true },
  ];

  return (
    <main className="dashboard-page">
      <header className="dashboard-top">
        <div>
          <span className="dashboard-kicker">BRASA-BAR</span>
          <h1>Painel Administrativo</h1>
          <p>Visão geral dos produtos e do estoque</p>
        </div>
        <div className="dashboard-top__actions">
          <Link className="btn btn-secondary" to="/admin">← Voltar</Link>
        </div>
      </header>

      <section className="dashboard-cards" aria-label="Indicadores">
        {indicadores.map((item) => (
          <article className={`dashboard-card ${item.alerta ? "dashboard-card--alert" : ""}`} key={item.titulo}>
            <div className="dashboard-card__header">
              <span className={`dashboard-card__icon ${item.alerta ? "dashboard-card__icon--alert" : ""}`} aria-hidden="true">{item.emoji}</span>
              <span className={`dashboard-tag ${item.alerta ? "dashboard-tag--alert" : ""}`}>{item.tipo}</span>
            </div>
            <h2>{item.titulo}</h2>
            <p className="dashboard-card__value">{item.valor}</p>
            <span className="dashboard-card__caption">{item.legenda}</span>
          </article>
        ))}
      </section>

      <section className="dashboard-lower">
        <article className="dashboard-feature">
          <span aria-hidden="true">🏆</span>
          <div>
            <small>DESTAQUE DO ESTOQUE</small>
            <h2>Produto em destaque</h2>
            <p>
              {calculos.destaque
                ? `${calculos.destaque.nome_produto} - ${formatarMoeda(Number(calculos.destaque.preco) * Number(calculos.destaque.estoque))}`
                : "Nenhum produto"}
            </p>
          </div>
        </article>
        <article className="dashboard-quick">
          <h2>Acesso rápido</h2>
          <p>Gerencie os dados do sistema.</p>
          <div>
            <Link to="/produtos">🥃 Produtos <strong>→</strong></Link>
            <Link to="/categorias">📂 Categorias <strong>→</strong></Link>
            <Link to="/eventos">📅 Eventos <strong>→</strong></Link>
            <Link to="/mesas">🪑 Mesas <strong>→</strong></Link>
          </div>
        </article>
      </section>

      <section className="dashboard-criticos">
        <span className="dashboard-tag dashboard-tag--alert">ATENÇÃO</span>
        <h2>Produtos com estoque crítico</h2>
        <p>Produtos com 5 unidades ou menos em estoque.</p>
        {calculos.criticos.length === 0 ? (
          <p className="dashboard-empty">Nenhum produto com estoque crítico.</p>
        ) : (
          <ul>
            {calculos.criticos.map((produto) => (
              <li key={produto.id_produto}>
                <span><strong>{produto.nome_produto}</strong><small>{produto.nome_categoria}</small></span>
                <strong>{produto.estoque} unidades</strong>
              </li>
            ))}
          </ul>
        )}
      </section>

      <section className="dashboard-summary" aria-label="Resumo de preços">
        <span>Preço médio: <strong>{formatarMoeda(dadosDashboard.preco_medio)}</strong></span>
        <span>Maior preço: <strong>{formatarMoeda(dadosDashboard.maior_preco)}</strong></span>
        <span>Menor preço: <strong>{formatarMoeda(dadosDashboard.menor_preco)}</strong></span>
      </section>
    </main>
  );
}
