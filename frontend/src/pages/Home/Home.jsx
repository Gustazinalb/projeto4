import { Link } from "react-router-dom";
import "./Home.css";

export default function Home() {
  return (
    <main className="home-page">
      <div className="home-hero">
        <img src="/img/home.png" alt="Ambiente do Brasa-Bar" />
        <div className="home-hero__actions">
          <Link className="home-button home-button--orange" to="/catalogo">
            VER CATÁLOGO
          </Link>
          <Link className="home-button home-button--outline" to="/cronograma">
            VER EVENTOS
          </Link>
        </div>
      </div>
    </main>
  );
}
