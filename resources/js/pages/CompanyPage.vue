<script setup>
import { onMounted, ref } from 'vue';
import api from '../api';
import CompanyLogo from '../components/CompanyLogo.vue';
import VacancyCard from '../components/VacancyCard.vue';
import Spinner from '../components/Spinner.vue';
import EmptyState from '../components/EmptyState.vue';

const props = defineProps({ id: String });
const company = ref(null);
const vacancies = ref([]);

onMounted(async () => {
    const [c, v] = await Promise.all([api.get(`/companies/${props.id}`), api.get('/vacancies', { params: { company: props.id, per_page: 50 } })]);
    company.value = c.data.data;
    vacancies.value = v.data.data;
});
</script>

<template>
    <div class="mx-auto max-w-6xl px-4 py-10">
        <Spinner v-if="!company" />
        <template v-else>
            <section class="card p-6 md:p-8">
                <div class="flex flex-col gap-5 md:flex-row md:items-center">
                    <CompanyLogo :company="company" size="h-20 w-20 text-2xl" />
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-slate-900">{{ company.name }} <span v-if="company.is_verified" class="text-base text-brand-500">✔ Tasdiqlangan</span></h1>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span v-if="company.industry" class="chip">🏢 {{ company.industry }}</span>
                            <span v-if="company.city" class="chip">📍 {{ company.city }}</span>
                            <span v-if="company.employees_count" class="chip">👥 {{ company.employees_count }} xodim</span>
                            <a v-if="company.website" :href="company.website" target="_blank" rel="noopener" class="chip text-brand-600">🌐 Veb-sayt</a>
                        </div>
                    </div>
                </div>
                <p v-if="company.description" class="mt-6 whitespace-pre-line text-slate-700">{{ company.description }}</p>
            </section>
            <h2 class="mt-10 text-xl font-bold text-slate-900">Faol vakansiyalar ({{ vacancies.length }})</h2>
            <EmptyState v-if="!vacancies.length" class="mt-4" title="Hozircha vakansiya yo'q" icon="📭" />
            <div v-else class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <VacancyCard v-for="v in vacancies" :key="v.id" :vacancy="v" />
            </div>
        </template>
    </div>
</template>
