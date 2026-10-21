import { createContext, useCallback, useContext, useMemo, useState } from "react";
import api from "../services/api.js";
import { AUTH_STORAGE_KEY } from "../utils/authStorage.js";

const AuthContext = createContext(null);

function readStoredAuth() {
  const storedAuth = localStorage.getItem(AUTH_STORAGE_KEY);

  if (!storedAuth) {
    return { token: null, user: null };
  }

  const auth = JSON.parse(storedAuth);

  if (
    typeof auth?.token !== "string" ||
    !auth.user ||
    typeof auth.user !== "object"
  ) {
    localStorage.removeItem(AUTH_STORAGE_KEY);
    return { token: null, user: null };
  }

  return { token: auth.token, user: auth.user };
}

export function AuthProvider({ children }) {
  const [auth, setAuth] = useState(readStoredAuth);

  const login = useCallback(async (credentials) => {
    const { data } = await api.post("/login.php", credentials);

    if (
      data?.sucesso !== true ||
      typeof data.token !== "string" ||
      !data.usuario ||
      typeof data.usuario !== "object"
    ) {
      throw new Error(data?.mensagem || "Não foi possível autenticar.");
    }

    const nextAuth = { token: data.token, user: data.usuario };
    localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify(nextAuth));
    setAuth(nextAuth);

    return data.usuario;
  }, []);

  const logout = useCallback(() => {
    localStorage.removeItem(AUTH_STORAGE_KEY);
    setAuth({ token: null, user: null });
  }, []);

  const value = useMemo(
    () => ({
      token: auth.token,
      user: auth.user,
      isAuthenticated: Boolean(auth.token),
      login,
      logout,
    }),
    [auth, login, logout],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error("useAuth deve ser usado dentro de um AuthProvider.");
  }

  return context;
}
