import { Navigate, Route, Routes } from "react-router-dom";
import AdminLayout from "./components/layout/AdminLayout.jsx";
import ProtectedRoute from "./components/ProtectedRoute.jsx";
import PublicLayout from "./components/layout/PublicLayout.jsx";
import AdminHome from "./pages/Admin/AdminHome.jsx";
import Catalogo from "./pages/Catalogo/Catalogo.jsx";
import Contato from "./pages/Contato/Contato.jsx";
import Cronograma from "./pages/Cronograma/Cronograma.jsx";
import Home from "./pages/Home/Home.jsx";
import Login from "./pages/Auth/Login.jsx";
import Cadastro from "./pages/Auth/Cadastro.jsx";
import Profile from "./pages/Profile/Profile.jsx";
import Dashboard from "./pages/Dashboard/Dashboard.jsx";
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
      <Route element={<PublicLayout />}>
        <Route path="/" element={<Home />} />
        <Route path="/catalogo" element={<Catalogo />} />
        <Route path="/cronograma" element={<Cronograma />} />
        <Route path="/contato" element={<Contato />} />
        <Route path="/login" element={<Login />} />
        <Route path="/cadastro" element={<Cadastro />} />
      </Route>
      <Route element={<ProtectedRoute />}>
        <Route element={<AdminLayout />}>
          <Route path="/admin" element={<AdminHome />} />
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/perfil" element={<Profile />} />
          <Route path="/produtos" element={<ProdutosList />} />
          <Route path="/produtos/novo" element={<ProdutosForm />} />
          <Route path="/produtos/:id/editar" element={<ProdutosForm />} />
          <Route path="/categorias" element={<CategoriasList />} />
          <Route path="/categorias/novo" element={<CategoriasForm />} />
          <Route path="/categorias/:id/editar" element={<CategoriasForm />} />
          <Route path="/eventos" element={<EventosList />} />
          <Route path="/eventos/novo" element={<EventosForm />} />
          <Route path="/eventos/:id/editar" element={<EventosForm />} />
          <Route path="/mesas" element={<MesasList />} />
          <Route path="/mesas/novo" element={<MesasForm />} />
          <Route path="/mesas/:id/editar" element={<MesasForm />} />
        </Route>
      </Route>
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
}
