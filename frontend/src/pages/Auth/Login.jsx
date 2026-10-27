import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import Button from "../../components/Button.jsx";
import Input from "../../components/Input.jsx";
import { useAuth } from "../../contexts/AuthContext.jsx";
import { getApiErrorMessage } from "../../utils/apiError.js";
import { isValidEmail } from "../../utils/validators.js";
import "./Auth.css";

export default function Login() {
  const navigate = useNavigate();
  const { login } = useAuth();
  const [email, setEmail] = useState("");
  const [senha, setSenha] = useState("");
  const [mostrarSenha, setMostrarSenha] = useState(false);
  const [erros, setErros] = useState({});
  const [enviando, setEnviando] = useState(false);

  async function enviar(event) {
    event.preventDefault();
    const novosErros = {};

    if (!email.trim()) novosErros.email = "Informe seu e-mail.";
    else if (!isValidEmail(email)) novosErros.email = "Informe um e-mail válido.";
    if (!senha) novosErros.senha = "Informe sua senha.";

    setErros(novosErros);
    if (Object.keys(novosErros).length) return;

    setEnviando(true);
    try {
      await login({ email: email.trim(), senha });
      toast.success("Login realizado com sucesso.");
      navigate("/admin", { replace: true });
    } catch (error) {
      toast.error(getApiErrorMessage(error, "Não foi possível entrar. Verifique seus dados."));
    } finally {
      setEnviando(false);
    }
  }

  return (
    <main className="auth-page">
      <section className="auth-card" aria-labelledby="login-title">
        <img className="auth-card__logo" src="/img/logo.png" alt="Logo Brasa-Bar" />
        <h1 id="login-title"><span>BRASA</span>-BAR</h1>
        <h2>Painel Administrativo</h2>
        <form onSubmit={enviar} noValidate>
          <Input
            id="email"
            label="E-mail"
            type="email"
            autoComplete="email"
            placeholder="Digite seu e-mail"
            value={email}
            error={erros.email}
            onChange={(event) => {
              setEmail(event.target.value);
              setErros((atuais) => ({ ...atuais, email: "" }));
            }}
            required
          />
          <div className="auth-password-field">
            <Input
              id="senha"
              label="Senha"
              type={mostrarSenha ? "text" : "password"}
              autoComplete="current-password"
              placeholder="Digite sua senha"
              value={senha}
              error={erros.senha}
              onChange={(event) => {
                setSenha(event.target.value);
                setErros((atuais) => ({ ...atuais, senha: "" }));
              }}
              required
            />
            <button
              className="auth-password-toggle"
              type="button"
              aria-label={mostrarSenha ? "Ocultar senha" : "Mostrar senha"}
              onClick={() => setMostrarSenha((mostrar) => !mostrar)}
            >
              <i className={`fa-solid ${mostrarSenha ? "fa-eye-slash" : "fa-eye"}`} aria-hidden="true" />
            </button>
          </div>
          <Button type="submit" className="auth-submit" disabled={enviando}>
            {enviando ? "Entrando..." : "Entrar"}
          </Button>
        </form>
        <p className="auth-card__footnote">🔒 Acesso restrito. Apenas usuários cadastrados.</p>
        <p className="auth-card__link">
          Ainda não tem conta? <Link to="/cadastro">Cadastre-se</Link>
        </p>
      </section>
    </main>
  );
}
