import { useEffect, useMemo, useState } from "react";
import { Link, useNavigate, useParams } from "react-router-dom";
import { toast } from "react-toastify";
import Button from "../Button.jsx";
import ErrorMessage from "../ErrorMessage.jsx";
import Input from "../Input.jsx";
import Loading from "../Loading.jsx";
import api from "../../services/api.js";
import {
  getResourceById,
  resourceConfig,
  saveResource,
} from "../../services/resources.js";
import { getApiErrorMessage } from "../../utils/apiError.js";
import "./Crud.css";

function createInitialValues(campos) {
  return Object.fromEntries(campos.map((campo) => [campo.name, campo.defaultValue ?? ""]));
}

export default function ResourceForm({ resource }) {
  const config = resourceConfig[resource];
  const navigate = useNavigate();
  const { id } = useParams();
  const editando = Boolean(id);
  const [valores, setValores] = useState(() => createInitialValues(config.campos));
  const [categorias, setCategorias] = useState([]);
  const [erros, setErros] = useState({});
  const [carregando, setCarregando] = useState(editando || resource === "produtos");
  const [erroCarregamento, setErroCarregamento] = useState("");
  const [enviando, setEnviando] = useState(false);

  const campos = useMemo(() => config.campos, [config]);

  useEffect(() => {
    let ativo = true;

    async function prepararFormulario() {
      setCarregando(true);
      setErroCarregamento("");
      try {
        const [registro, respostaCategorias] = await Promise.all([
          editando ? getResourceById(resource, id) : Promise.resolve(null),
          resource === "produtos" ? api.get("/categorias.php") : Promise.resolve(null),
        ]);
        if (editando && !registro) {
          throw new Error(`${config.singular} não encontrado(a).`);
        }
        if (respostaCategorias && !Array.isArray(respostaCategorias.data)) {
          throw new Error("Não foi possível carregar as categorias.");
        }
        if (ativo) {
          if (respostaCategorias) setCategorias(respostaCategorias.data);
          if (registro) {
            setValores((atuais) => ({
              ...atuais,
              ...Object.fromEntries(campos.map(({ name }) => [
                name,
                registro[name] === null || registro[name] === undefined
                  ? ""
                  : String(registro[name]),
              ])),
            }));
          }
        }
      } catch (error) {
        if (ativo) setErroCarregamento(getApiErrorMessage(error, `Não foi possível carregar os dados de ${config.singular.toLowerCase()}.`));
      } finally {
        if (ativo) setCarregando(false);
      }
    }

    prepararFormulario();
    return () => {
      ativo = false;
    };
  }, [campos, config.singular, editando, id, resource]);

  function atualizar(campo, valor) {
    setValores((atuais) => ({ ...atuais, [campo]: valor }));
    setErros((atuais) => ({ ...atuais, [campo]: "" }));
  }

  function validar() {
    const novosErros = {};
    campos.forEach((campo) => {
      const valor = valores[campo.name];
      if (campo.required && String(valor).trim() === "") {
        novosErros[campo.name] = "Este campo é obrigatório.";
        return;
      }
      if (campo.type === "number" && String(valor).trim() !== "") {
        const numero = Number(valor);
        if (!Number.isFinite(numero)) novosErros[campo.name] = "Informe um número válido.";
        else if (campo.min !== undefined && numero < campo.min) {
          novosErros[campo.name] = `O valor mínimo é ${campo.min}.`;
        } else if (campo.step === "1" && !Number.isInteger(numero)) {
          novosErros[campo.name] = "Informe um número inteiro.";
        }
      }
    });
    setErros(novosErros);
    return Object.keys(novosErros).length === 0;
  }

  async function enviar(event) {
    event.preventDefault();
    if (!validar()) return;

    const dados = Object.fromEntries(campos.map(({ name }) => [name, valores[name]]));
    campos.forEach((campo) => {
      if (campo.type === "number") dados[campo.name] = Number(dados[campo.name]);
    });

    setEnviando(true);
    try {
      const { data } = await saveResource(resource, dados, id);
      toast.success(data?.mensagem || `${config.singular} salvo(a) com sucesso.`);
      navigate(config.rota);
    } catch (error) {
      toast.error(getApiErrorMessage(error, `Não foi possível salvar ${config.singular.toLowerCase()}.`));
    } finally {
      setEnviando(false);
    }
  }

  function renderField(campo) {
    const commonProps = {
      id: campo.name,
      label: campo.label,
      required: campo.required,
      value: valores[campo.name] ?? "",
      error: erros[campo.name],
      onChange: (event) => atualizar(campo.name, event.target.value),
    };

    if (campo.type === "textarea") {
      return (
        <div className="mb-3" key={campo.name}>
          <label className="form-label" htmlFor={campo.name}>{campo.label}</label>
          <textarea
            id={campo.name}
            className={`form-control ${erros[campo.name] ? "is-invalid" : ""}`}
            value={valores[campo.name]}
            maxLength={campo.maxLength}
            required={campo.required}
            onChange={(event) => atualizar(campo.name, event.target.value)}
            rows={4}
            aria-invalid={Boolean(erros[campo.name])}
          />
          {erros[campo.name] && <div className="invalid-feedback">{erros[campo.name]}</div>}
        </div>
      );
    }

    if (campo.type === "select" || campo.type === "category") {
      const opcoes = campo.type === "category"
        ? categorias.map((categoria) => ({
          value: categoria.id_categoria,
          label: categoria.nome_categoria,
        }))
        : campo.options;
      return (
        <div className="mb-3" key={campo.name}>
          <label className="form-label" htmlFor={campo.name}>{campo.label}</label>
          <select
            id={campo.name}
            className={`form-select ${erros[campo.name] ? "is-invalid" : ""}`}
            value={valores[campo.name]}
            required={campo.required}
            onChange={(event) => atualizar(campo.name, event.target.value)}
            aria-invalid={Boolean(erros[campo.name])}
          >
            <option value="">Selecione</option>
            {opcoes.map((opcao) => (
              <option value={opcao.value} key={opcao.value}>{opcao.label}</option>
            ))}
          </select>
          {erros[campo.name] && <div className="invalid-feedback">{erros[campo.name]}</div>}
        </div>
      );
    }

    return (
      <Input
        key={campo.name}
        {...commonProps}
        type={campo.type || "text"}
        min={campo.min}
        step={campo.step}
        maxLength={campo.maxLength}
        placeholder={campo.placeholder}
      />
    );
  }

  return (
    <section className="crud-page">
      <div className="crud-page__top">
        <div>
          <h1>{editando ? `Editar ${config.singular}` : `Novo(a) ${config.singular}`}</h1>
          <p>{editando ? "Altere os dados e salve as mudanças." : `Preencha os dados para cadastrar ${config.singular.toLowerCase()}.`}</p>
        </div>
      </div>
      {carregando && <Loading mensagem="Carregando formulário..." />}
      {!carregando && erroCarregamento && <ErrorMessage>{erroCarregamento}</ErrorMessage>}
      {!carregando && !erroCarregamento && (
        <form className="crud-form-card" onSubmit={enviar} noValidate>
          {campos.map(renderField)}
          {resource === "produtos" && (
            <small className="form-text text-muted d-block mb-3">
              Informe apenas o nome do arquivo dentro da pasta img.
            </small>
          )}
          {resource === "eventos" && (
            <p className="text-muted">
              A API atual não oferece operações para vincular produtos ao evento.
            </p>
          )}
          <div className="d-flex gap-2">
            <Link className="btn btn-secondary" to={config.rota}>Cancelar</Link>
            <Button type="submit" className="crud-orange-button" disabled={enviando}>
              {enviando ? "Salvando..." : editando ? "Salvar alterações" : `Cadastrar ${config.singular.toLowerCase()}`}
            </Button>
          </div>
        </form>
      )}
    </section>
  );
}
