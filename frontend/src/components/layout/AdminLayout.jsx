import { NavLink, Outlet, useNavigate } from "react-router-dom";
import { useAuth } from "../../contexts/AuthContext.jsx";
import "./AdminLayout.css";

const menu = [
  { to: "/dashboard", label: "📊 Dashboard" },
  { to: "/produtos", label: "🥃 Produtos" },
  { to: "/categorias", label: "📂 Categorias" },
  { to: "/eventos", label: "📅 Eventos" },
  { to: "/mesas", label: "🪑 Mesas" },
];

export default function AdminLayout() {
  const navigate = useNavigate();
  const { logout, user } = useAuth();

  function sair() {
    logout();
    navigate("/login");
  }

  return (
    <div className="admin-shell">
      <aside className="admin-sidebar">
        <div className="admin-sidebar__brand">
          <h2>BRASA-BAR</h2>
          <span>Painel Administrativo</span>
        </div>
        <nav className="admin-sidebar__menu" aria-label="Administração">
          {menu.map(({ to, label }) => (
            <NavLink key={to} to={to} className={({ isActive }) => isActive ? "active" : ""}>
              {label}
            </NavLink>
          ))}
          <NavLink to="/admin">🏠 Início</NavLink>
          <NavLink to="/perfil">👤 Perfil</NavLink>
        </nav>
        <div className="admin-sidebar__user">
          <span>{user?.nome}</span>
          <button type="button" onClick={sair}>🚪 Sair</button>
        </div>
      </aside>
      <main className="admin-content">
        <Outlet />
      </main>
    </div>
  );
}
