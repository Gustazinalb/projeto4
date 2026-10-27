export default function ErrorMessage({ children, onRetry }) {
  return (
    <div className="alert alert-danger" role="alert">
      <span>{children}</span>
      {onRetry && (
        <button className="btn btn-sm btn-outline-danger ms-3" type="button" onClick={onRetry}>
          Tentar novamente
        </button>
      )}
    </div>
  );
}
