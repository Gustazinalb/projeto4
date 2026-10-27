import "./Contato.css";

const informacoes = [
  {
    icone: "fa-solid fa-location-dot",
    titulo: "Endereço",
    linhas: ["Rua das Brasas, 123 - Centro", "Campo Mourão - PR"],
  },
  {
    icone: "fa-solid fa-phone",
    titulo: "Telefone / WhatsApp",
    linhas: ["(44) 99714-9528"],
  },
  {
    icone: "fa-brands fa-instagram",
    titulo: "Instagram",
    linhas: ["@brasabar"],
  },
  {
    icone: "fa-regular fa-clock",
    titulo: "Funcionamento",
    linhas: ["Terça a Domingo", "18:00 às 02:00"],
  },
];

export default function Contato() {
  return (
    <main className="page-main contato-page">
      <section className="contato-card" aria-label="Informações de contato">
        {informacoes.map(({ icone, titulo, linhas }, index) => (
          <div key={titulo}>
            {index > 0 && <hr />}
            <div className="contato-row">
              <i className={icone} aria-hidden="true" />
              <div>
                <h2>{titulo}</h2>
                {linhas.map((linha) => <p key={linha}>{linha}</p>)}
              </div>
            </div>
          </div>
        ))}
        <a
          href="https://wa.me/5544997149528"
          target="_blank"
          rel="noreferrer"
          className="contato-whatsapp"
        >
          <i className="fa-brands fa-whatsapp" aria-hidden="true" />
          CHAMAR NO WHATSAPP
        </a>
      </section>
    </main>
  );
}
