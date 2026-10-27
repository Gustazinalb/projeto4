export function getApiErrorMessage(error, fallback = "Não foi possível concluir a solicitação.") {
  if (Number(error?.response?.status) >= 500) return fallback;

  const message = error?.response?.data?.mensagem;
  return typeof message === "string" && message.trim() ? message : fallback;
}
