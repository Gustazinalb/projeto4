import axios from "axios";
import { AUTH_STORAGE_KEY } from "../utils/authStorage.js";

const api = axios.create({
  baseURL: "http://localhost:8080/api",
  headers: {
    "Content-Type": "application/json",
  },
});

api.interceptors.request.use((config) => {
  const storedAuth = localStorage.getItem(AUTH_STORAGE_KEY);

  if (storedAuth) {
    const { token } = JSON.parse(storedAuth);

    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
  }

  return config;
});

export default api;
