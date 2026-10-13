import { defineStore } from 'pinia';
import api from '../api';

export const useMetaStore = defineStore('meta', {
    state: () => ({
        cities: [],
        categories: [],
        employment_types: [],
        experiences: [],
        currencies: [],
        application_statuses: [],
        stats: {},
        loaded: false,
    }),
    actions: {
        async load(force = false) {
            if (this.loaded && !force) return;
            const { data } = await api.get('/meta');
            this.$patch({ ...data, loaded: true });
        },
    },
});
