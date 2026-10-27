import { Link } from "react-router-dom";

export default function Footer() {
  return (
    <footer className="site-footer">
      <div className="site-footer__brand">
        <h2>BRASA-BAR</h2>
        <p>Onde cada noite tem uma história.</p>
      </div>
      <div className="site-footer__contact">
        <span>📍 Campo Mourão - PR</span>
        <span>📞 (44) 99714-9528</span>
        <span>📸 @brasa-bar</span>
      </div>
      <nav className="site-footer__nav" aria-label="Navegação do rodapé">
        <Link to="/">Home</Link>
        <Link to="/catalogo">Catálogo</Link>
        <Link to="/cronograma">Cronograma</Link>
        <Link to="/contato">Contato</Link>
      </nav>
      <div className="site-footer__copy">
        © 2026 Brasa-Bar - Todos os direitos reservados
      </div>
    </footer>
  );
}
