export default function Button({
  children,
  type = "button",
  disabled = false,
  ...props
}) {
  return (
    <button type={type} disabled={disabled} {...props}>
      {children}
    </button>
  );
}
