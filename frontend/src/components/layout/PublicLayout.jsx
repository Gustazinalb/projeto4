import { Outlet } from "react-router-dom";
import Footer from "./Footer.jsx";
import Header from "./Header.jsx";

export default function PublicLayout() {
  return (
    <div className="public-layout">
      <Header />
      <Outlet />
      <Footer />
    </div>
  );
}
