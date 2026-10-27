import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import Button from "../../components/Button.jsx";
import Input from "../../components/Input.jsx";
import api from "../../services/api.js";
import { getApiErrorMessage } from "../../utils/apiError.js";
import { isStrongPassword, isValidCPF, isValidEmail } from "../../utils/validators.js";
import "./Auth.css";

export default function Cadastro() {
  const navigate = useNavigate();
  const [formulario, setFormulario] = useState({
    nome: "",
    email: "",
    cpf: "",
    senha: "",
    confirmarSenha: "",
  });
  const [erros, setErros] = useState({});
  const [enviando, setEnviando] = useState(false);

  function atualizar(campo, valor) {
    setFormulario((atual) => ({ ...atual, [campo]: valor }));
    setErros((atuais) => ({ ...atuais, [campo]: "" }));
  }

  async function enviar(event) {
    event.preventDefault();
    const novosErros = {};

    if (!formulario.nome.trim()) novosErros.nome = "Informe seu nome.";
    if (!formulario.email.trim()) novosErros.email = "Informe seu e-mail.";
    else if (!isValidEmail(formulario.email)) novosErros.email = "Informe um e-mail válido.";
    if (!formulario.cpf.trim()) novosErros.cpf = "Informe seu CPF.";
    else if (!isValidCPF(formulario.cpf)) novosErros.cpf = "Informe um CPF válido.";
    if (!formulario.senha) novosErros.senha = "Informe uma senha.";
    else if (!isStrongPassword(formulario.senha)) {
      novosErros.senha = "A senha não atende aos requisitos indicados.";
    }
    if (!formulario.confirmarSenha) novosErros.confirmarSenha = "Confirme sua senha.";
    else if (formulario.senha !== formulario.confirmarSenha) {
      novosErros.confirmarSenha = "As senhas não coincidem.";
    }

    setErros(novosErros);
    if (Object.keys(novosErros).length) return;

    setEnviando(true);
    try {
      const { data } = await api.post("/cadastro.php", {
        nome: formulario.nome.trim(),
        email: formulario.email.trim(),
        cpf: formulario.cpf,
        senha: formulario.senha,
      });

      toast.success(data?.mensagem || "Cadastro realizado com sucesso.");
      navigate("/login", { replace: true });
    } catch (error) {
      toast.error(getApiErrorMessage(error, "Não foi possível realizar o cadastro."));
    } finally {
      setEnviando(false);
    }
  }

  return (
    <main className="auth-page">
      <section className="auth-card auth-card--form" aria-labelledby="cadastro-title">
        <h1 id="cadastro-title"><span>BRASA</span>-BAR</h1>
        <h2>Criar conta</h2>
        <form onSubmit={enviar} noValidate>
          <Input
            id="nome"
            label="Nome"
            autoComplete="name"
            value={formulario.nome}
            error={erros.nome}
            onChange={(event) => atualizar("nome", event.target.value)}
            required
          />
          <Input
            id="cadastro-email"
            label="E-mail"
            type="email"
            autoComplete="email"
            value={formulario.email}
            error={erros.email}
            onChange={(event) => atualizar("email", event.target.value)}
            required
          />
          <Input
            id="cpf"
            label="CPF"
            inputMode="numeric"
            autoComplete="off"
            value={formulario.cpf}
            error={erros.cpf}
            onChange={(event) => atualizar("cpf", event.target.value)}
            required
          />
          <Input
            id="cadastro-senha"
            label="Senha"
            type="password"
            autoComplete="new-password"
            value={formulario.senha}
            error={erros.senha}
            onChange={(event) => atualizar("senha", event.target.value)}
            required
          />
          <p className="auth-password-requirements">
            A senha deve conter: mínimo 8 caracteres, 1 letra maiúscula, 1 número e 1 símbolo.
          </p>
          <Input
            id="confirmar-senha"
            label="Confirmar senha"
            type="password"
            autoComplete="new-password"
            value={formulario.confirmarSenha}
            error={erros.confirmarSenha}
            onChange={(event) => atualizar("confirmarSenha", event.target.value)}
            required
          />
          <Button type="submit" className="auth-submit" disabled={enviando}>
            {enviando ? "Cadastrando..." : "Cadastrar"}
          </Button>
        </form>
        <p className="auth-card__link">
          Já tem conta? <Link to="/login">Entrar</Link>
        </p>
      </section>
    </main>
  );
}
