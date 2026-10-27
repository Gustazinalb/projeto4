export default function Pagination({
  currentPage,
  totalPages,
  onPageChange,
}) {
  if (totalPages <= 1) {
    return null;
  }

  return (
    <nav className="pagination-control" aria-label="Paginação">
      <button
        className="btn btn-outline-light"
        type="button"
        onClick={() => onPageChange(currentPage - 1)}
        disabled={currentPage <= 1}
      >
        Anterior
      </button>
      <span className="pagination-control__status" aria-live="polite">
        Página {currentPage} de {totalPages}
      </span>
      <button
        className="btn btn-outline-light"
        type="button"
        onClick={() => onPageChange(currentPage + 1)}
        disabled={currentPage >= totalPages}
      >
        Próxima
      </button>
    </nav>
  );
}
