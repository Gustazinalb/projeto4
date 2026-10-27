import axios from "axios";
import { AUTH_STORAGE_KEY } from "../utils/authStorage.js";

const api = axios.create({
  baseURL: "/api",
  headers: {
    "Content-Type": "application/json",
  },
});

api.interceptors.request.use((config) => {
  const storedAuth = localStorage.getItem(AUTH_STORAGE_KEY);

  if (storedAuth) {
    let token;

    try {
      token = JSON.parse(storedAuth)?.token;
    } catch {
      localStorage.removeItem(AUTH_STORAGE_KEY);
    }

    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
  }

  return config;
});

api.interceptors.response.use(
  (response) => response,
  (error) => {
    const isLoginRequest = error.config?.url?.endsWith("/login.php");

    if (error.response?.status === 401 && !isLoginRequest) {
      localStorage.removeItem(AUTH_STORAGE_KEY);
      window.dispatchEvent(new Event("brasa-bar:unauthorized"));

      if (window.location.pathname !== "/login") {
        window.location.assign("/login");
      }
    }

    return Promise.reject(error);
  },
);

export default api;
