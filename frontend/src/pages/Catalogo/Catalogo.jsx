import { useEffect, useMemo, useState } from "react";
import { useSearchParams } from "react-router-dom";
import Pagination from "../../components/Pagination.jsx";
import api from "../../services/api.js";
import { getApiErrorMessage } from "../../utils/apiError.js";
import "./Catalogo.css";

const API_LIMIT = 100;
const PRODUCTS_PER_PAGE = 12;

export default function Catalogo() {
  const [searchParams] = useSearchParams();
  const busca = searchParams.get("busca") || "";
  const [produtos, setProdutos] = useState([]);
  const [carregando, setCarregando] = useState(true);
  const [erro, setErro] = useState("");
  const [pagina, setPagina] = useState(1);

  useEffect(() => {
    let ativo = true;

    async function carregarProdutos() {
      setCarregando(true);
      setErro("");
      setPagina(1);

      try {
        const todosProdutos = [];
        let numeroPagina = 1;
        let produtosPagina;

        do {
          const { data } = await api.get("/produtos.php", {
            params: { busca, pagina: numeroPagina, limite: API_LIMIT },
          });

          if (data?.sucesso !== true || !Array.isArray(data.produtos)) {
            throw new Error("Resposta inválida ao carregar o catálogo.");
          }

          produtosPagina = data.produtos;
          todosProdutos.push(...produtosPagina);
          numeroPagina += 1;
        } while (produtosPagina.length === API_LIMIT);

        if (ativo) setProdutos(todosProdutos);
      } catch (error) {
        if (ativo) {
          setErro(getApiErrorMessage(error, "Não foi possível carregar o catálogo."));
          setProdutos([]);
        }
      } finally {
        if (ativo) setCarregando(false);
      }
    }

    carregarProdutos();
    return () => {
      ativo = false;
    };
  }, [busca]);

  const totalPaginas = Math.max(1, Math.ceil(produtos.length / PRODUCTS_PER_PAGE));
  const produtosPagina = useMemo(
    () => produtos.slice((pagina - 1) * PRODUCTS_PER_PAGE, pagina * PRODUCTS_PER_PAGE),
    [produtos, pagina],
  );
  const categorias = useMemo(() => {
    const agrupados = new Map();
    produtosPagina.forEach((produto) => {
      const nomeCategoria = produto.nome_categoria || "Sem categoria";
      if (!agrupados.has(nomeCategoria)) agrupados.set(nomeCategoria, []);
      agrupados.get(nomeCategoria).push(produto);
    });
    return [...agrupados.entries()];
  }, [produtosPagina]);

  return (
    <main className="page-main catalogo-page">
      <div className="page-heading">
        <h1>Catálogo</h1>
      </div>
      {busca && <p className="catalogo-search">Resultados para: <strong>{busca}</strong></p>}
      {carregando && <p className="catalogo-state" role="status">Carregando produtos...</p>}
      {!carregando && erro && <p className="catalogo-state catalogo-state--error" role="alert">{erro}</p>}
      {!carregando && !erro && produtos.length === 0 && (
        <p className="catalogo-state">Nenhum produto encontrado.</p>
      )}
      {!carregando && !erro && categorias.map(([categoria, itens]) => (
        <section className="catalogo-category" key={categoria}>
          <h2>{categoria}</h2>
          <div className="catalogo-grid">
            {itens.map((produto) => (
              <article className="catalogo-item" key={produto.id_produto}>
                {produto.imagem && (
                  <img
                    src={`/img/${encodeURIComponent(produto.imagem)}`}
                    alt={produto.nome_produto}
                  />
                )}
                <h3>{produto.nome_produto}</h3>
                <p>Estoque: {produto.estoque}</p>
                {Number(produto.estoque) > 0
                  ? <p className="text-success fw-bold">Disponível</p>
                  : <p className="text-danger fw-bold">Indisponível</p>}
              </article>
            ))}
          </div>
        </section>
      ))}
      {!carregando && !erro && produtos.length > PRODUCTS_PER_PAGE && (
        <Pagination
          currentPage={pagina}
          totalPages={totalPaginas}
          onPageChange={setPagina}
        />
      )}
    </main>
  );
}
