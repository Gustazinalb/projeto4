import { Link } from "react-router-dom";

const cards = [
  {
    to: "/dashboard",
    icone: "📊",
    titulo: "Dashboard",
    descricao: "Visualize os principais dados do sistema.",
  },
  {
    to: "/produtos",
    icone: "🥃",
    titulo: "Produtos",
    descricao: "Cadastre, edite e exclua produtos.",
  },
  {
    to: "/categorias",
    icone: "📂",
    titulo: "Categorias",
    descricao: "Gerencie as categorias dos produtos.",
  },
  {
    to: "/eventos",
    icone: "📅",
    titulo: "Eventos",
    descricao: "Cadastre e gerencie eventos.",
  },
  {
    to: "/mesas",
    icone: "🪑",
    titulo: "Mesas",
    descricao: "Gerencie mesas e seus status.",
  },
];

export default function AdminHome() {
  return (
    <section className="admin-home">
      <header className="admin-home__heading">
        <h1>Painel Administrativo</h1>
        <p>Gerencie os produtos, categorias, eventos e mesas do Brasa-Bar.</p>
      </header>
      <div className="admin-home__cards">
        {cards.map(({ to, icone, titulo, descricao }) => (
          <Link className="admin-home__card" to={to} key={to}>
            <span aria-hidden="true">{icone}</span>
            <h2>{titulo}</h2>
            <p>{descricao}</p>
          </Link>
        ))}
      </div>
    </section>
  );
}
