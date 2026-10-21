import { Navigate, Route, Routes } from "react-router-dom";
import Login from "./pages/Auth/Login.jsx";
import Profile from "./pages/Profile/Profile.jsx";
import ProdutosList from "./pages/Produtos/List.jsx";
import ProdutosForm from "./pages/Produtos/Form.jsx";
import CategoriasList from "./pages/Categorias/List.jsx";
import CategoriasForm from "./pages/Categorias/Form.jsx";
import EventosList from "./pages/Eventos/List.jsx";
import EventosForm from "./pages/Eventos/Form.jsx";
import MesasList from "./pages/Mesas/List.jsx";
import MesasForm from "./pages/Mesas/Form.jsx";

export default function App() {
  return (
    <Routes>
      <Route path="/login" element={<Login />} />
      <Route path="/perfil" element={<Profile />} />
      <Route path="/produtos" element={<ProdutosList />} />
      <Route path="/produtos/novo" element={<ProdutosForm />} />
      <Route path="/categorias" element={<CategoriasList />} />
      <Route path="/categorias/novo" element={<CategoriasForm />} />
      <Route path="/eventos" element={<EventosList />} />
      <Route path="/eventos/novo" element={<EventosForm />} />
      <Route path="/mesas" element={<MesasList />} />
      <Route path="/mesas/novo" element={<MesasForm />} />
      <Route path="*" element={<Navigate to="/login" replace />} />
    </Routes>
  );
}
