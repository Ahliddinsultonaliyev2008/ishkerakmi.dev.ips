<script setup>
import { onMounted, ref } from 'vue';
import api from '../api';
import VacancyCard from '../components/VacancyCard.vue';
import Spinner from '../components/Spinner.vue';
import EmptyState from '../components/EmptyState.vue';

const vacancies = ref(null);
onMounted(async () => {
    vacancies.value = (await api.get('/me/saved', { params: { per_page: 50 } })).data.data;
});
</script>

<template>
    <div class="mx-auto max-w-6xl px-4 py-10">
        <h1 class="text-3xl font-bold text-slate-900">Saqlangan vakansiyalar</h1>
        <Spinner v-if="!vacancies" />
        <EmptyState v-else-if="!vacancies.length" class="mt-6" icon="❤️" title="Hali hech narsa saqlamagansiz" text="Vakansiyadagi yurakcha tugmasini bosib saqlang.">
            <RouterLink to="/vacancies" class="btn-primary">Vakansiyalarni ko'rish</RouterLink>
        </EmptyState>
        <div v-else class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <VacancyCard v-for="v in vacancies" :key="v.id" :vacancy="v" />
        </div>
    </div>
</template>
