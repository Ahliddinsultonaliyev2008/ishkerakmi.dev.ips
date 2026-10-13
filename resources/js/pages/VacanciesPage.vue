<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';
import { useMetaStore } from '../stores/meta';
import VacancyCard from '../components/VacancyCard.vue';
import Pagination from '../components/Pagination.vue';
import EmptyState from '../components/EmptyState.vue';
import Spinner from '../components/Spinner.vue';

const route = useRoute();
const router = useRouter();
const meta = useMetaStore();
const keys = ['q', 'city', 'category', 'employment_type', 'experience', 'salary_min', 'sort'];
const filters = reactive(Object.fromEntries(keys.map((k) => [k, route.query[k] ?? ''])));
const vacancies = ref([]);
const pagination = ref(null);
const loading = ref(true);

async function load(page = Number(route.query.page) || 1) {
    loading.value = true;
    const params = Object.fromEntries(Object.entries(filters).filter(([, v]) => v !== ''));
    router.replace({ query: { ...params, ...(page > 1 && { page }) } });
    const { data } = await api.get('/vacancies', { params: { ...params, page } });
    vacancies.value = data.data;
    pagination.value = data.meta;
    loading.value = false;
}

function reset() {
    keys.forEach((k) => (filters[k] = ''));
}

let timer;
watch(filters, () => {
    clearTimeout(timer);
    timer = setTimeout(() => load(1), 300);
});

onMounted(() => {
    meta.load();
    load();
});
</script>

<template>
    <div class="mx-auto max-w-6xl px-4 py-10">
        <h1 class="text-3xl font-bold text-slate-900">Vakansiyalar</h1>
        <p class="mt-1 text-slate-500">{{ pagination?.total ?? 0 }} ta vakansiya topildi</p>

        <div class="mt-6 grid gap-6 lg:grid-cols-[280px_1fr]">
            <aside class="card h-fit space-y-4 p-5 lg:sticky lg:top-20">
                <div>
                    <label class="label">Qidiruv</label>
                    <input v-model="filters.q" class="input" placeholder="Masalan: dasturchi" />
                </div>
                <div>
                    <label class="label">Shahar</label>
                    <select v-model="filters.city" class="input">
                        <option value="">Barchasi</option>
                        <option v-for="c in meta.cities" :key="c" :value="c">{{ c }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">Kategoriya</label>
                    <select v-model="filters.category" class="input">
                        <option value="">Barchasi</option>
                        <option v-for="c in meta.categories" :key="c.id" :value="c.slug">{{ c.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">Ish turi</label>
                    <select v-model="filters.employment_type" class="input">
                        <option value="">Barchasi</option>
                        <option v-for="o in meta.employment_types" :key="o.value" :value="o.value">{{ o.label }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">Tajriba</label>
                    <select v-model="filters.experience" class="input">
                        <option value="">Farqi yo'q</option>
                        <option v-for="o in meta.experiences" :key="o.value" :value="o.value">{{ o.label }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">Maosh (dan, so'm)</label>
                    <input v-model="filters.salary_min" type="number" min="0" step="500000" class="input" placeholder="5 000 000" />
                </div>
                <div>
                    <label class="label">Saralash</label>
                    <select v-model="filters.sort" class="input">
                        <option value="">Eng yangilari</option>
                        <option value="salary">Maosh bo'yicha</option>
                    </select>
                </div>
                <button class="btn-ghost w-full" @click="reset">Filtrlarni tozalash</button>
            </aside>

            <div>
                <Spinner v-if="loading" />
                <EmptyState v-else-if="!vacancies.length" title="Vakansiya topilmadi" text="Filtrlarni o'zgartirib ko'ring." />
                <template v-else>
                    <div class="grid gap-4 md:grid-cols-2">
                        <VacancyCard v-for="v in vacancies" :key="v.id" :vacancy="v" />
                    </div>
                    <Pagination :meta="pagination" @change="load" />
                </template>
            </div>
        </div>
    </div>
</template>
