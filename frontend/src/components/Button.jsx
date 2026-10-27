export default function Button({
  children,
  type = "button",
  disabled = false,
  variant = "primary",
  className = "",
  ...props
}) {
  return (
    <button
      type={type}
      className={`btn btn-${variant} ${className}`.trim()}
      disabled={disabled}
      {...props}
    >
      {children}
    </button>
  );
}
