<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';
import { useMetaStore } from '../stores/meta';
import VacancyCard from '../components/VacancyCard.vue';
import Pagination from '../components/Pagination.vue';
import EmptyState from '../components/EmptyState.vue';
import Spinner from '../components/Spinner.vue';
import SearchInput from '../components/SearchInput.vue';

const route = useRoute();
const router = useRouter();
const meta = useMetaStore();
const keys = ['q', 'city', 'category', 'employment_type', 'experience', 'salary_min', 'sort'];
const filters = reactive(Object.fromEntries(keys.map((k) => [k, route.query[k] ?? ''])));
const vacancies = ref([]);
const pagination = ref(null);
const loading = ref(true);
const firstLoad = ref(true);
const showFilters = ref(false);
const activeCount = computed(() => keys.filter((k) => k !== 'q' && k !== 'sort' && filters[k] !== '').length);
let requestId = 0;

async function load(page = Number(route.query.page) || 1) {
    const id = ++requestId;
    loading.value = true;
    const params = Object.fromEntries(Object.entries(filters).filter(([, v]) => v !== ''));
    router.replace({ query: { ...params, ...(page > 1 && { page }) } });
    try {
        const { data } = await api.get('/vacancies', { params: { ...params, page } });
        if (id !== requestId) return;
        vacancies.value = data.data;
        pagination.value = data.meta;
    } finally {
        if (id === requestId) {
            loading.value = false;
            firstLoad.value = false;
        }
    }
}

function changePage(page) {
    load(page);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function reset() {
    keys.forEach((k) => (filters[k] = ''));
}

let timer;
watch(filters, () => {
    clearTimeout(timer);
    timer = setTimeout(() => load(1), 150);
});

onMounted(() => {
    meta.load();
    load();
});
</script>

<template>
    <div>
        <section class="relative overflow-hidden bg-gradient-to-r from-lapis-800 to-brand-700 text-white">
            <div class="bg-girih absolute inset-0 opacity-[0.08]"></div>
            <div class="relative mx-auto max-w-6xl px-4 pt-10 pb-16">
                <h1 class="font-display text-3xl font-bold">Vakansiyalar</h1>
                <p class="mt-1 text-brand-100">
                    <span class="font-semibold text-gold-300">{{ pagination?.total ?? 0 }}</span> ta vakansiya topildi
                </p>
            </div>
        </section>

        <div class="mx-auto -mt-9 max-w-6xl px-4">
            <div class="card relative flex flex-col gap-2 p-2 shadow-lg sm:flex-row">
                <SearchInput v-model="filters.q" class="flex-1" input-class="border-0 py-3 focus:ring-0" placeholder="Lavozim, kasb yoki ko'nikma — yozib bo'lgach o'zi qidiradi" />
                <select v-model="filters.sort" class="input border-0 bg-slate-50 py-3 focus:ring-0 sm:w-52">
                    <option value="">Eng yangilari</option>
                    <option value="salary">Maosh bo'yicha</option>
                </select>
                <button class="btn-ghost lg:hidden" @click="showFilters = !showFilters">
                    Filtrlar
                    <span v-if="activeCount" class="grid h-5 w-5 place-items-center rounded-full bg-brand-600 text-xs text-white">{{ activeCount }}</span>
                </button>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-[270px_1fr]">
                <aside class="card h-fit space-y-4 p-5 lg:sticky lg:top-24 lg:block" :class="showFilters ? 'block' : 'hidden'">
                    <div class="flex items-center justify-between">
                        <h2 class="font-bold text-lapis-900">Filtrlar</h2>
                        <button v-if="activeCount" class="text-xs font-semibold text-brand-600 hover:underline" @click="reset">Tozalash</button>
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
                            <option v-for="c in meta.categories" :key="c.id" :value="c.slug">{{ c.icon }} {{ c.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Ish turi</label>
                        <div class="flex flex-wrap gap-1.5">
                            <button
                                v-for="o in [{ value: '', label: 'Barchasi' }, ...(meta.employment_types ?? [])]"
                                :key="o.value"
                                type="button"
                                class="rounded-full border px-3 py-1.5 text-xs font-semibold transition"
                                :class="filters.employment_type === o.value ? 'border-brand-600 bg-brand-600 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-brand-300'"
                                @click="filters.employment_type = o.value"
                            >
                                {{ o.label }}
                            </button>
                        </div>
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
                        <select v-model="filters.salary_min" class="input">
                            <option value="">Farqi yo'q</option>
                            <option v-for="s in [3000000, 5000000, 8000000, 12000000, 20000000]" :key="s" :value="String(s)">{{ (s / 1000000).toLocaleString('uz') }} mln so'mdan</option>
                        </select>
                    </div>
                </aside>

                <div class="relative">
                    <Spinner v-if="firstLoad" />
                    <EmptyState v-else-if="!vacancies.length" title="Vakansiya topilmadi" text="Boshqa so'z bilan qidirib ko'ring yoki filtrlarni o'zgartiring.">
                        <button v-if="activeCount || filters.q" class="btn-ghost" @click="reset">Filtrlarni tozalash</button>
                    </EmptyState>
                    <template v-else>
                        <div class="grid gap-4 transition-opacity md:grid-cols-2" :class="loading && 'pointer-events-none opacity-50'">
                            <VacancyCard v-for="v in vacancies" :key="v.id" :vacancy="v" />
                        </div>
                        <Pagination :meta="pagination" @change="changePage" />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
