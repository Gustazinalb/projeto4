import { useEffect, useState } from "react";
import api from "../../services/api.js";
import { getApiErrorMessage } from "../../utils/apiError.js";
import "./Cronograma.css";

const diasSemana = [
  "Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado",
];

const destaques = [
  { icone: "fa-calendar-days", titulo: "Atualizado", texto: "Eventos toda semana" },
  { icone: "fa-beer-mug-empty", titulo: "Happy Hour", texto: "Promoções especiais" },
  { icone: "fa-ticket", titulo: "Eventos", texto: "Entrada gratuita" },
  { icone: "fa-bell", titulo: "Novidades", texto: "Toda semana" },
];

export default function Cronograma() {
  const [eventos, setEventos] = useState([]);
  const [carregando, setCarregando] = useState(true);
  const [erro, setErro] = useState("");

  useEffect(() => {
    let ativo = true;

    api.get("/eventos.php")
      .then(({ data }) => {
        if (data?.sucesso !== true || !Array.isArray(data.eventos)) {
          throw new Error("Resposta inválida ao carregar os eventos.");
        }
        if (ativo) setEventos(data.eventos);
      })
      .catch((error) => {
        if (ativo) setErro(getApiErrorMessage(error, "Não foi possível carregar os eventos."));
      })
      .finally(() => {
        if (ativo) setCarregando(false);
      });

    return () => {
      ativo = false;
    };
  }, []);

  return (
    <main className="page-main cronograma-page">
      <section className="cronograma">
        <div className="page-heading"><h1>Cronograma de Eventos</h1></div>
        {carregando && <p className="cronograma-state" role="status">Carregando eventos...</p>}
        {!carregando && erro && <p className="cronograma-state" role="alert">{erro}</p>}
        {!carregando && !erro && (
          <div className="cronograma-grid">
            {eventos.length === 0 ? (
              <article className="evento-card">
                <div className="evento-card__top"><h2>Eventos</h2></div>
                <div className="evento-card__content">
                  <i className="fa-solid fa-calendar-xmark evento-card__icon" aria-hidden="true" />
                  <h3>Nenhum evento cadastrado</h3>
                </div>
              </article>
            ) : eventos.map((evento) => {
              const data = new Date(`${evento.data_evento}T00:00:00`);
              return (
                <article className="evento-card" key={evento.id_evento}>
                  <div className="evento-card__top">
                    <h2>{diasSemana[data.getDay()]}</h2>
                    <span>{data.toLocaleDateString("pt-BR")}</span>
                  </div>
                  <div className="evento-card__content">
                    <i className="fa-solid fa-calendar-check evento-card__icon" aria-hidden="true" />
                    <h4>{evento.hora_evento}</h4>
                    <h3>{evento.nome_evento}</h3>
                    <p>{evento.descricao || ""}</p>
                  </div>
                </article>
              );
            })}
          </div>
        )}
      </section>

      <section className="cronograma-highlights" aria-label="Destaques">
        {destaques.map(({ icone, titulo, texto }) => (
          <article className="cronograma-highlight" key={titulo}>
            <i className={`fa-solid ${icone}`} aria-hidden="true" />
            <h2>{titulo}</h2>
            <p>{texto}</p>
          </article>
        ))}
      </section>

      <section className="evento-semana">
        <div className="evento-semana__info">
          <h2>🎸 Evento da Semana</h2>
          <h3>CONFIRA NOSSO CRONOGRAMA</h3>
          <p>Confira todos os eventos da semana e venha curtir o Brasa Bar.</p>
          <a href="/catalogo" className="evento-semana__button">VER CATÁLOGO</a>
        </div>
      </section>

      <section className="instagram">
        <h2>📸 SIGA NOSSO INSTAGRAM</h2>
        <div className="instagram__grid">
          {["jack-apple.png", "skol.png", "perola.png", "intencion-pink.png"].map((imagem) => (
            <img key={imagem} src={`/img/${imagem}`} alt={`Produto Brasa-Bar: ${imagem.replace(".png", "")}`} />
          ))}
        </div>
        <p>@brasa.bar</p>
      </section>
    </main>
  );
}
