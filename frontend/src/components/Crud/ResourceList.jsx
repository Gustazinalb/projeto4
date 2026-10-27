import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { toast } from "react-toastify";
import ConfirmModal from "../ConfirmModal.jsx";
import ErrorMessage from "../ErrorMessage.jsx";
import Loading from "../Loading.jsx";
import Pagination from "../Pagination.jsx";
import Table from "../Table.jsx";
import { deleteResource, listResources, resourceConfig } from "../../services/resources.js";
import { getApiErrorMessage } from "../../utils/apiError.js";
import "./Crud.css";

const PAGE_SIZE = 10;

export default function ResourceList({ resource }) {
  const config = resourceConfig[resource];
  const [pagina, setPagina] = useState(1);
  const [registros, setRegistros] = useState([]);
  const [totalPaginas, setTotalPaginas] = useState(1);
  const [carregando, setCarregando] = useState(true);
  const [erro, setErro] = useState("");
  const [atualizacao, setAtualizacao] = useState(0);
  const [registroSelecionado, setRegistroSelecionado] = useState(null);
  const [excluindo, setExcluindo] = useState(false);

  const carregar = useCallback(async () => {
    setCarregando(true);
    setErro("");
    try {
      const resultado = await listResources(resource, pagina, PAGE_SIZE);
      setRegistros(resultado.dados);
      setTotalPaginas(resultado.totalPaginas);
    } catch (error) {
      setErro(getApiErrorMessage(error, `Não foi possível carregar ${config.titulo.toLowerCase()}.`));
    } finally {
      setCarregando(false);
    }
  }, [config.titulo, pagina, resource]);

  useEffect(() => {
    carregar();
  }, [carregar, atualizacao]);

  async function confirmarExclusao() {
    if (!registroSelecionado) return;
    setExcluindo(true);
    try {
      const { data } = await deleteResource(resource, registroSelecionado[config.idField]);
      toast.success(data?.mensagem || `${config.singular} excluído(a) com sucesso.`);
      setRegistroSelecionado(null);
      if (registros.length === 1 && pagina > 1) setPagina((atual) => atual - 1);
      else setAtualizacao((atual) => atual + 1);
    } catch (error) {
      toast.error(getApiErrorMessage(error, `Não foi possível excluir ${config.singular.toLowerCase()}.`));
    } finally {
      setExcluindo(false);
    }
  }

  const columns = [
    ...config.colunas.map((column) => ({
      ...column,
      render: column.render || (column.type === "produto"
        ? (registro) => (
          <div className="crud-product-cell">
            {registro.imagem && (
              <img src={`/img/${encodeURIComponent(registro.imagem)}`} alt="" />
            )}
            <strong>{registro.nome_produto}</strong>
          </div>
        )
        : (registro) => registro[column.key]),
    })),
    {
      key: "acoes",
      label: "Ações",
      render: (registro) => (
        <div className="crud-actions">
          <Link
            className="btn btn-sm crud-orange-button"
            to={`${config.rota}/${registro[config.idField]}/editar`}
          >
            Editar
          </Link>
          <button
            className="btn btn-sm btn-danger"
            type="button"
            onClick={() => setRegistroSelecionado(registro)}
          >
            Excluir
          </button>
        </div>
      ),
    },
  ];

  return (
    <section className="crud-page">
      <div className="crud-page__top">
        <div>
          <h1>Gerenciar {config.titulo}</h1>
          <p>Cadastre, edite e remova {config.titulo.toLowerCase()}.</p>
          <Link className="btn btn-secondary" to="/admin">← Voltar</Link>
        </div>
        <Link className="btn crud-orange-button" to={`${config.rota}/novo`}>
          + Novo(a) {config.singular}
        </Link>
      </div>
      {carregando && <Loading mensagem={`Carregando ${config.titulo.toLowerCase()}...`} />}
      {!carregando && erro && <ErrorMessage onRetry={() => setAtualizacao((atual) => atual + 1)}>{erro}</ErrorMessage>}
      {!carregando && !erro && (
        <>
          <div className="crud-table-card">
            <Table columns={columns} rows={registros} rowKey={config.idField} />
          </div>
          <Pagination currentPage={pagina} totalPages={totalPaginas} onPageChange={setPagina} />
        </>
      )}
      <ConfirmModal
        aberto={Boolean(registroSelecionado)}
        mensagem={`Tem certeza que deseja excluir ${config.singular.toLowerCase()}${registroSelecionado?.nome_produto || registroSelecionado?.nome_evento || registroSelecionado?.nome_categoria ? ` "${registroSelecionado.nome_produto || registroSelecionado.nome_evento || registroSelecionado.nome_categoria}"` : ""}?`}
        confirmando={excluindo}
        onCancelar={() => setRegistroSelecionado(null)}
        onConfirmar={confirmarExclusao}
      />
    </section>
  );
}
