export default function ConfirmModal({
  aberto,
  titulo = "Confirmar exclusão",
  mensagem,
  confirmando = false,
  onCancelar,
  onConfirmar,
}) {
  if (!aberto) return null;

  return (
    <div className="confirm-modal-backdrop" role="presentation">
      <section
        className="confirm-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirm-modal-title"
      >
        <h2 id="confirm-modal-title">{titulo}</h2>
        <p>{mensagem}</p>
        <div className="confirm-modal__actions">
          <button className="btn btn-secondary" type="button" onClick={onCancelar} disabled={confirmando}>
            Cancelar
          </button>
          <button className="btn btn-danger" type="button" onClick={onConfirmar} disabled={confirmando}>
            {confirmando ? "Excluindo..." : "Excluir"}
          </button>
        </div>
      </section>
    </div>
  );
}
