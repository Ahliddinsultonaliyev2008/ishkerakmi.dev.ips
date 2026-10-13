import { defineStore } from 'pinia';
import api, { tokenStorage } from '../api';

export const useAuthStore = defineStore('auth', {
    state: () => ({ user: null, loaded: false }),
    getters: {
        isLoggedIn: (state) => !!state.user,
        isEmployer: (state) => state.user?.role?.value === 'employer',
        isSeeker: (state) => state.user?.role?.value === 'seeker',
    },
    actions: {
        async init() {
            if (this.loaded) return;
            if (tokenStorage.get()) {
                try {
                    const { data } = await api.get('/auth/me');
                    this.user = data.data;
                } catch {
                    tokenStorage.clear();
                }
            }
            this.loaded = true;
        },
        setSession({ token, user }) {
            tokenStorage.set(token);
            this.user = user;
        },
        async login(payload) {
            const { data } = await api.post('/auth/login', payload);
            this.setSession(data);
        },
        async register(payload) {
            const { data } = await api.post('/auth/register', payload);
            this.setSession(data);
        },
        async logout() {
            try {
                await api.post('/auth/logout');
            } finally {
                tokenStorage.clear();
                this.user = null;
            }
        },
    },
});
