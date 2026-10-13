<script setup>
import { onMounted, ref, watch } from 'vue';
import api from '../api';
import CompanyLogo from '../components/CompanyLogo.vue';
import Pagination from '../components/Pagination.vue';
import Spinner from '../components/Spinner.vue';
import EmptyState from '../components/EmptyState.vue';
import SearchInput from '../components/SearchInput.vue';

const companies = ref([]);
const pagination = ref(null);
const q = ref('');
const loading = ref(true);

async function load(page = 1) {
    loading.value = true;
    const { data } = await api.get('/companies', { params: { q: q.value || undefined, page } });
    companies.value = data.data;
    pagination.value = data.meta;
    loading.value = false;
}

watch(q, () => load(1));
onMounted(load);
</script>

<template>
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="font-display text-3xl font-bold text-lapis-900">Kompaniyalar</h1>
                <p class="mt-1 text-slate-500">Ish beruvchilar bilan tanishing</p>
            </div>
            <SearchInput v-model="q" class="md:w-80" placeholder="Kompaniya nomi" />
        </div>
        <Spinner v-if="loading" />
        <EmptyState v-else-if="!companies.length" title="Kompaniya topilmadi" />
        <template v-else>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <RouterLink v-for="c in companies" :key="c.id" :to="`/companies/${c.id}`" class="card p-5 transition hover:border-brand-200 hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <CompanyLogo :company="c" />
                        <div class="min-w-0">
                            <div class="truncate font-semibold text-slate-900">{{ c.name }} <span v-if="c.is_verified" class="text-brand-500">✔</span></div>
                            <div class="text-sm text-slate-500">{{ c.industry }} · {{ c.city }}</div>
                        </div>
                    </div>
                    <p class="mt-3 line-clamp-2 text-sm text-slate-600">{{ c.description }}</p>
                    <div class="mt-3 text-sm font-semibold text-brand-600">{{ c.vacancies_count }} ta faol vakansiya</div>
                </RouterLink>
            </div>
            <Pagination :meta="pagination" @change="load" />
        </template>
    </div>
</template>
