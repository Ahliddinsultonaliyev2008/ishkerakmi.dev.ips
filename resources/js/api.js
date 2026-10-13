import axios from 'axios';

const TOKEN_KEY = 'ishkerakmi_token';

export const tokenStorage = {
    get: () => localStorage.getItem(TOKEN_KEY),
    set: (token) => localStorage.setItem(TOKEN_KEY, token),
    clear: () => localStorage.removeItem(TOKEN_KEY),
};

const api = axios.create({
    baseURL: '/api/v1',
    headers: { Accept: 'application/json' },
});

api.interceptors.request.use((config) => {
    const token = tokenStorage.get();
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401 && tokenStorage.get()) {
            tokenStorage.clear();
            window.location.href = '/login';
        }
        return Promise.reject(error);
    },
);

export function errorMessage(error, fallback = "Xatolik yuz berdi. Qaytadan urinib ko'ring.") {
    const data = error?.response?.data;
    if (data?.errors) return Object.values(data.errors).flat()[0];
    return data?.message || fallback;
}

export function validationErrors(error) {
    const errors = error?.response?.data?.errors || {};
    return Object.fromEntries(Object.entries(errors).map(([key, value]) => [key, value[0]]));
}

export default api;
