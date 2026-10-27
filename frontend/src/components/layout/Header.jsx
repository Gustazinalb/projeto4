import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "../../contexts/AuthContext.jsx";

const links = [
  { to: "/", label: "Home" },
  { to: "/catalogo", label: "Catálogo" },
  { to: "/cronograma", label: "Cronograma" },
  { to: "/contato", label: "Contato" },
  { to: "/admin", label: "Admin" },
];

export default function Header() {
  const navigate = useNavigate();
  const { isAuthenticated, user, logout } = useAuth();
  const [busca, setBusca] = useState("");
  const [menuAberto, setMenuAberto] = useState(false);

  function buscarProdutos(event) {
    event.preventDefault();
    navigate(`/catalogo?busca=${encodeURIComponent(busca.trim())}`);
    setMenuAberto(false);
  }

  function sair() {
    logout();
    navigate("/login");
    setMenuAberto(false);
  }

  return (
    <header className="public-header">
      <nav className="navbar navbar-expand-lg navbar-dark">
        <div className="container-fluid">
          <Link className="navbar-brand" to="/" aria-label="Brasa-Bar - Home">
            <img src="/img/logo.png" alt="Logo Brasa-Bar" />
          </Link>
          <button
            className="navbar-toggler"
            type="button"
            aria-controls="site-menu"
            aria-expanded={menuAberto}
            aria-label="Alternar navegação"
            onClick={() => setMenuAberto((aberto) => !aberto)}
          >
            <span className="navbar-toggler-icon" />
          </button>
          <div
            id="site-menu"
            className={`collapse navbar-collapse public-menu ${menuAberto ? "show" : ""}`}
          >
            <form className="d-flex gap-2 my-2 my-lg-0" onSubmit={buscarProdutos}>
              <input
                className="form-control site-search"
                type="search"
                aria-label="Pesquisar produto"
                placeholder="🔍 Pesquisar produto"
                value={busca}
                onChange={(event) => setBusca(event.target.value)}
              />
              <button className="btn btn-dark" type="submit">Buscar</button>
            </form>
            <ul className="navbar-nav ms-auto">
              {links.map(({ to, label }) => (
                <li className="nav-item" key={to}>
                  <Link className="nav-link" to={to} onClick={() => setMenuAberto(false)}>
                    {label}
                  </Link>
                </li>
              ))}
              {isAuthenticated && (
                <>
                  <li className="nav-item">
                    <Link className="nav-link" to="/perfil" onClick={() => setMenuAberto(false)}>
                      {user?.nome || "Meu perfil"}
                    </Link>
                  </li>
                  <li className="nav-item">
                    <button className="nav-link btn btn-link" type="button" onClick={sair}>
                      Sair
                    </button>
                  </li>
                </>
              )}
            </ul>
          </div>
        </div>
      </nav>
    </header>
  );
}
