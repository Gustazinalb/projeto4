export default function Input({
  id,
  label,
  error,
  className = "",
  ...props
}) {
  return (
    <div className="mb-3">
      <label className="form-label" htmlFor={id}>{label}</label>
      <input
        id={id}
        className={`form-control ${error ? "is-invalid" : ""} ${className}`.trim()}
        aria-invalid={Boolean(error)}
        aria-describedby={error ? `${id}-error` : undefined}
        {...props}
      />
      {error && <div id={`${id}-error`} className="invalid-feedback">{error}</div>}
    </div>
  );
}
