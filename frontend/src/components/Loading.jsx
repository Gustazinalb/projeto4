export default function Loading({ mensagem = "Carregando..." }) {
  return (
    <div className="loading-state" role="status" aria-live="polite">
      <span className="spinner-border spinner-border-sm" aria-hidden="true" />
      <span>{mensagem}</span>
    </div>
  );
}
