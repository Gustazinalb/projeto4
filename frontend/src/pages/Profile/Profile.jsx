import { useState } from "react";
import { toast } from "react-toastify";
import Button from "../../components/Button.jsx";
import Input from "../../components/Input.jsx";
import { useAuth } from "../../contexts/AuthContext.jsx";
import api from "../../services/api.js";
import { getApiErrorMessage } from "../../utils/apiError.js";
import { isStrongPassword, isValidCPF } from "../../utils/validators.js";
import "../Auth/Auth.css";
import "../../components/Crud/Crud.css";

export default function Profile() {
  const { user, updateUser } = useAuth();
  const [formulario, setFormulario] = useState({
    nome: user?.nome || "",
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
      const nome = formulario.nome.trim();
      const { data } = await api.put("/usuario_editar.php", {
        nome,
        email: user.email,
        cpf: formulario.cpf,
        senha: formulario.senha,
      });
      updateUser({ nome, cpf: formulario.cpf });
      setFormulario((atual) => ({ ...atual, senha: "", confirmarSenha: "" }));
      toast.success(data?.mensagem || "Dados atualizados com sucesso.");
    } catch (error) {
      toast.error(getApiErrorMessage(error, "Não foi possível atualizar seu perfil."));
    } finally {
      setEnviando(false);
    }
  }

  return (
    <section className="profile-page">
      <div className="admin-home__heading">
        <h1>Meu perfil</h1>
        <p>Atualize seus dados pessoais e sua senha.</p>
      </div>
      <form className="profile-form" onSubmit={enviar} noValidate>
        <Input
          id="perfil-nome"
          label="Nome"
          autoComplete="name"
          value={formulario.nome}
          error={erros.nome}
          onChange={(event) => atualizar("nome", event.target.value)}
          required
        />
        <Input
          id="perfil-email"
          label="E-mail"
          type="email"
          value={user?.email || ""}
          readOnly
          required
        />
        <Input
          id="perfil-cpf"
          label="CPF"
          inputMode="numeric"
          value={formulario.cpf}
          error={erros.cpf}
          onChange={(event) => atualizar("cpf", event.target.value)}
          required
        />
        <Input
          id="perfil-senha"
          label="Nova senha"
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
          id="perfil-confirmar-senha"
          label="Confirmar nova senha"
          type="password"
          autoComplete="new-password"
          value={formulario.confirmarSenha}
          error={erros.confirmarSenha}
          onChange={(event) => atualizar("confirmarSenha", event.target.value)}
          required
        />
        <Button type="submit" className="auth-submit" disabled={enviando}>
          {enviando ? "Salvando..." : "Salvar alterações"}
        </Button>
      </form>
    </section>
  );
}
