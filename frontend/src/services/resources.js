import api from "./api.js";
import { getApiErrorMessage } from "../utils/apiError.js";

export const resourceConfig = {
  produtos: {
    titulo: "Produtos",
    singular: "Produto",
    endpoint: "/produtos_crud.php",
    rota: "/produtos",
    idField: "id_produto",
    campos: [
      { name: "nome_produto", label: "Nome do produto", required: true, maxLength: 100 },
      { name: "preco", label: "Preço", type: "number", required: true, min: 0, step: "0.01" },
      { name: "estoque", label: "Estoque", type: "number", required: true, min: 0, step: "1" },
      { name: "id_categoria", label: "Categoria", type: "category", required: true },
      { name: "imagem", label: "Nome da imagem", required: false, maxLength: 255, placeholder: "exemplo.png" },
    ],
    colunas: [
      { key: "nome_produto", label: "Produto", type: "produto" },
      { key: "nome_categoria", label: "Categoria" },
      {
        key: "preco",
        label: "Preço",
        render: (produto) => Number(produto.preco).toLocaleString("pt-BR", {
          style: "currency",
          currency: "BRL",
        }),
      },
      { key: "estoque", label: "Estoque" },
    ],
  },
  categorias: {
    titulo: "Categorias",
    singular: "Categoria",
    endpoint: "/categorias_crud.php",
    rota: "/categorias",
    idField: "id_categoria",
    campos: [
      { name: "nome_categoria", label: "Nome da categoria", required: true, maxLength: 50 },
    ],
    colunas: [
      { key: "id_categoria", label: "ID" },
      { key: "nome_categoria", label: "Categoria" },
    ],
  },
  eventos: {
    titulo: "Eventos",
    singular: "Evento",
    endpoint: "/eventos_crud.php",
    rota: "/eventos",
    idField: "id_evento",
    campos: [
      { name: "nome_evento", label: "Nome do evento", required: true, maxLength: 100 },
      { name: "data_evento", label: "Data do evento", type: "date", required: true },
      { name: "hora_evento", label: "Horário do evento", type: "time", required: true },
      { name: "descricao", label: "Descrição", type: "textarea", required: true, maxLength: 500 },
      { name: "imagem", label: "Nome da imagem", required: false, maxLength: 255, placeholder: "exemplo.png" },
    ],
    colunas: [
      { key: "nome_evento", label: "Evento" },
      {
        key: "data_evento",
        label: "Data",
        render: (evento) => evento.data_evento
          ? new Date(`${evento.data_evento}T00:00:00`).toLocaleDateString("pt-BR")
          : "",
      },
      { key: "hora_evento", label: "Horário" },
      { key: "descricao", label: "Descrição" },
    ],
  },
  mesas: {
    titulo: "Mesas",
    singular: "Mesa",
    endpoint: "/mesas_crud.php",
    rota: "/mesas",
    idField: "id_mesa",
    campos: [
      { name: "numero", label: "Número", type: "number", required: true, min: 1, step: "1" },
      { name: "capacidade", label: "Capacidade", type: "number", required: true, min: 1, step: "1" },
      {
        name: "status",
        label: "Status",
        type: "select",
        required: true,
        defaultValue: "disponivel",
        options: [
          { value: "disponivel", label: "Disponível" },
          { value: "ocupada", label: "Ocupada" },
          { value: "reservada", label: "Reservada" },
        ],
      },
    ],
    colunas: [
      { key: "numero", label: "Número" },
      { key: "capacidade", label: "Capacidade" },
      {
        key: "status",
        label: "Status",
        render: (mesa) => ({
          disponivel: "Disponível",
          ocupada: "Ocupada",
          reservada: "Reservada",
        }[mesa.status] || mesa.status),
      },
    ],
  },
};

function getResourceConfig(resource) {
  const config = resourceConfig[resource];
  if (!config) throw new Error("Recurso administrativo desconhecido.");
  return config;
}

export async function listResources(resource, pagina = 1, limite = 10) {
  const config = getResourceConfig(resource);
  const { data } = await api.get(config.endpoint, { params: { pagina, limite } });

  if (!Array.isArray(data?.dados)) {
    throw new Error(getApiErrorMessage({ response: { data } }, `Não foi possível carregar ${config.titulo.toLowerCase()}.`));
  }

  return {
    dados: data.dados,
    total: Number(data.total) || 0,
    totalPaginas: Math.max(1, Number(data.total_paginas) || 0),
  };
}

export async function listAllResources(resource) {
  const config = getResourceConfig(resource);
  const primeiraPagina = await listResources(resource, 1, 100);
  const registros = [...primeiraPagina.dados];

  for (let pagina = 2; pagina <= primeiraPagina.totalPaginas; pagina += 1) {
    const resultado = await listResources(resource, pagina, 100);
    registros.push(...resultado.dados);
  }

  return registros;
}

export async function getResourceById(resource, id) {
  const registros = await listAllResources(resource);
  const config = getResourceConfig(resource);
  return registros.find((registro) => String(registro[config.idField]) === String(id));
}

export async function saveResource(resource, dados, id) {
  const config = getResourceConfig(resource);
  return id
    ? api.put(`${config.endpoint}?id=${encodeURIComponent(id)}`, dados)
    : api.post(config.endpoint, dados);
}

export async function deleteResource(resource, id) {
  const config = getResourceConfig(resource);
  return api.delete(`${config.endpoint}?id=${encodeURIComponent(id)}`);
}
